# Установка и локальный запуск

*[English version further down.](#install-en)*

## Предварительные требования

- Docker Desktop (или другой Docker Engine + Compose v2).
- Правка hosts-файла не требуется: домены `*.localhost` (`shorter.localhost`,
  `api.shorter.localhost`) автоматически резолвятся в loopback-адрес в современных браузерах и ОС.

## 1. Настройка Docker-окружения

Compose-файл читает переменные из `docker/.env`. Скопировать пример:

```bash
cd docker
cp .env.example .env
```

Значения по умолчанию подходят сразу. При необходимости поправь:

| Переменная | Назначение |
|---|---|
| `XDEBUG_ON` / `XDEBUG_MODE` | Включить/выключить Xdebug в контейнерах `php`/`worker` |
| `MYSQL_HOST_PORT` | Порт на хосте, на который публикуется MySQL (внутри контейнера всегда 3306) |
| `MYSQL_DATABASE` / `MYSQL_USER` / `MYSQL_PASSWORD` / `MYSQL_ROOT_PASSWORD` | Локальные креды базы данных |

## 2. Секрет приложения (опционально для локальной разработки)

`APP_SECRET` в закоммиченном `.env` пустой. Для локальной разработки можно не добавлять:

```bash
echo "APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')" > .env.local
```

(выполнять из корня проекта, не из `docker/`)

## 3. Запуск стека

Из директории `docker/`:

```bash
docker compose up -d --build
```

Соберёт и запустит пять контейнеров:

| Сервис | Роль |
|---|---|
| `nginx` | Reverse-proxy — направляет `api.shorter.localhost` в PHP-FPM, а `shorter.localhost` делит между редиректом (PHP) и dev-сервером фронтенда (Vite) |
| `php` | PHP-FPM 8.3, обслуживает Symfony-приложение |
| `worker` | Тот же PHP-образ, запускает `messenger:consume async` — обрабатывает очередь учёта кликов |
| `mysql` | MySQL 8.0 |
| `node` | Запускает Vite dev-сервер фронтенда (при первом старте автоматически ставит зависимости) |

Первый старт контейнера `node` выполняет `yarn install`, это может занять пару минут — если
`shorter.localhost/` сразу после старта отдаёт `502`, смотреть `docker compose logs -f node`.

## 4. Установка PHP-зависимостей и настройка базы данных

```bash
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## 5. Открыть приложение

| URL | Что это |
|---|---|
| http://shorter.localhost/ | Фронтенд (создание ссылок, просмотр статистики) |
| http://shorter.localhost/\<код\> | Редирект на исходный URL |
| http://api.shorter.localhost/api/doc | Swagger UI (интерактивная документация API) |
| http://api.shorter.localhost/api/doc.json | Сырой OpenAPI 3 документ |

## Запуск тестов

```bash
docker compose exec php vendor/bin/phpunit
```


## Запуск линтеров / статического анализа

```bash
# Статический анализ PHP (level 8)
docker compose exec php vendor/bin/phpstan analyse

# Границы архитектурных слоёв
docker compose exec php vendor/bin/deptrac analyse

# Форматирование composer.json
docker compose exec php composer normalize --dry-run

# Фронтенд
docker compose exec node sh -c "cd web && yarn lint"
docker compose exec node sh -c "cd web && yarn build"   # заодно прогоняет тайпчек TypeScript
```

Все эти проверки (кроме сборки фронтенда) также запускаются автоматически через `grumphp.yml` как
pre-commit хук, и через GitHub Actions на каждый pull request
(`.github/workflows/backend-ci.yml`, `.github/workflows/frontend-ci.yml`).

## Troubleshooting

- **`shorter.localhost/` отдаёт 502**: контейнер `node` ещё ставит зависимости или запускает
  Vite — смотреть `docker compose logs -f node`.
- **Контейнеры не видят друг друга / DNS-ошибки сразу после `docker compose up`**: перезапустить
  `docker compose up -d` ещё раз.
- **Порты уже заняты**: что-то на хосте уже слушает `80` (nginx) или `3307` (порт MySQL на
  хосте) — остановить слушателя, либо поменять маппинг в `docker/.env`.

---

<a name="install-en"></a>
# Installation & local setup (English)

## Prerequisites

- Docker Desktop (or another Docker Engine + Compose v2 setup).
- No hosts-file editing is required: `*.localhost` domains (`shorter.localhost`,
  `api.shorter.localhost`) resolve to the loopback address automatically in modern browsers and
  operating systems.

## 1. Configure the Docker environment

The Compose file reads its variables from `docker/.env`. Copy the example file to get started:

```bash
cd docker
cp .env.example .env
```

The defaults work out of the box. Adjust them if you need to, in particular:

| Variable | Purpose |
|---|---|
| `XDEBUG_ON` / `XDEBUG_MODE` | Enable/disable Xdebug in the `php`/`worker` containers |
| `MYSQL_HOST_PORT` | Host port MySQL is published on (container always listens on 3306) |
| `MYSQL_DATABASE` / `MYSQL_USER` / `MYSQL_PASSWORD` / `MYSQL_ROOT_PASSWORD` | Local database credentials |

## 2. Application secret (optional for local dev)

Symfony's `APP_SECRET` ships empty in the committed `.env`. For local development, you can skip
this:

```bash
echo "APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')" > .env.local
```

(run from the project root, not `docker/`)

## 3. Start the stack

From the `docker/` directory:

```bash
docker compose up -d --build
```

This builds and starts five containers:

| Service | Role |
|---|---|
| `nginx` | Reverse proxy — routes `api.shorter.localhost` to PHP-FPM, and splits `shorter.localhost` between the redirect action (PHP) and the frontend dev server (Vite) |
| `php` | PHP-FPM 8.3, serves the Symfony application |
| `worker` | Same PHP image, runs `messenger:consume async` — processes queued click-tracking messages |
| `mysql` | MySQL 8.0 |
| `node` | Runs the Vite dev server for the frontend (installs dependencies automatically on first boot) |

The `node` container's first boot runs `yarn install`, which can take a minute or two — check
`docker compose logs -f node` if `shorter.localhost/` returns a `502` right after startup.

## 4. Install PHP dependencies and set up the database

```bash
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## 5. Open the app

| URL | What it is |
|---|---|
| http://shorter.localhost/ | The frontend (create links, view stats) |
| http://shorter.localhost/\<code\> | Redirects to the original URL |
| http://api.shorter.localhost/api/doc | Swagger UI (interactive API docs) |
| http://api.shorter.localhost/api/doc.json | Raw OpenAPI 3 document |

## Running tests

```bash
docker compose exec php vendor/bin/phpunit
```

## Running linters / static analysis

```bash
# PHP static analysis (level 8)
docker compose exec php vendor/bin/phpstan analyse

# Architectural layer boundaries
docker compose exec php vendor/bin/deptrac analyse

# composer.json formatting
docker compose exec php composer normalize --dry-run

# Frontend
docker compose exec node sh -c "cd web && yarn lint"
docker compose exec node sh -c "cd web && yarn build"   # also runs the TypeScript type-check
```

All of the above (except the frontend build) also run automatically via `grumphp.yml` as a
pre-commit hook, and via GitHub Actions on every pull request
(`.github/workflows/backend-ci.yml`, `.github/workflows/frontend-ci.yml`).

## Troubleshooting

- **`shorter.localhost/` returns 502**: the `node` container is still installing dependencies or
  starting Vite — check `docker compose logs -f node`.
- **Containers can't reach each other / DNS errors right after `docker compose up`**: re-run
  `docker compose up -d` once more.
- **Ports already in use**: something else on the host is bound to `80` (nginx) or `3307`
  (MySQL's host-mapped port) — stop it, or change the mapping in `docker/.env`.
