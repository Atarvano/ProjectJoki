# Stack Research

**Domain:** Procedural PHP backend foundation for HR app milestone
**Researched:** 2026-03-04
**Confidence:** HIGH

## Recommended Stack

This milestone should **extend the existing native procedural PHP app**, not reshape it. The right stack is a **small procedural backend layer** on top of the current pages: `mysqli` for database access, MySQL in Laragon, native PHP sessions for auth, and built-in password APIs for credential handling.

The key point: **add persistence and real login, but keep the codebase page-based and include-driven**. Do not introduce framework architecture, ORM abstractions, or SPA tooling for this milestone.

### Core Technologies

| Technology | Version | Purpose | Why Recommended |
|------------|---------|---------|-----------------|
| PHP | **8.4.x** (current local CLI: **8.4.10**) | Runtime for all existing and new procedural app code | Best fit because the project already runs on PHP 8.4 in Laragon, and PHP 8.4 has current session/password APIs, strict MySQLi error behavior defaults, and no migration cost. Use the installed 8.4 line rather than downgrading or jumping to framework-dependent stacks. |
| MySQL Community Server | **8.0.x** (current local install appears to be **8.0.30**) | Primary relational database for employee/auth data | Practical fit for Laragon/phpMyAdmin workflows, supports InnoDB, transactions, foreign keys, and `utf8mb4`. Use the Laragon-installed 8.0 line for compatibility and minimal setup friction. |
| MySQLi procedural API | Built into PHP 8.4 | Database connection/query layer via `koneksi.php` and CRUD helpers | This is the correct DB API for a procedural PHP codebase. It keeps code style aligned with the project constraint while still supporting prepared statements, transactions, charset setup, and strict error handling. |
| Native PHP Sessions | Built into PHP 8.4 | Session-backed login state for HR and employee access control | This milestone explicitly calls for native session auth. PHP sessions are enough for a local/internal Laragon app and integrate directly with current page-based routing. |
| `password_hash()` / `password_verify()` | Built into PHP 8.4 | Password hashing and verification | Official, maintained password API. Avoids homemade hashing and works cleanly with procedural login handlers. |

### Supporting Libraries

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| PhpSpreadsheet | **^5.5** (already present) | Existing XLSX export for reports | **Keep as-is only for export endpoints.** Do not expand Composer usage into auth, CRUD, routing, or app bootstrap. |
| phpMyAdmin | **6.x** for PHP 8.4+ in Laragon | Schema inspection, manual data checks, quick local admin work | Use for creating/verifying local tables and records in development. Laragon docs specifically recommend phpMyAdmin 6 when using PHP 8.4+. |
| Bootstrap | Existing project version | UI for forms/tables/messages around CRUD and login | Reuse existing frontend stack only. No frontend stack change is needed for backend milestone work. |

### Development Tools

| Tool | Purpose | Notes |
|------|---------|-------|
| Laragon | Local PHP/MySQL/apache environment | Keep using Laragon as the source of truth for PHP and MySQL versions. Prefer matching CLI/runtime versions when running Composer or scripts. |
| phpMyAdmin 6 | Database admin UI | Add through Laragon Quick Add if not already installed. Use for local schema and row inspection, not as part of the app itself. |
| Composer 2.x | Dependency management for the already-approved export library | Keep Composer isolated to `vendor/` and `hr/export.php` usage. This milestone does **not** need additional Composer packages. |

## Recommended Stack Additions for This Milestone

### 1) Database layer: add a single procedural connection entrypoint

Add a dedicated **`koneksi.php`** file as the only place that opens MySQL connections.

Recommended behavior:
- create one connection with `mysqli_connect()`
- call `mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)` early
- set charset with `mysqli_set_charset($conn, 'utf8mb4')`
- use explicit host/database/user/password variables
- return or expose one connection handle consistently

Recommended local connection defaults for Laragon:
- host: **`127.0.0.1`**
- port: **`3306`** unless Laragon profile differs
- database: project-specific DB such as **`sicuti_hrd`**
- charset: **`utf8mb4`**
- engine: **InnoDB**

Why `127.0.0.1` instead of `localhost`:
- PHP docs note `localhost` has special transport behavior; using `127.0.0.1` makes the TCP/IP path explicit and avoids host-resolution ambiguity.

Recommended pattern:

```php
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$koneksi = mysqli_connect('127.0.0.1', 'root', '', 'sicuti_hrd', 3306);
mysqli_set_charset($koneksi, 'utf8mb4');
```

Use this file from CRUD/auth handlers, not from every template fragment.

### 2) Schema baseline: keep it small and HR-first

For this milestone, the schema should support:
- HR-managed employee master data
- login credentials linked to employees
- role-based access (`hr` vs `employee`)
- employee enablement only after HR creates the record

Recommended minimum tables:

#### `employees`
Purpose: source of truth for employee data.

Recommended columns:
- `id` BIGINT/INT primary key
- `employee_code` VARCHAR unique
- `nama` VARCHAR
- `email` VARCHAR unique
- `tahun_bergabung` YEAR or SMALLINT/INT
- `jabatan` VARCHAR nullable
- `departemen` VARCHAR nullable
- `status_aktif` TINYINT(1) default 1
- `created_at` DATETIME
- `updated_at` DATETIME

#### `users`
Purpose: auth/login table linked to employee records.

Recommended columns:
- `id` BIGINT/INT primary key
- `employee_id` FK to `employees.id` nullable for bootstrap HR account if needed
- `username` VARCHAR unique
- `password_hash` VARCHAR(255)
- `role` ENUM('hr','employee') or VARCHAR with constrained values
- `is_active` TINYINT(1) default 1
- `last_login_at` DATETIME nullable
- `created_at` DATETIME
- `updated_at` DATETIME

Why separate `users` from `employees`:
- keeps auth credentials distinct from employee profile data
- allows HR-first onboarding flow
- avoids mixing login concerns into every employee CRUD query
- preserves room for future policy changes without a rewrite

Recommended DB defaults:
- all tables use **InnoDB**
- all text uses **utf8mb4**
- indexes on `employee_code`, `email`, `username`, and FK columns

### 3) CRUD layer: procedural helper files, not repositories/classes

Add small procedural include files such as:
- `koneksi.php`
- `includes/session.php`
- `includes/auth.php`
- `includes/employee-data.php`
- `includes/flash.php`

Recommended role for each:
- **`koneksi.php`**: only DB connection/bootstrap
- **`includes/session.php`**: central `session_start([...])` wrapper and logout helper
- **`includes/auth.php`**: login check, role guard, current-user lookup
- **`includes/employee-data.php`**: employee CRUD functions using prepared statements
- **`includes/flash.php`**: session flash messages for create/update/delete/login failures

Example function set:
- `findUserByUsername(mysqli $conn, string $username): ?array`
- `findEmployeeById(mysqli $conn, int $id): ?array`
- `getAllEmployees(mysqli $conn): array`
- `createEmployee(mysqli $conn, array $data): int`
- `updateEmployee(mysqli $conn, int $id, array $data): void`
- `deleteEmployee(mysqli $conn, int $id): void`
- `requireLogin(): void`
- `requireRole(string $role): void`

Even if you use type hints in signatures, keep the implementation procedural and function-based.

### 4) Auth layer: use native sessions plus password API only

Recommended login/auth stack:
- `session_start()` with explicit cookie options
- `password_hash()` when creating/resetting passwords
- `password_verify()` during login
- `session_regenerate_id()` immediately after successful login
- role checks in page guards (`hr` and `employee`)

Recommended session options for local/current setup:
- `cookie_httponly => true`
- `cookie_samesite => 'Lax'`
- `cookie_secure => false` on plain local HTTP, **true later if local HTTPS is enabled**
- `use_strict_mode => true`
- `cookie_path => '/'`

Recommended session payload:
- `user_id`
- `employee_id` if role is employee
- `role`
- `display_name`
- maybe `last_activity` if inactivity timeout is added

Do not store large employee records in `$_SESSION`; store identifiers and reload fresh data from DB.

### 5) Password storage: size columns for current and future hashes

Use:
- `password_hash($password, PASSWORD_DEFAULT)`
- `password_verify($input, $storedHash)`

DB recommendation:
- `password_hash` column should be **VARCHAR(255)**

Why 255:
- PHP manual explicitly notes `PASSWORD_DEFAULT` can change over time and advises storing beyond 60 bytes; 255 is the practical safe default.

### 6) Query practice: prepared statements everywhere input is involved

For this milestone, use prepared statements for:
- login lookup by username/email
- create employee
- update employee
- delete employee
- employee detail lookup
- uniqueness checks

Why:
- PHP MySQLi docs explicitly recommend prepared statements as the less error-prone protection against SQL injection
- procedural code gets messier fast if it relies on string interpolation and manual escaping

Recommended rule:
- **Prepared statements for all user-supplied values**
- plain queries only for constant SQL with no external input

### 7) Existing app integration points

These are the stack changes that best fit the current codebase shape:

#### Current `login.php`
Change from visual-only role switch to:
- GET = render login form
- POST = verify credentials against DB
- on success, set session + redirect to role dashboard
- on failure, flash error and re-render

#### Current session usage in `includes/reports-data.php`
This file already starts sessions. For v2, centralize session bootstrap so auth/reporting use the same session rules.

Recommended change:
- move session config/start logic into one reusable include
- keep report/session demo behavior separate from auth logic where possible

#### Current `employee/dashboard.php`
Replace preset demo identity selection with the currently authenticated employee from session + DB lookup.

#### Current `hr/dashboard.php` and report pages
Add role guard (`requireRole('hr')`) before rendering HR-only pages.

#### Current `hr/export.php`
Keep current Composer/PhpSpreadsheet isolation. This milestone does **not** require changing export stack unless DB-backed reports are introduced.

## Installation

```bash
# Laragon local runtime target
# Keep project on PHP 8.4.x and MySQL 8.0.x

# Existing dependency only (already present)
composer install
```

Practical Laragon setup notes:
- Use **Laragon PHP 8.4.x** for the app
- Use **Laragon MySQL 8.0.x** for DB
- Add **phpMyAdmin 6** via `Menu > Tools > Quick add > phpmyadmin6.0snapshot` if phpMyAdmin is needed with PHP 8.4+
- Create DB manually in phpMyAdmin or with SQL script: `sicuti_hrd`

No new Composer packages are required for this milestone.

## Alternatives Considered

| Recommended | Alternative | When to Use Alternative |
|-------------|-------------|-------------------------|
| MySQLi procedural API | PDO | Use PDO only if the team explicitly wants DB-vendor portability or already has PDO helpers. For this milestone, MySQLi is the cleaner fit because the app is MySQL-only in Laragon and remains procedural. |
| MySQL 8.0.x in Laragon | MariaDB in Laragon | Use MariaDB only if the Laragon profile already standardizes on it. For this project, MySQL 8.0 is the safer recommendation because the research target is explicitly MySQL/phpMyAdmin and the local install already includes MySQL 8.0. |
| Native PHP sessions | Token/JWT auth | Use tokens only for APIs/mobile/external clients. This app is server-rendered and page-based, so sessions are simpler and more correct. |
| Separate `users` + `employees` tables | Single `employees` table with password columns | Single-table can work for very tiny apps, but it couples profile CRUD and auth too tightly. Separate tables reduce future rewrite pressure while staying procedural. |

## What NOT to Use

| Avoid | Why | Use Instead |
|-------|-----|-------------|
| Laravel / Symfony / Slim migration | Violates the milestone constraint and turns a backend foundation task into an architectural rewrite | Keep page-based procedural PHP with includes and helper functions |
| ORM / Eloquent / Doctrine | Adds object lifecycle complexity and framework gravity that the current app does not need | Use MySQLi prepared statements in procedural helper functions |
| JWT / OAuth / Sanctum / Passport | Wrong auth model for a local server-rendered Laragon app; adds unnecessary token complexity | Use native PHP sessions |
| MD5 / SHA1 / custom password hashing | Insecure or outdated for password storage | Use `password_hash()` and `password_verify()` |
| `SET NAMES utf8` as the main charset strategy | PHP docs recommend `mysqli_set_charset()` instead; `utf8` also risks 3-byte limitations | Use `mysqli_set_charset($conn, 'utf8mb4')` |
| MyISAM tables | Loses transaction and foreign-key strengths needed for real CRUD/auth data | Use InnoDB |
| Storing full user records in session | Causes stale data and bloated session state | Store IDs/role only and re-query current data |
| Expanding Composer across the whole app | Breaks the established constraint that Composer use stays isolated unless PHP lacks a native capability | Keep Composer isolated to export needs only |
| Frontend rewrite to React/Vue | Solves no milestone requirement and creates integration churn | Keep Bootstrap + existing PHP-rendered pages |

## Stack Patterns by Variant

**If the milestone stays local/internal on Laragon only:**
- Use PHP 8.4 + MySQL 8.0 + MySQLi + native sessions
- Keep `cookie_secure` off on local HTTP
- Because this is the simplest compatible setup and matches the current environment

**If local HTTPS is enabled later in Laragon:**
- Keep the same stack
- Turn `cookie_secure` on for sessions
- Because session architecture should stay the same; only cookie transport settings should harden

**If employee login is enabled only after HR creates records:**
- Keep `employees` as the primary master table
- Create linked `users` row only when credentials are issued
- Because it matches the requested HR-first onboarding flow without adding invite systems or registration features

## Version Compatibility

| Package A | Compatible With | Notes |
|-----------|-----------------|-------|
| PHP **8.4.x** | phpMyAdmin **6.x** | Laragon docs explicitly recommend phpMyAdmin 6 for PHP 8.4+ environments. |
| PHP **8.4.x** | PhpSpreadsheet **^5.5** | Existing dependency is compatible with modern PHP; keep usage isolated to export. |
| PHP **8.4.x** | MySQLi extension | Built into PHP runtime; no extra package needed beyond enabled extension. |
| MySQL **8.0.x** | InnoDB + `utf8mb4` | Recommended baseline for transactional CRUD/auth tables. |
| `PASSWORD_DEFAULT` hashes | `VARCHAR(255)` | Safe storage size for current and future PHP password hash formats. |

## Recommended Final Stack for This Milestone

Use this exact addition set:
- **PHP 8.4.x** (stay on current Laragon PHP line)
- **MySQL 8.0.x** in Laragon
- **MySQLi procedural API** for all DB work
- **`koneksi.php`** as the only connection bootstrap
- **InnoDB + utf8mb4** for schema defaults
- **Native PHP sessions** for login state
- **`password_hash()` / `password_verify()`** for credentials
- **Prepared statements** for all CRUD/auth inputs
- **phpMyAdmin 6** only as a local dev/admin tool
- **Existing PhpSpreadsheet ^5.5** retained only for export

That is the smallest stack change that delivers real backend capability without violating the procedural-PHP constraint or destabilizing the current shipped frontend.

## Sources

- PHP manual — MySQLi book: https://www.php.net/manual/en/book.mysqli.php — **HIGH confidence**
- PHP manual — MySQLi prepared statements: https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php — **HIGH confidence**
- PHP manual — `mysqli_set_charset()`: https://www.php.net/manual/en/mysqli.set-charset.php — **HIGH confidence**
- PHP manual — MySQLi report mode: https://www.php.net/manual/en/mysqli-driver.report-mode.php — **HIGH confidence**
- PHP manual — MySQLi connections: https://www.php.net/manual/en/mysqli.quickstart.connections.php — **HIGH confidence**
- PHP manual — `password_hash()`: https://www.php.net/manual/en/function.password-hash.php — **HIGH confidence**
- PHP manual — `password_verify()`: https://www.php.net/manual/en/function.password-verify.php — **HIGH confidence**
- PHP manual — `session_start()`: https://www.php.net/manual/en/function.session-start.php — **HIGH confidence**
- PHP manual — `session_set_cookie_params()`: https://www.php.net/manual/en/function.session-set-cookie-params.php — **HIGH confidence**
- PHP manual — `session_regenerate_id()`: https://www.php.net/manual/en/function.session-regenerate-id.php — **HIGH confidence**
- MySQL 8.0 reference manual — `utf8mb4`: https://dev.mysql.com/doc/refman/8.0/en/charset-unicode-utf8mb4.html — **HIGH confidence**
- MySQL 8.0/8.4 reference manual — InnoDB storage engine: https://dev.mysql.com/doc/refman/8.0/en/innodb-storage-engine.html and https://dev.mysql.com/doc/en/storage-engines.html — **HIGH confidence**
- Laragon operations docs: https://laragon.org/docs/operations.html — **HIGH confidence**
- phpMyAdmin requirements/news: https://docs.phpmyadmin.net/en/master/require.html and https://www.phpmyadmin.net/news/2025/10/8/phpmyadmin-523-is-released/ — **MEDIUM confidence** (official project sources, version 6 still documented as dev/snapshot)
- PhpSpreadsheet docs: https://phpspreadsheet.readthedocs.io/en/latest/ — **HIGH confidence**

---
*Stack research for: procedural PHP backend/auth/database milestone*