# Pentagon Quest

Clean architecture with UI separated from backend logic. The repository has two folders:

```
apps/pentagon_quest_logic/   backend, Composer, vendor, .env, migrations, updates
public/                      website UI (this folder is public_html on hosting)
```

On hosting, upload `public/` as `public_html/` and keep `apps/pentagon_quest_logic/` next to it (not inside the web root):

```
/home/user/apps/pentagon_quest_logic/   Composer, vendor, .env, PHP logic
/home/user/public_html/                 contents of public/ (index.php, router, assets)
```

## Folder layout

### `apps/pentagon_quest_logic`

Complete backend. Not web-accessible. This is the only place `vendor/` belongs.

| Path | Role |
| --- | --- |
| `composer.json` / `composer.lock` | PHP dependencies |
| `vendor/` | Installed packages (`composer install` here) |
| `.env` / `.env.example` | Database, SMTP, and admin credentials |
| `.htaccess` | Denies HTTP access to this folder |
| `Core/` | Database connection and admin auth |
| `Models/` | Persistence |
| `Services/` | Business rules (enquiries, tours, mail, updates, …) |
| `Controllers/Api/` | JSON API actions |
| `Helpers/` | Path, upload, validation, CSRF, responses |
| `config/` | App, database, and SMTP settings |
| `database/required_migrations/` | Complete CREATE TABLE files from Models/ (parent tables first; run on install) |
| `database/migrations/` | Legacy incremental SQL (not used by setup) |
| `database/seeds/` | Initial content |
| `updates/` | Incremental patches you push later and run from the admin UI |
| `cli/` | Off-web helpers such as admin registration |

### `public` (becomes `public_html`)

| Path | Role |
| --- | --- |
| `index.php` | Home page / site entry |
| `router.php` | Routing for the PHP built-in server |
| `.htaccess` | Apache routing and security headers |
| `assets/` | CSS, JS, images, videos, and **uploads** (`assets/images/uploads/`) |
| `admin/`, `api/`, `handlers/` | Admin UI and thin HTTP adapters |

There is no `pages/` folder and no second `assets/` or `vendor/` at the repo root.

## Updates folder

Put new schema or data patches in:

`apps/pentagon_quest_logic/updates/`

Name files so they run in order:

- `001_add_newsletter_flag.sql`
- `002_backfill_slugs.php`

SQL files are executed as-is. PHP files must return a callable:

```php
<?php
return static function (PDO $db): void {
    $db->exec('ALTER TABLE destinations ADD COLUMN slug VARCHAR(160) NULL');
};
```

Then open **Admin → Updates** and click **Run pending updates**. Applied files are stored in `schema_updates` and will not run again.

The installer also applies any pending updates after baseline migrations:

```bash
php apps/pentagon_quest_logic/database/migrate.php
```

## Setup

1. Copy `apps/pentagon_quest_logic/.env.example` to `apps/pentagon_quest_logic/.env` and set database plus SMTP values.
2. Install PHP packages from the backend folder only:

```bash
cd apps/pentagon_quest_logic
composer install
```

3. Create the MySQL database, then run the migrate command above.
4. Open `http://localhost:8000/setup/` to connect MySQL, run migrations, and create the admin account.
5. Point the web server document root at `public/` (this is `public_html` on shared hosting).
6. After setup, admin login is `/admin/login.php`.

## Local PHP server

Any of these work (the path handler finds `public/` or `public_html/`):

```bash
php -S localhost:8000 public/router.php
php -S localhost:8000 -t public public/router.php
php -S localhost:8000 -t public router.php
cd public && php -S localhost:8000 router.php
```

Then open `http://localhost:8000/setup/`.

If the backend is not next to the web root, set `PQ_APP_PATH` to the absolute path of `pentagon_quest_logic` and optionally `PQ_PUBLIC_PATH` to `public_html`.
