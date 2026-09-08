# Архитектура

*[English version further down.](#architecture-en)*

## Почему проект выглядит именно так

Shorter намеренно переусложнён архитектурно для того, что он делает. Для сокращения ссылок не нужны
четыре слоя, шина сообщений или принудительное соблюдение направления зависимостей — но смысл
этого проекта в демонстрации гексагонального/DDD + CQRS подхода.

## Бэкенд: слои

Бэкенд разделён на четыре верхнеуровневых пространства имён под `src/`, и `deptrac.yaml`
принудительно соблюдает направление зависимостей между ними — сборка падает, если один слой лезет
в тот, в который ему нельзя:

```
UserInterface  ─┐
                 ├──▶  Application  ──▶  Domain
Infrastructure ─┘                          ▲
                 └──────────────────────────┘
```

| Слой | Зависит от | Содержит |
|---|---|---|
| `Domain` | ничего | Сущности, *интерфейсы* репозиториев, доменные команды/запросы и их обработчики, доменные исключения |
| `Application` | `Domain` | UseCase'ы (валидируемые DTO входных данных), обработчики UseCase'ов, View/ViewFactory (формы ответов API), классы атрибутов OpenAPI |
| `Infrastructure` | `Domain`, `Application` | Реализации репозиториев на Doctrine, реализация шины сообщений, генератор коротких кодов |
| `UserInterface` | `Domain`, `Application` | HTTP Actions (контроллеры) |

`Domain` не имеет никаких внешних зависимостей — он не знает, что существуют Symfony, Doctrine
или HTTP. Именно это делает его быстрым для юнит-тестирования (см. [Тестирование](#тестирование)
ниже) и, в принципе, переносимым на другой фреймворк.

## Шина сообщений CQRS: двойной диспатч

От контроллера к бизнес-логике нет прямого пути через вызов метода — каждая запись и чтение идут
через Symfony Messenger, причём идут через него **дважды**. На конкретном примере `CreateUrl`:

1. **`UserInterface\Http\Api\Url\CreateUrlAction`** читает тело запроса, собирает
   `Application\Url\UseCases\CreateUrlUseCase` (обычный DTO с валидационными атрибутами
   `Assert\*`), сам генерирует `id` сущности (см. заметку ниже) и диспатчит его через
   `CommandBusInterface`.
2. **`Application\Url\UseCases\Handlers\CreateUrlUseCaseHandler`** принимает его, валидирует,
   собирает `Domain\Url\TransferObjects\NewUrlTransferObject` (через
   `NewUrlTransferObjectFactory`, который заодно генерирует уникальный короткий код), заворачивает
   в `Domain\Url\Commands\CreateUrlCommand` и диспатчит **его** через ту же самую
   `CommandBusInterface`.
3. **`Domain\Url\Commands\Handlers\CreateUrlCommandHandler`** — реальный конечный обработчик —
   общается только с `Domain\Url\Repositories\UrlRepositoryInterface`, чтобы сохранить сущность.
   Он ничего не знает о существовании HTTP-запроса или DTO UseCase'а.

Та же схема применяется и к чтению (`GetUrlByCodeUseCase` → `GetUrlByCodeQuery`) через
`QueryBusInterface`.

**Почему двойной диспатч, а не прямой вызов доменного обработчика из UseCase-хэндлера?** Разделяет логику на слои.
Domain слой отвечает только за персистентность данных, Application - за бизнесс-логику, например как сокращение URL.

**Почему `id` генерируется в Action, а не в хэндлере?** Строгое разделенеи на CommandBus и QueryBus. 
CommandBus - операции над данными: вызов сторонних апи, мутация данных, публикация события и т.д. QueryBus - получение данных.
Action генерирует id, с которым создаёт сущность, а после получает эту сущность, чтобы показать пользователю. 
Так целая вертикаль отвечает или только за создание или только за отображение.


## Асинхронная обработка: учёт кликов

Запись клика никогда не должна замедлять или рисковать сломать сам редирект. Поэтому
`Application\Click\UseCases\RecordClickUseCase` маршрутизируется по классу сообщения на
Messenger-транспорт `doctrine://` (`config/packages/messenger.yaml`) вместо выполнения в потоке
запроса:

```
RedirectAction ──dispatch──▶ RecordClickUseCase ──(роутится на async-транспорт)──▶ таблица messenger_messages
                                                                                        │
                                                          контейнер worker (messenger:consume async)
```

Отдельный контейнер `worker` (тот же PHP-образ, что и `php`, но с другой командой — см.
`docker/docker-compose.yml`) непрерывно выполняет `messenger:consume async` и разбирает очередь.
Роутинг настроен по **классу сообщения**, а не по шине, так что это единственное место во всём
приложении, работающее асинхронно — всё остальное по умолчанию синхронно. В тестовом окружении
`when@test` в `messenger.yaml` подменяет транспорт на `sync://`, так что тесты никогда не зависят
от того, жив ли воркер.

## Роутинг: два хоста, одна кодовая база

API и публичный редирект/фронтенд живут на разных хостах — так же, как это выглядело бы при
реальном деплое подобного сервиса (редирект-сервис должен быть неотличим от любой другой
веб-страницы; API не должен быть перемешан с ней):

- **`api.shorter.localhost`** — только JSON API (`CreateUrlAction`, `GetLinkStatsAction`).
- **`shorter.localhost`** — редирект (`RedirectAction`) и фронтенд-SPA, на одном и том же хосте и
  порту.

`config/routes.yaml` в Symfony принудительно разделяет их через ограничения `host:` для каждой
группы маршрутов. Nginx делает то же самое ещё на уровень выше, и дополнительно разделяет сам
`shorter.localhost`: путь, точно совпадающий с 10-символьным буквенно-цифровым кодом
(`^/[A-Za-z0-9]{10}$` — фиксированная форма, которую генерирует `RandomShortCodeGenerator`), уходит
в PHP на редирект; всё остальное проксируется на dev-сервер Vite. Именно эта фиксированная длина
кода делает разделение однозначным без риска пересечения этих двух зон ответственности.

## Документация API

OpenAPI 3 документация генерируется из атрибутов кода (`OpenApi\Attributes` пакета
`zircote/swagger-php`) на DTO UseCase'ов, классах View и Actions, и отдаётся
**NelmioApiDocBundle v5** по адресу `/api/doc` (Swagger UI) и `/api/doc.json` (сырой документ). Там,
где имя PHP-свойства DTO не совпадает с форматом на проводе (например,
`CreateUrlUseCase::$targetUrl` против реального JSON-ключа `target_url`), инлайновый
`#[OA\Property(property: 'target_url', ...)]` переопределяет дефолтное именование по reflection.
Поля, которые существуют в DTO, но никогда не должны задаваться клиентом (как
`CreateUrlUseCase::$id`, заполняемое на сервере), исключаются из сгенерированной схемы через
`#[Ignore]` из Symfony Serializer.

Кросс-доменные запросы с фронтенда (`shorter.localhost`, обращающийся к `api.shorter.localhost`)
разрешены через `nelmio/cors-bundle`.

## Тестирование

Четыре тестсьюта PHPUnit зеркалят четыре слоя, и каждый слой тестируется на том уровне, который
для него имеет смысл:

| Сьют | Что тестирует | Как |
|---|---|---|
| `Domain` | Сущности, обработчики доменных команд/запросов, фабрику transfer-объектов | Изолированно, с помощью `tests/Builders` (fluent-билдеры ORM-сущностей) и написанных вручную in-memory реализаций репозиториев (`Infrastructure\Persistence\Repositories\*\InMemoryXRepository`) — без базы данных |
| `Application` | Обработчики UseCase'ов, фабрики View | Тот же in-memory подход, плюс классы `Assertion`, проверяющие, что сохранённый transfer-объект совпадает с тем, что было передано |
| `Infrastructure` | Генератор коротких кодов | Обычные юнит-тесты |
| `UserInterface` | HTTP Actions | Полноценные acceptance-тесты через тестовый `KernelBrowser` Symfony (`AcceptanceTestCase` → `ApiTestCase`/`PublicTestCase`, которые выставляют нужный заголовок `Host` для каждого домена), бьющие в реальную (тестовую) базу данных |

Каждый тест — включая acceptance — выполняется внутри транзакции, которую откатывает
**DAMADoctrineTestBundle**, так что сьют никогда не мутирует базу, на которой выполняется, вне
зависимости от того, какой слой он тестирует.

## Статический анализ и качество кода

- **PHPStan, level 8** (с `phpstan-symfony` для анализа, учитывающего контейнер).
- **Deptrac** — принудительно соблюдает границы слоёв, описанные выше; см. `deptrac.yaml`.
- **GrumPHP** — запускает `composer normalize`, PHPStan и Deptrac как pre-commit хук
  (`grumphp.yml`), нарушения ловятся ещё до коммита.
- **GitHub Actions** (`.github/workflows/backend-ci.yml`, `frontend-ci.yml`) — те же проверки,
  плюс полный набор PHPUnit и сборка фронтенда, запускаются на каждый pull request.

## Фронтенд

React 18 + TypeScript на Vite, общается с API через **Redux Toolkit Query (RTK Query)**.

### API-слой: папка на эндпоинт, явная (де)нормализация

У каждого эндпоинта своя папка под `web/src/api/`, подключаемая к единому пустому базовому API
(`shorterApi.ts`) через `injectEndpoints` — паттерн, который сам RTK Query рекомендует для
разделения эндпоинтов по файлам без потери типизации:

```
api/
  shorterApi.ts                 – пустая база: baseQuery, tagTypes
  Url/
    Types.ts                    – UrlViewObjectInterface (snake_case, как в JSON на проводе)
                                   / UrlInterface (camelCase, с чем работают компоненты)
    Denormalizers.ts             – denormalizeUrl, общий для всех эндпоинтов, возвращающих Url
    CreateUrl/
      Types.ts, Normalizers.ts   – camelCase payload → snake_case тело запроса
      index.ts                   – сам подключённый эндпоинт и его сгенерированные хуки
    GetLinkStats/
      Types.ts, Denormalizers.ts
      index.ts
  Error/
    Types.ts, Denormalizers.ts   – превращает формат бэкенда { message, violations } в
                                   camelCase-ключи violations, которые компоненты могут
                                   напрямую сопоставлять со своими собственными полями
```

Компоненты никогда не видят snake_case: конвертация происходит один раз, на границе, в
`transformResponse` каждого эндпоинта (или явно в точке использования — для ошибок, поскольку
возвращаемый тип `transformErrorResponse` у RTK Query не отражается в TypeScript-типе хука,
известное ограничение библиотеки, так что денормализация ошибки вызывается прямо там, где ошибка
используется).

### UI

- **MUI v6** для компонентов, с небольшой кастомной темой (`theme.ts`).
- **CSS Modules** (`Component.module.scss`), лежащие рядом с каждым компонентом, названные по
  имени компонента, а не `index` — импорт `styles` на компонент, без глобальных стилей.
- Без клиентского роутинга: всё приложение — одна страница с двумя табами (Create / Stats), оба
  всегда примонтированы (видимость переключается через атрибут `hidden`), так что переключение
  табов не сбрасывает введённые данные или результаты.
- Без списка ссылок на фронтенде: API отдаёт только *создание* и *статистику по коду* — эндпоинта
  "список всех ссылок" нет по замыслу, поэтому фронтенд и не притворяется, что его хранит.

## Полный стек технологий

| Слой | Технология |
|---|---|
| Язык / рантайм | PHP 8.3 |
| Фреймворк | Symfony 7.2 |
| Хранение данных | Doctrine ORM 3 + Doctrine Migrations, MySQL 8.0 |
| Сообщения | Symfony Messenger (Doctrine-транспорт для async) |
| Документация API | NelmioApiDocBundle 5, zircote/swagger-php |
| CORS | NelmioCorsBundle |
| Тестирование | PHPUnit 11, DAMADoctrineTestBundle, doctrine/doctrine-fixtures-bundle, fakerphp/faker |
| Статический анализ | PHPStan (level 8) + phpstan-symfony, Deptrac, GrumPHP, ergebnis/composer-normalize |
| Фронтенд | React 18, TypeScript, Vite 5 |
| Стейт/данные фронтенда | Redux Toolkit + RTK Query |
| UI фронтенда | MUI 6, Sass (CSS Modules), Fontsource (самохостящийся Inter) |
| Инфраструктура | Docker Compose (nginx, php-fpm, mysql, worker, node) |
| CI | GitHub Actions |

---

<a name="architecture-en"></a>
# Architecture (English)

## Why this project looks the way it does

Shorter is deliberately over-architected for what it does. Shortening links doesn't need four
layers, a message bus, or dependency-direction enforcement — but the point of this project is
demonstrating the hexagonal/DDD + CQRS approach.

## Backend: layers

The backend is split into four top-level namespaces under `src/`, and `deptrac.yaml` enforces the
dependency direction between them — a build fails if a layer reaches into one it isn't allowed to:

```
UserInterface  ─┐
                 ├──▶  Application  ──▶  Domain
Infrastructure ─┘                          ▲
                 └──────────────────────────┘
```

| Layer | Depends on | Contains |
|---|---|---|
| `Domain` | nothing | Entities, repository *interfaces*, domain commands/queries and their handlers, domain exceptions |
| `Application` | `Domain` | UseCases (validated input DTOs), UseCase handlers, Views/ViewFactories (API response shapes), OpenAPI attribute classes |
| `Infrastructure` | `Domain`, `Application` | Doctrine repository implementations, the message bus implementation, the short-code generator |
| `UserInterface` | `Domain`, `Application` | HTTP Actions (controllers) |

`Domain` has zero outward dependencies — it doesn't know Symfony, Doctrine, or HTTP exist. That's
what makes it fast to unit-test (see [Testing](#testing) below) and, in principle, portable to a
different framework.

## The CQRS message bus: double dispatch

There's no direct method-call path from a controller to business logic — every write and read goes
through Symfony Messenger, and it goes through it **twice**. Using `CreateUrl` as the concrete
example:

1. **`UserInterface\Http\Api\Url\CreateUrlAction`** reads the request body, builds an
   `Application\Url\UseCases\CreateUrlUseCase` (a plain DTO with `Assert\*` validation
   constraints), generates the entity's `id` itself (see the note below), and dispatches it on
   `CommandBusInterface`.
2. **`Application\Url\UseCases\Handlers\CreateUrlUseCaseHandler`** receives it, validates it,
   builds a `Domain\Url\TransferObjects\NewUrlTransferObject` (via
   `NewUrlTransferObjectFactory`, which also generates the unique short code), wraps it in a
   `Domain\Url\Commands\CreateUrlCommand`, and dispatches **that** on the same `CommandBusInterface`.
3. **`Domain\Url\Commands\Handlers\CreateUrlCommandHandler`** — the actual leaf handler — talks
   only to `Domain\Url\Repositories\UrlRepositoryInterface` to persist the entity. It has no idea
   an HTTP request or a UseCase DTO exists.

The same shape applies to reads (`GetUrlByCodeUseCase` → `GetUrlByCodeQuery`) via
`QueryBusInterface`.

**Why double-dispatch instead of the UseCase handler just calling the Domain handler directly?**
It separates the logic by layer: the Domain layer is only responsible for persisting data, while
Application holds the business logic — like shortening a URL.

**Why generate `id` in the Action, not in a handler?** A strict split between `CommandBus` and
`QueryBus`. `CommandBus` is for operations on data: calling third-party APIs, mutating data,
publishing an event, and so on. `QueryBus` is for reading data. The Action generates the `id` it
creates the entity with, then fetches that same entity back to show it to the user — so the whole
vertical is responsible for either creation or display, never both.

## Async processing: click tracking

Recording a click must never slow down or risk failing the redirect itself. So
`Application\Click\UseCases\RecordClickUseCase` is routed, by message class, to a `doctrine://`
Messenger transport (`config/packages/messenger.yaml`) instead of running inline:

```
RedirectAction ──dispatch──▶ RecordClickUseCase ──(routed to async transport)──▶ messenger_messages table
                                                                                        │
                                                          worker container (messenger:consume async)
```

A dedicated `worker` container (same PHP image as `php`, different command — see
`docker/docker-compose.yml`) runs `messenger:consume async` continuously and processes the queue.
Routing is per **message class**, not per bus, so this is the only thing in the whole app that
runs asynchronously — everything else stays synchronous by default. In the test environment,
`when@test` in `messenger.yaml` swaps the transport for `sync://`, so tests never depend on a
worker being alive.

## Routing: two hosts, one codebase

The API and the public-facing redirect/frontend live on separate hosts, matching how you'd
actually deploy something like this (a redirect service should be indistinguishable from any other
web page; an API shouldn't be):

- **`api.shorter.localhost`** — JSON API only (`CreateUrlAction`, `GetLinkStatsAction`).
- **`shorter.localhost`** — the redirect (`RedirectAction`) and the frontend SPA, sharing the same
  host and port.

Symfony's `config/routes.yaml` enforces the split via `host:` constraints per route group. Nginx
enforces it too, one level up, and additionally splits `shorter.localhost` itself: a request path
matching exactly a 10-character alphanumeric code (`^/[A-Za-z0-9]{10}$` — the fixed shape produced
by `RandomShortCodeGenerator`) goes to PHP for the redirect; everything else is proxied to the
Vite dev server. That fixed-length code is what makes the split unambiguous without the two
concerns ever colliding.

## API documentation

OpenAPI 3 documentation is generated from code attributes (`zircote/swagger-php`'s
`OpenApi\Attributes`) on the UseCase DTOs, View classes, and Actions, served by
**NelmioApiDocBundle v5** at `/api/doc` (Swagger UI) and `/api/doc.json` (raw document). Where a
DTO's PHP property name doesn't match the wire format (e.g. `CreateUrlUseCase::$targetUrl` vs. the
actual `target_url` JSON key), an inline `#[OA\Property(property: 'target_url', ...)]` overrides
the default reflection-based naming. Fields that exist on the DTO but must never be settable from
the client (like `CreateUrlUseCase::$id`, populated server-side) are excluded from the generated
schema with Symfony Serializer's `#[Ignore]`.

Cross-origin requests from the frontend (`shorter.localhost` calling `api.shorter.localhost`) are
allowed via `nelmio/cors-bundle`.

## Testing

Four PHPUnit testsuites mirror the four layers, and each layer is tested at the level that makes
sense for it:

| Suite | What it tests | How |
|---|---|---|
| `Domain` | Entities, domain command/query handlers, the transfer-object factory | In isolation, using `tests/Builders` (fluent ORM entity builders) and hand-written in-memory repository implementations (`Infrastructure\Persistence\Repositories\*\InMemoryXRepository`) — no database involved |
| `Application` | UseCase handlers, View factories | Same in-memory approach, plus `Assertion` helper classes that check a persisted transfer object matches what was passed in |
| `Infrastructure` | The short-code generator | Plain unit tests |
| `UserInterface` | HTTP Actions | Full acceptance tests through Symfony's test `KernelBrowser` (`AcceptanceTestCase` → `ApiTestCase`/`PublicTestCase`, which set the right `Host` header for each domain), hitting a real (test) database |

Every test — acceptance included — runs inside a transaction that **DAMADoctrineTestBundle**
rolls back afterwards, so the suite never mutates the database it runs against, no matter which
layer it's testing.

## Static analysis & code quality

- **PHPStan, level 8** (with `phpstan-symfony` for container-aware analysis).
- **Deptrac** — enforces the layer boundaries described above; see `deptrac.yaml`.
- **GrumPHP** — runs `composer normalize`, PHPStan, and Deptrac as a pre-commit hook
  (`grumphp.yml`), so violations are caught before they're even committed.
- **GitHub Actions** (`.github/workflows/backend-ci.yml`, `frontend-ci.yml`) — the same checks,
  plus the full PHPUnit suite and the frontend build, run on every pull request.

## Frontend

React 18 + TypeScript on Vite, talking to the API through **Redux Toolkit Query (RTK Query)**.

### API layer: per-endpoint folders, explicit (de)normalization

Each endpoint gets its own folder under `web/src/api/`, injected into a single empty base API
(`shorterApi.ts`) via `injectEndpoints` — the pattern RTK Query itself recommends for splitting
endpoints across files without losing type inference:

```
api/
  shorterApi.ts                 – empty base: baseQuery, tagTypes
  Url/
    Types.ts                    – UrlViewObjectInterface (snake_case, matches the JSON wire format)
                                   / UrlInterface (camelCase, what components use)
    Denormalizers.ts             – denormalizeUrl, shared by every endpoint that returns a Url
    CreateUrl/
      Types.ts, Normalizers.ts   – camelCase payload → snake_case request body
      index.ts                   – the injected endpoint + its generated hooks
    GetLinkStats/
      Types.ts, Denormalizers.ts
      index.ts
  Error/
    Types.ts, Denormalizers.ts   – turns the backend's { message, violations } shape into
                                   camelCase-keyed violations components can match against
                                   their own field names directly
```

Components never see snake_case: the conversion happens once, at the boundary, in each endpoint's
`transformResponse` (or explicitly at the point of use, for errors — RTK Query's
`transformErrorResponse` return type isn't reflected in the hook's TypeScript type, a known library
limitation, so error denormalization is called directly where the error is consumed instead).

### UI

- **MUI v6** for components, with a small custom theme (`theme.ts`).
- **CSS Modules** (`Component.module.scss`) co-located with each component, named after the
  component rather than `index` — a `styles` import per component, no global stylesheet.
- No client-side routing: the whole app is one page with two tabs (Create / Stats), both kept
  mounted at all times (visibility toggled with the `hidden` attribute) so switching tabs doesn't
  reset in-progress input or results.
- No frontend link list: the API only exposes *create* and *get-stats-by-code* — there's no
  "list all links" endpoint by design, so the frontend never pretends to persist one either.

## Full tech stack

| Layer | Technology |
|---|---|
| Language / runtime | PHP 8.3 |
| Framework | Symfony 7.2 |
| Persistence | Doctrine ORM 3 + Doctrine Migrations, MySQL 8.0 |
| Messaging | Symfony Messenger (Doctrine transport for async) |
| API docs | NelmioApiDocBundle 5, zircote/swagger-php |
| CORS | NelmioCorsBundle |
| Testing | PHPUnit 11, DAMADoctrineTestBundle, doctrine/doctrine-fixtures-bundle, fakerphp/faker |
| Static analysis | PHPStan (level 8) + phpstan-symfony, Deptrac, GrumPHP, ergebnis/composer-normalize |
| Frontend | React 18, TypeScript, Vite 5 |
| Frontend state/data | Redux Toolkit + RTK Query |
| Frontend UI | MUI 6, Sass (CSS Modules), Fontsource (self-hosted Inter) |
| Infrastructure | Docker Compose (nginx, php-fpm, mysql, worker, node) |
| CI | GitHub Actions |
