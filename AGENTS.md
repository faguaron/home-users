# AGENTS.md

## Project Overview

Symfony 7 REST API for managing users of **Centro Cultural de Aragón de Tarragona**.

Built with hexagonal architecture: pure Domain layer (Layer 1) + CQRS Application/Infrastructure adapters (Layer 2).

---

## Architecture

```
src/User/
├── Domain/          # Layer 1 — pure PHP, zero framework dependencies
├── Application/     # Layer 2a — CQRS Commands/Queries + Handlers
└── Infrastructure/  # Layer 2b — Doctrine ORM adapter, HTTP controllers
```

### Key design rules
- **Domain must stay pure**: no Symfony, Doctrine, or framework imports inside `src/User/Domain/`
- **One controller per route**: each HTTP action lives in its own controller class under `src/User/Infrastructure/Http/Controller/`
- **Value Objects validate themselves**: constructors throw `\InvalidArgumentException` on invalid input — never allow invalid state
- **Repository as port**: always depend on `UserRepositoryInterface` (domain), never on `DoctrineUserRepository` (infrastructure)
- **Doctrine XML mapping only**: ORM mapping lives in `src/User/Infrastructure/Doctrine/User.orm.xml` — no annotations on domain classes

---

## Code Style

This project enforces **PSR-12** + **Symfony** coding standards via PHP-CS-Fixer.

Run the linter before every commit:

```bash
vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php
```

Check without modifying:

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff --config .php-cs-fixer.dist.php
```

Rules configured in `.php-cs-fixer.dist.php`:
- `@PSR12` + `@Symfony` rule sets
- `declare_strict_types` on every file
- `ordered_imports` alphabetically
- `no_unused_imports`
- `trailing_comma_in_multiline`

---

## Testing

Unit tests live under `tests/Unit/` and mirror the `src/` structure.

```bash
# Run all unit tests
vendor/bin/phpunit --configuration phpunit.dist.xml

# Run a specific test file
vendor/bin/phpunit --configuration phpunit.dist.xml tests/Unit/User/Domain/UserTest.php
```

### Testing conventions

- **Domain tests**: test Value Object validation (valid/invalid inputs), `User::create()`, `User::update()`, `User::toPrimitives()`
- **Application tests**: mock the repository using `createMock()` when verifying calls with `expects()`; use `createStub()` when only configuring return values
- **No infrastructure tests**: Doctrine repository and controllers are not unit-tested — they require integration/functional tests
- Never use `createMock()` without setting `expects()` — use `createStub()` instead to avoid PHPUnit 13 strict notices

### Test data helpers

Use valid Spanish fixtures:
- DNI: `12345678Z` (12345678 % 23 = 14 → 'Z') or `00000023T`
- IBAN: `ES9121000418450200051332` or `DE89370400440532013000`
- UUID: any valid v4, e.g. `550e8400-e29b-41d4-a716-446655440000`

---

## Adding a New Feature

### New field on User

1. Add Value Object in `src/User/Domain/ValueObject/`
2. Add the field to `User` aggregate (`__construct`, `create`, `fromPrimitives`, `update`, `toPrimitives`)
3. Add column to `src/User/Infrastructure/Doctrine/User.orm.xml`
4. Add column to `src/User/Infrastructure/Doctrine/UserDoctrineEntity.php`
5. Update `DoctrineUserRepository` mapping (`save` and `toDomain`)
6. Update commands/queries and HTTP controllers
7. Generate and run migration: `php bin/console doctrine:migrations:diff && php bin/console doctrine:migrations:migrate`
8. Add unit tests for the new Value Object and update existing handler tests

### New use case

1. Create `Command` or `Query` DTO in `src/User/Application/<UseCaseName>/`
2. Create the corresponding `Handler` in the same directory
3. Add a new controller in `src/User/Infrastructure/Http/Controller/` with `#[Route(...)]`
4. Write unit tests for the handler in `tests/Unit/User/Application/<UseCaseName>/`

---

## Database

Default: **SQLite** (zero config for local development).

```bash
# Create schema
php bin/console doctrine:schema:create

# Generate a migration after model changes
php bin/console doctrine:migrations:diff

# Apply pending migrations
php bin/console doctrine:migrations:migrate
```

To switch to PostgreSQL, update `DATABASE_URL` in `.env`:

```
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/home_users?serverVersion=16&charset=utf8"
```

---

## REST API Reference

| Method | Endpoint | Success | Error |
|--------|----------|---------|-------|
| `POST` | `/api/users` | `201 Created` | `422` duplicate email/DNI, invalid value |
| `GET` | `/api/users` | `200 OK` | — |
| `GET` | `/api/users/{id}` | `200 OK` | `404` not found |
| `PUT` | `/api/users/{id}` | `204 No Content` | `404`, `422` |
| `DELETE` | `/api/users/{id}` | `204 No Content` | `404` not found |

### Example request body (POST / PUT)

```json
{
  "name": "Joan",
  "first_surname": "Garcia",
  "second_surname": "Pérez",
  "dni": "12345678Z",
  "email": "joan.garcia@example.com",
  "phone_number": "612345678",
  "bank_account_number": "ES9121000418450200051332",
  "date_of_birth": "1990-06-15"
}
```

The `id` field is optional on `POST` — a UUID v4 is generated automatically if omitted.

---

## Docker

All Docker and Docker Compose files live under `ops/docker/`.

```
ops/docker/
├── Dockerfile                  # Multi-stage build (dev / prod targets)
├── docker-compose.yml          # Dev stack: php-fpm + nginx + postgres
├── docker-compose.prod.yml     # Production overrides
├── .env.example                # Copy to .env and fill in secrets
├── nginx/
│   └── default.conf            # Nginx → PHP-FPM (Symfony front controller)
└── php/
    ├── php.ini                 # OPcache, memory, timezone
    └── php-fpm.conf            # FPM pool settings
```

### Start dev environment

```bash
cd ops/docker
cp .env.example .env            # fill in secrets
docker compose up -d --build
docker compose exec php php bin/console doctrine:migrations:migrate
```

API available at `http://localhost:8080/api/users`.

### Start production environment

```bash
cd ops/docker
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

In production the database port is **not** exposed and the PHP image is built with `--no-dev` composer deps and a warmed-up cache.

### Useful commands

```bash
# Run migrations inside container
docker compose exec php php bin/console doctrine:migrations:migrate

# Run unit tests inside container
docker compose exec php vendor/bin/phpunit --configuration phpunit.dist.xml

# Tail logs
docker compose logs -f php nginx

# Stop everything
docker compose down
```

---

## Pre-commit Checklist

Before pushing:

```bash
vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php   # fix style
vendor/bin/phpunit --configuration phpunit.dist.xml             # run tests
```

Both must complete without errors or violations.
