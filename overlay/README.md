# Inventory Studio — Laravel 13

An owner-scoped product inventory API with a responsive browser client. This is a fresh rebuild of the formerly README-only `laravel-4.2-crud` project, not an in-place framework upgrade.

## Requirements and local setup
PHP 8.3+ with SQLite, mbstring and XML extensions; Composer 2. No Node build required.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan inventory:token your-email@example.com
php artisan serve
```

Open http://127.0.0.1:8000 and paste the printed token. Tokens expire in 24 hours and are held only in browser memory. The default skeleton uses SQLite. Never upload `.env`, tokens, vendor, or real database files.

## Quality checks
```bash
vendor/bin/pint
php artisan test
composer validate --strict
composer audit
```
Commit `composer.lock` generated during setup. CI checks formatting, functional behavior, dependency metadata and known advisories. Dependencies are resolved at setup time; no unverified lockfile is included.

## API contract
All routes require `Authorization: Bearer TOKEN` and `Accept: application/json`.

| Method | Path | Behavior |
|---|---|---|
| GET | /api/v1/products?q=RF&per_page=10 | Owner-scoped search and pagination |
| POST | /api/v1/products | Create; 201 |
| GET | /api/v1/products/{id} | Read; 200 |
| PUT | /api/v1/products/{id} | Full validated update; 200 |
| DELETE | /api/v1/products/{id} | Delete; 204 |

Example JSON: `{"name":"RF module","sku":"RF-001","price_cents":12500,"stock":4,"description":"Laboratory module"}`.
Prices use USD integer cents: 12500 = $125.00. No float is stored. SKU uniqueness is per owner and enforced by a database constraint. Validation errors return 422; guests 401; other-owner access 403; missing records 404. Search uses SQL LIKE semantics, including `%` and `_` wildcards.

## Docker development
After local setup:
```bash
docker compose build
docker compose run --rm app php artisan migrate
docker compose up
```
The database directory persists on the host. The container binds only to local loopback. This uses PHP's development server; production needs a proper web server, TLS, secret management, backups, and an appropriate database. No public registration or password login is supplied; CLI-issued tokens support the local portfolio demo.

## Design decisions and limits
Form requests own validation, policies own authorization, resources define JSON, and controllers orchestrate Eloquent. There is no repository abstraction because this app has one persistence implementation. Database constraints complement request validation. A simultaneous duplicate-SKU race can currently surface as a database exception; map that conflict explicitly before deployment. Stock is edited as an absolute value, not a concurrency-safe order/reservation ledger. Deletion is permanent. Policies use Laravel's standard discovery conventions.

The tests cover product lifecycle, guest rejection, cross-owner isolation, search/pagination, numeric validation, duplicate SKU, ownership injection and missing IDs. Runtime tests were not executed in the generation environment because PHP/Composer were unavailable. Run the commands above before claiming a passing build or production readiness.
