# Stack Research

**Domain:** v2.0 backend additions for procedural PHP HR app (CRUD/auth/session)
**Researched:** 2026-03-05
**Confidence:** HIGH

## Recommended Stack

This milestone should **add backend persistence/auth only** and keep the current app shape (page-based procedural PHP + includes).

### Core Technologies

| Technology | Version | Purpose | Why Recommended |
|------------|---------|---------|-----------------|
| PHP | **8.4.x** (local verified: **8.4.10**) | Runtime for all new CRUD/auth/session code | Already installed and active; supports modern password/session APIs and procedural MySQLi cleanly. No migration cost. |
| MySQL-compatible server | **MySQL 8.4 LTS** (preferred) or existing local MySQL/MariaDB from Laragon/XAMPP | Canonical DB for employees/users/reports | InnoDB + FK + transactions are required for HR-first provisioning integrity. Use the local bundled server to avoid env drift. |
| MySQLi (procedural) + mysqlnd | Built-in PHP extension (verified enabled) | DB access via `koneksi.php` + prepared statements | Matches project constraint (native procedural PHP, no ORM/framework). |
| Native PHP Session | Built-in PHP extension (verified enabled) | Server-side login state + role guard | Correct model for this server-rendered app; simpler and safer than token/JWT for this scope. |
| Password API (`password_hash`, `password_verify`) | Built-in PHP API | Password storage/verification | Official and current API; meets SEC-01 baseline directly. |

### Supporting Libraries

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| `phpoffice/phpspreadsheet` | **5.5.0** (verified installed) | Existing XLSX export | Keep only for export flows (`hr/export.php`). Do not expand Composer footprint for CRUD/auth. |

### Development Tools

| Tool | Purpose | Notes |
|------|---------|-------|
| Laragon / XAMPP | Local Apache+PHP+DB runtime | Keep localhost workflow; avoid infra changes in v2.0. |
| phpMyAdmin (bundled) | Manual DB verification | Use for local data inspection only; not part of app runtime logic. |
| Composer 2.x | Existing dependency management | Keep as-is; no new package required for v2.0 backend features. |

## Exact Stack Additions/Changes for v2.0

### 1) Add one DB bootstrap file (required)
Create `koneksi.php` as the **single** connection source:
- `mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)`
- `mysqli_connect(...)`
- `mysqli_set_charset($conn, 'utf8mb4')`
- return/expose one `$koneksi` handle

### 2) Add DB schema + repeatable migration path (required)
Add:
- `database/schema.sql`
- `database/migrate.php` (or equivalent procedural runner)

Minimum tables (from active requirements):
- `employees`
- `users`
- `leave_reports`
- `leave_report_rows`
- `schema_migrations`

DB defaults:
- Engine: **InnoDB**
- Charset: **utf8mb4**
- FK constraints between `users.employee_id -> employees.id` and report relations

### 3) Add procedural auth/session guard layer (required)
Add include helpers (function-based, not classes):
- `includes/session_bootstrap.php`
- `includes/auth.php`
- `includes/guards.php`

Expected behavior:
- start session once in bootstrap
- login verifies DB hash with `password_verify`
- regenerate ID after successful login (`session_regenerate_id`)
- role guard for HR vs employee pages
- logout destroys session and cookie correctly

### 4) Add procedural CRUD layer for HR employee management (required)
Add function files (or one consolidated file):
- `includes/employee_data.php`
- `includes/user_data.php`

Rules:
- prepared statements for all user input queries
- transaction for HR-first provisioning (create employee + activate user)
- soft-delete/inactive path if business rule needs non-destructive delete

### 5) Preserve existing behavior while migrating data source
Current features to preserve:
- deterministic leave calculator engine
- existing dashboards/layout
- report/export behavior

Migration approach:
- keep existing UI pages
- replace session-array source behind functions with DB-backed reads/writes
- keep PhpSpreadsheet export endpoint, only swap data source to DB

## Exact Integration Points (Current Codebase)

| Existing File | Change Needed | Stack Impact |
|---|---|---|
| `login.php` | Replace demo role switch with real POST auth + session set | Introduces DB-backed auth and session role state |
| `hr/dashboard.php` and HR pages | Add `requireRole('hr')` guard | Enforces AUTH-03 |
| `employee/dashboard.php` | Resolve current user via session + DB (remove demo preset identity flow for real login path) | Ensures employee sees own data only |
| `includes/reports-data.php` | Stop owning session init directly; consume shared session bootstrap and move toward DB storage | Avoids split session/auth behavior |
| `hr/export.php` | Keep PhpSpreadsheet usage; swap report source to DB-backed queries when migration step lands | Preserves validated export behavior |

## Installation

```bash
# Existing dependency only
composer install

# No new composer package is required for v2.0 CRUD/auth/session
```

## Alternatives Considered

| Recommended | Alternative | When to Use Alternative |
|-------------|-------------|-------------------------|
| MySQLi procedural | PDO | Use PDO only if you intentionally standardize cross-DB portability later. Not needed for this milestone. |
| Native session auth | JWT/token auth | Use JWT only for API/mobile/multi-client architecture. Not needed for server-rendered localhost app. |
| Procedural includes/functions | Framework (Laravel/Slim/Symfony) | Only for a future rewrite milestone; explicitly out of scope now. |

## What NOT to Use

| Avoid | Why | Use Instead |
|-------|-----|-------------|
| Framework/OOP migration | Violates current milestone constraints and adds rewrite risk | Procedural includes + functions |
| ORM (Eloquent/Doctrine) | Unnecessary complexity for native procedural milestone | MySQLi prepared statements |
| JWT/OAuth session replacement | Wrong auth model for this server-rendered localhost scope | Native PHP sessions |
| Custom hashing / MD5 / SHA1 | Insecure for credentials | `password_hash` + `password_verify` |
| New frontend stack (React/Vue SPA) | No requirement fit; high regression risk | Keep existing PHP-rendered UI |
| New Composer auth/DB libraries | Not required for target capability | Native PHP/MySQLi APIs |

## Stack Patterns by Variant

**If staying HTTP localhost (default Laragon/XAMPP):**
- Keep native sessions
- `cookie_secure=false`, `httponly=true`, `samesite=Lax`
- Enable `cookie_secure=true` later when HTTPS localhost is enabled

**If provisioning must be atomic (recommended):**
- Wrap employee create + user create/activate in one DB transaction
- Roll back both on failure

## Version Compatibility

| Package A | Compatible With | Notes |
|-----------|-----------------|-------|
| PHP 8.4.x | MySQLi + mysqlnd | Verified enabled in current runtime (`php -m`). |
| PHP 8.4.x | Session extension | Verified enabled in current runtime. |
| PHP 8.4.x | PhpSpreadsheet 5.5.0 | Verified installed and compatible (package requires PHP ^8.1). |
| PASSWORD_DEFAULT hashes | `VARCHAR(255)` | PHP docs recommend flexible length because defaults can evolve. |

## Sources

- PHP Supported Versions: https://www.php.net/supported-versions.php (checked 2026-03-05)
- PHP `password_hash`: https://www.php.net/manual/en/function.password-hash.php
- PHP `password_verify`: https://www.php.net/manual/en/function.password-verify.php
- PHP sessions `session_start`: https://www.php.net/manual/en/function.session-start.php
- PHP sessions `session_regenerate_id`: https://www.php.net/manual/en/function.session-regenerate-id.php
- PHP MySQLi prepared statements: https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php
- PHP MySQLi charset: https://www.php.net/manual/en/mysqli.set-charset.php
- PHP MySQLi transactions: https://www.php.net/manual/en/mysqli.begin-transaction.php
- MySQL release model (LTS vs Innovation): https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html
- Local repo verification: `php -v`, `php -m`, `composer show phpoffice/phpspreadsheet`

---
*Stack research for: Sicuti HRD v2.0 backend native procedural PHP milestone*