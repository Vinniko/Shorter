# Shorter

*[English version further down.](#shorter-en)*

Сокращение ссылок со статистикой переходов — портфолио-проект, созданный для демонстрации
гексагонального/DDD Symfony-бэкенда с шинами команд/запросов (CQRS), и React + TypeScript
фронтенда поверх него.

## Возможности

- Сократить любой URL в 10-символьный код.
- Перейти по короткой ссылке (`shorter.localhost/<код>`).
- Учёт кликов записывается асинхронно через очередь и воркер, а не в потоке самого запроса.
- Посмотреть статистику переходов по короткому коду.
- Интерактивная документация API (Swagger UI), сгенерированная из кода.
- Небольшой одностраничный фронтенд (табы Create / Stats) на RTK Query.

## Документация

- **[Установка и локальный запуск](docs/install.md)** — Docker Compose стек, настройка
  окружения, миграции, тесты и линтеры.
- **[Архитектура](docs/architecture.md)** — слои, паттерн CQRS/шина сообщений, асинхронная
  обработка, роутинг, стратегия тестирования и полный стек технологий.

## Стек технологий кратко

| | |
|---|---|
| Бэкенд | PHP 8.3, Symfony 7.2, Doctrine ORM + Migrations, Symfony Messenger |
| Документация API | NelmioApiDocBundle + zircote/swagger-php (OpenAPI 3) |
| База данных | MySQL 8.0 |
| Качество кода | PHPStan (level 8), Deptrac, GrumPHP, PHPUnit 11 |
| Фронтенд | React 18, TypeScript, Vite, Redux Toolkit Query, MUI |
| Инфраструктура | Docker Compose (php-fpm, nginx, mysql, worker, node), GitHub Actions CI |

Обоснование решений — в [docs/architecture.md](docs/architecture.md).

## Быстрый старт

```bash
cd docker
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

Затем можно открыть **http://shorter.localhost/** (фронтенд) и **http://api.shorter.localhost/api/doc**
(документацию API). Полные детали, включая переменные окружения и troubleshooting — в
[docs/install.md](docs/install.md).

## Лицензия

Proprietary — портфолио-проект, не предназначен для продакшена.

---

<a name="shorter-en"></a>
# Shorter (English)

A URL shortener with click analytics — a portfolio project built to demonstrate a hexagonal/DDD
Symfony backend with a CQRS message bus, and a React + TypeScript frontend on top of it.


## Features

- Shorten any URL into a 10-character code.
- Redirect through the short link (`shorter.localhost/<code>`).
- Click tracking recorded asynchronously via a message queue worker, not on the request thread.
- Look up click statistics for any short code.
- Interactive API documentation (Swagger UI) generated from the code.
- A small single-page frontend (Create / Stats tabs) built on RTK Query.

## Documentation

- **[Installation & local setup](docs/install.md)** — Docker Compose stack, environment
  configuration, running migrations, tests and linters.
- **[Architecture](docs/architecture.md)** — layers, the CQRS/message bus pattern, async
  processing, routing, testing strategy, and the full tech stack.

## Tech stack at a glance

| | |
|---|---|
| Backend | PHP 8.3, Symfony 7.2, Doctrine ORM + Migrations, Symfony Messenger |
| API docs | NelmioApiDocBundle + zircote/swagger-php (OpenAPI 3) |
| Database | MySQL 8.0 |
| Quality | PHPStan (level 8), Deptrac, GrumPHP, PHPUnit 11 |
| Frontend | React 18, TypeScript, Vite, Redux Toolkit Query, MUI |
| Infra | Docker Compose (php-fpm, nginx, mysql, worker, node), GitHub Actions CI |

See [docs/architecture.md](docs/architecture.md) for the reasoning behind these choices.

## Quick start

```bash
cd docker
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

Then open **http://shorter.localhost/** (frontend) and **http://api.shorter.localhost/api/doc**
(API docs). Full details, including environment variables and troubleshooting, are in
[docs/install.md](docs/install.md).

## License

Proprietary — portfolio project, not intended for production use.
