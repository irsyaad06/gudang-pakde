# 08 — Deployment, Operations & Submission

## Development setup
Existing project directory: `gudang-pakde` (Laravel already installed by developer). Do not recreate it.

### Prerequisites
- PHP + Composer compatible with installed Laravel 13.
- Node.js/npm compatible with installed Vite.
- MySQL 8.x.
- Git.

### Fresh clone setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configure DB_* in .env and create the MySQL database
php artisan migrate --seed
php artisan storage:link
php artisan serve
# in another terminal
npm run dev
```
Run `php artisan route:list`, `php artisan test`, and `npm run build` to verify. Migrations, category seeder, and public-disk upload code are implemented. Tests use SQLite in-memory from `phpunit.xml`; they do not validate MySQL connectivity. Validate `php artisan migrate:status` and `php artisan migrate --seed` with a configured local MySQL database before deploying. Existing local `.env` files are not changed automatically.

### Example local `.env` values
```dotenv
APP_NAME="Gudang Pakde"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gudang_pakde
DB_USERNAME=root
DB_PASSWORD=
```
**Local example only:** never use debug mode, empty passwords, or a root DB account in production.

## CI quality gates (recommended)
1. Install PHP/Node dependencies with lockfiles.
2. Run code formatting/static checks if configured.
3. Run `php artisan test`.
4. Run frontend build (`npm run build`) and typecheck if configured.
5. Fail pipeline on test/build errors.
6. Ensure no secrets are committed.

## Production deployment design (not MVP task)
- Build immutable Laravel/container release with compiled Vite assets.
- Use HTTPS edge + load balancer + private app instances.
- Configure production env and secrets, shared Redis, object storage, MySQL.
- Run backward-compatible migrations with a documented rollback plan.
- Health-check instances before serving traffic.
- Centralize logs, monitor metrics and error alerts.
- Schedule backups and practice restore.
- Restrict network access and use least privilege.

## Operational runbook
| Symptom | First checks |
|---|---|
| HTTP 500 | Laravel logs, APP_KEY, env/config cache, database connectivity |
| Blank Vue page | Browser console, Vite manifest, `npm run build` |
| Login loop | Session driver, cookie domain, trusted proxy/HTTPS |
| Missing images | Storage disk config, `storage:link`, file permissions/object ACL |
| Slow catalog | Query plan, indexes, N+1, pagination, cache hit |
| Queue stuck | Worker health, Redis connectivity, failed jobs |
| Database unavailable | Network/security groups, credentials, DB health |
| Deployment failure | CI logs, migrations, rollback previous release |

## One-day implementation schedule
| Time | Focus |
|---|---|
| 1.5 h | Validate starter/auth, DB connectivity |
| 1 h | Models/migrations/seeders |
| 2 h | Listing CRUD, photo upload, policy |
| 1.5 h | Catalog, detail, search/filter |
| 0.75 h | Profile, status, WhatsApp |
| 1.25 h | Tests, documentation, screenshots |

## GitHub submission checklist
- [ ] Application runs locally from documented steps
- [ ] Source code Laravel/Vue committed
- [ ] README overview, stack, setup, environment
- [ ] `.env.example` contains no real secrets
- [ ] Database design documented
- [ ] Application architecture diagram documented
- [ ] Infrastructure architecture diagram documented
- [ ] Route/API contracts and auth requirements documented
- [ ] Technical decisions/trade-offs documented
- [ ] Scaling, reliability, security documented
- [ ] Deployment/monitoring/backup/troubleshooting documented
- [ ] Screenshot/demo link if available
- [ ] `php artisan test` passes (report exceptions)
- [ ] `npm run build` passes

## Documentation maintenance
Application routes and schema are now implemented as described in docs 05/06; production scaling and infrastructure remain design-only. Keep route names, props, schema, and implementation status current. Do not claim infrastructure has been deployed simply because its diagram exists.
