# Architecture Research

**Domain:** Integrating DB + auth into existing procedural PHP leave app
**Researched:** 2026-03-04
**Confidence:** HIGH

## Standard Architecture

### System Overview

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                               HTTP Page Layer                              │
├─────────────────────────────────────────────────────────────────────────────┤
│  index.php   login.php   hr/*.php (dashboard/kalkulator/laporan/export)   │
│  employee/dashboard.php                                                     │
└───────────────┬───────────────────────────────┬─────────────────────────────┘
                │                               │
                ▼                               ▼
┌──────────────────────────────────┐   ┌──────────────────────────────────────┐
│         Request Guards           │   │        Existing Domain Logic         │
│  includes/auth.php              │   │  includes/cuti-calculator.php        │
│  - requireSession()             │   │  (kept as-is, reused)                │
│  - requireRole('hr'|'employee') │   └──────────────────────────────────────┘
└──────────────────┬───────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                  Data Access (procedural repositories)                     │
├─────────────────────────────────────────────────────────────────────────────┤
│ includes/koneksi.php  -> mysqli connection                                │
│ includes/employee-repo.php -> employee CRUD + lookup for auth             │
│ includes/report-repo.php    -> persisted reports CRUD/list/export         │
└──────────────────┬──────────────────────────────────────────────────────────┘
                   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                                MySQL Tables                                │
├─────────────────────────────────────────────────────────────────────────────┤
│ users (credentials, role, active)                                          │
│ employees (profile, join year, user_id nullable->required by flow)         │
│ leave_reports (summary/report metadata), leave_report_rows (8-year rows)   │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Component Responsibilities

| Component | Responsibility | Typical Implementation |
|-----------|----------------|------------------------|
| Page controllers (`hr/*.php`, `employee/dashboard.php`, `login.php`) | Handle request input, call guards + repositories + calculator, render HTML/redirect | Keep current top-of-file procedural controller block pattern |
| `includes/koneksi.php` | Single DB connect primitive for app | Build `mysqli` connection once per request (and charset) |
| `includes/auth.php` (new) | Session bootstrap, login/logout, route guards | Procedural helpers around `$_SESSION` + `session_start()` |
| `includes/employee-repo.php` (new) | Employee CRUD + auth lookups | `mysqli` prepared statements for select/insert/update/delete |
| `includes/report-repo.php` (new) | Replace session report store with DB-backed report storage | write/read report headers + rows via prepared statements |
| `includes/reports-data.php` (existing) | Transitional compatibility façade | Keep function names, rewire internals to call DB repo |

## Recommended Project Structure

```text
.
├── index.php                         # existing landing
├── login.php                         # MODIFIED: real credential POST + role redirect
├── logout.php                        # NEW: session destroy + redirect
├── hr/
│   ├── dashboard.php                 # MODIFIED: metrics from DB, require HR session
│   ├── kalkulator.php                # MODIFIED: save report against employee_id
│   ├── laporan.php                   # MODIFIED: report listing from DB
│   ├── export.php                    # MODIFIED: export DB-backed reports
│   └── employees.php                 # NEW: employee list/create/update/delete (HR only)
├── employee/
│   └── dashboard.php                 # MODIFIED: derive employee from session user_id
└── includes/
    ├── koneksi.php                   # NEW: DB connection (Laragon/phpMyAdmin pattern)
    ├── auth.php                      # NEW: auth/session helpers + guards
    ├── employee-repo.php             # NEW: employee CRUD + auth queries
    ├── report-repo.php               # NEW: persistent report queries
    ├── reports-data.php              # MODIFIED: legacy API delegates to report-repo
    ├── cuti-calculator.php           # existing canonical engine (unchanged logic)
    ├── dashboard-sidebar.php          # MODIFIED: add HR employee management nav link
    └── dashboard-topbar.php          # MODIFIED: real user label + logout link target
```

### Structure Rationale

- **Keep `/includes` as integration seam:** Current app already uses shared includes; adding DB/auth/repo there preserves architecture and minimizes rewrite risk.
- **Introduce new pages only where flow requires it:** `hr/employees.php` is the new HR-first onboarding center; rest are modifications to existing pages.

## Integration Points (Explicit New vs Modified)

### New Components/Files

1. **`includes/koneksi.php`**
   - Exposes one function (e.g., `db()` or `getDbConnection()`) returning configured `mysqli`.
   - Required by all repo modules, never called directly in view templates.

2. **`includes/auth.php`**
   - `startAppSession()`
   - `attemptLogin($email, $password)` -> verifies `password_verify()` against stored hash
   - `requireSession()` / `requireRole('hr'|'employee')`
   - `logoutUser()`

3. **`includes/employee-repo.php`**
   - `createEmployee(...)`, `updateEmployee(...)`, `deleteEmployee(...)`
   - `findEmployeeByUserId(...)`, `findUserByEmail(...)`, `listEmployees(...)`

4. **`includes/report-repo.php`**
   - `saveLeaveReport(...)`, `listReports(...)`, `getReportDetail(...)`, `countReports()`

5. **`hr/employees.php`**
   - HR-only CRUD page to satisfy “HR creates employee first” flow.

6. **`logout.php`**
   - Session teardown and redirect to `login.php`.

### Modified Existing Modules

1. **`login.php`**
   - From `?role=` visual switcher to POST login controller.
   - Role redirect based on authenticated user role in DB/session, not URL parameter.

2. **`hr/dashboard.php`**
   - Add `requireRole('hr')` guard.
   - Replace session report counters with DB query-backed counts.

3. **`hr/kalkulator.php`**
   - Preserve existing calculator behavior/UI.
   - Save report linked to persisted employee/user identities (not free-text-only session entry).

4. **`hr/laporan.php` + `hr/export.php`**
   - Read report list/details from DB repository.
   - Keep PhpSpreadsheet isolation in `hr/export.php` (existing decision preserved).

5. **`employee/dashboard.php`**
   - Add `requireRole('employee')`.
   - Replace preset demo selection with logged-in employee’s own profile/records.

6. **`includes/reports-data.php`**
   - Convert into compatibility shim so existing page calls still work while internals use DB.
   - This lowers migration risk and enables stepwise replacement.

## Architectural Patterns

### Pattern 1: Guard-Then-Work Page Entry

**What:** Every protected page starts session + role guard before business logic.
**When to use:** All `/hr/*`, `/employee/*`, and export endpoints.
**Trade-offs:** Slight boilerplate per page; major clarity and access safety.

**Example:**
```php
<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('hr');
// ...page controller logic...
```

### Pattern 2: Procedural Repository Boundary

**What:** SQL lives in repo includes, not in page files.
**When to use:** Any DB interaction (auth, employee CRUD, reports).
**Trade-offs:** More files/functions; big gain in maintainability and easier testing.

**Example:**
```php
function findUserByEmail(string $email): ?array {
    $db = db();
    $stmt = $db->prepare('SELECT id, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ?: null;
}
```

### Pattern 3: Compatibility Façade Migration

**What:** Keep existing function entry points (`getReports()`, `saveReport()`) but replace storage internals.
**When to use:** During v1->v2 migration to avoid touching every caller at once.
**Trade-offs:** Temporary indirection layer; significantly safer rollout.

## Data Flow

### Request Flow (new auth-aware)

```text
[User request]
    ↓
[Page controller]
    ↓
[auth guard + input validation]
    ↓
[repo call(s)] → [koneksi.php mysqli] → [MySQL]
    ↓
[existing cuti-calculator when needed]
    ↓
[render HTML or redirect]
```

### Key Data Flows (updated from v1)

1. **Login + session establishment**
   - `login.php` POST -> `findUserByEmail()` -> `password_verify()` -> `session_regenerate_id()` -> set `$_SESSION['user_id']`, `$_SESSION['role']` -> redirect by role.

2. **HR onboarding before employee access**
   - HR session -> `hr/employees.php` create employee + user credentials (or activate existing) -> employee can then log in.

3. **Employee self-view from identity (no presets)**
   - Employee session -> resolve `employee` by `user_id` -> calculate/render entitlement from persisted join year and/or latest stored report.

4. **Report persistence + export**
   - HR kalkulator saves report rows in DB -> laporan lists from DB -> export reads same dataset to guarantee parity.

## Suggested Database Baseline (for integration planning)

```sql
users (
  id PK,
  email UNIQUE,
  password_hash,
  role ENUM('hr','employee'),
  is_active,
  created_at
)

employees (
  id PK,
  user_id UNIQUE NULL,  -- nullable during onboarding, then linked
  nama,
  tahun_bergabung,
  created_by_hr_user_id,
  created_at,
  updated_at
)

leave_reports (
  id PK,
  employee_id FK,
  generated_by_hr_user_id FK,
  tahun_bergabung_snapshot,
  total_cuti,
  created_at
)

leave_report_rows (
  id PK,
  report_id FK,
  tahun_ke,
  tahun_kalender,
  hari_cuti,
  status
)
```

## Anti-Patterns to Avoid

### Anti-Pattern 1: Keeping auth decisions in query params (`?role=`)
**What people do:** Continue relying on URL role switches.
**Why it's wrong:** Bypasses real auth boundary and conflicts with session-backed flow.
**Do this instead:** Resolve role strictly from authenticated session state.

### Anti-Pattern 2: Mixing session demo data and DB data in same feature path
**What people do:** Some pages read `$_SESSION['reports']`, others read DB.
**Why it's wrong:** Inconsistent counts/export parity and debugging chaos.
**Do this instead:** Route all report read/write through one repo API.

### Anti-Pattern 3: SQL in page templates
**What people do:** Add ad-hoc mysqli queries directly in `hr/*.php`.
**Why it's wrong:** Duplicated SQL + harder migration/testing.
**Do this instead:** Keep SQL inside repo includes with prepared statements.

## Build Order (Dependency-Aware, Procedural Constraints Preserved)

1. **DB foundation first**
   - Add migration SQL + `includes/koneksi.php`.
   - Verify simple connectivity in Laragon/phpMyAdmin environment.

2. **Auth core second**
   - Implement `includes/auth.php` and `logout.php`.
   - Seed one HR user account and verify login/logout/guard behavior.

3. **Employee master data third (HR-only)**
   - Build `employee-repo.php` + `hr/employees.php` CRUD.
   - This enforces required flow dependency: employee accounts originate from HR.

4. **Report persistence layer fourth**
   - Build `report-repo.php`; adapt `includes/reports-data.php` as compatibility façade.
   - Keep existing page contracts stable while changing storage backend.

5. **Integrate HR modules fifth**
   - Update `hr/dashboard.php`, `hr/kalkulator.php`, `hr/laporan.php` to DB-backed repos + role guard.

6. **Integrate employee module sixth**
   - Update `employee/dashboard.php` to session identity model (remove preset selection path).

7. **Export + parity hardening last**
   - Update `hr/export.php` to read DB-backed reports and verify parity with laporan listing.

Dependency chain:

```text
DB connection -> Auth/session -> HR employee CRUD -> Report repo -> HR pages -> Employee page -> Export parity
```

## Scaling Considerations

| Scale | Architecture Adjustments |
|-------|--------------------------|
| 0-1k users | Current procedural monolith + mysqli + shared includes is sufficient |
| 1k-100k users | Add DB indexes (`users.email`, `employees.user_id`, `leave_reports.employee_id`) and pagination in laporan |
| 100k+ users | Consider background export jobs and eventually framework migration; not needed for this milestone |

## Sources

- Project context: `.planning/PROJECT.md`, `.planning/STATE.md` (read 2026-03-04)
- Existing code architecture: `index.php`, `login.php`, `hr/*.php`, `employee/dashboard.php`, `includes/*.php` (read 2026-03-04)
- PHP sessions (`session_start`, `session_regenerate_id`, `session_set_cookie_params`):
  - https://www.php.net/manual/en/function.session-start.php
  - https://www.php.net/manual/en/function.session-regenerate-id.php
  - https://www.php.net/manual/en/function.session-set-cookie-params.php
- PHP password hashing:
  - https://www.php.net/manual/en/function.password-hash.php
  - https://www.php.net/manual/en/function.password-verify.php
- PHP MySQLi + prepared statements:
  - https://www.php.net/manual/en/book.mysqli.php
  - https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php

---
*Architecture research for: v2.0 backend procedural foundation integration*
*Researched: 2026-03-04*
