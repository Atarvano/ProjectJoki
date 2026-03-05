---
phase: 14-database-foundation
plan: 01
subsystem: database
tags: [mysql, mysqli, sql, seed-data]

requires:
  - phase: roadmap
    provides: Phase 14 requirements and schema contract
provides:
  - Importable SQL schema for sicuti_hrd database
  - Global MySQLi connection via koneksi.php
  - Migration runner bound to $koneksi contract
affects: [phase-15-authentication, phase-16-crud, phase-17-provisioning, phase-18-data-wiring]

tech-stack:
  added: [MySQL InnoDB schema file, procedural MySQLi connection pattern]
  patterns: [global $koneksi include contract, prepared-statement baseline]

key-files:
  created: [database/sicuti_hrd.sql, koneksi.php]
  modified: [database/migrate.php]

key-decisions:
  - "Use CREATE IF NOT EXISTS plus ON DUPLICATE KEY UPDATE to keep manual SQL re-import safe"
  - "Keep prepared-statement sanity check silent in koneksi.php so included pages do not get extra output"

patterns-established:
  - "All PHP pages include koneksi.php and reuse global $koneksi"
  - "Auth and CRUD queries should follow mysqli_prepare + mysqli_stmt_bind_param"

requirements-completed: [DATA-01, DATA-02, DATA-03, DATA-04, DATA-05, DATA-06]

duration: 2 min
completed: 2026-03-05
---

# Phase 14 Plan 01: Database Foundation Summary

**Importable MySQL schema with karyawan/users tables, HR admin seed, and a shared MySQLi connection contract for all next phases.**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-05T05:18:32Z
- **Completed:** 2026-03-05T05:20:25Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments
- Added `database/sicuti_hrd.sql` with database creation, 3 core tables, FK cascade, indexes, and deterministic seed data.
- Added root `koneksi.php` exposing global `$koneksi` and a prepared-statement join sanity-check pattern.
- Updated `database/migrate.php` to use `$db = $koneksi` instead of non-existent `db_connect()`.

## Task Commits

Each task was committed atomically:

1. **Task 1: Create SQL schema file with tables, indexes, and seed data** - `62eb882` (feat)
2. **Task 2: Create koneksi.php and update migrate.php** - `c7dd6f2` (fix)

## Files Created/Modified
- `database/sicuti_hrd.sql` - Full SQL import artifact with schema and seeds.
- `koneksi.php` - Global MySQLi connection and prepared statement example.
- `database/migrate.php` - Migration runner now wired to `$koneksi`.

## Decisions Made
- Used idempotent SQL patterns (`CREATE ... IF NOT EXISTS`, `ON DUPLICATE KEY UPDATE`) to support repeat imports safely.
- Kept sanity-check query non-verbose to avoid breaking any HTML output flow when `koneksi.php` is required by pages.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Recreated missing migration runner path before applying planned fix**
- **Found during:** Task 2 (Create koneksi.php and update migrate.php)
- **Issue:** `database/migrate.php` and `database/` were missing from working tree, blocking the required line update.
- **Fix:** Recreated `database/` and restored `database/migrate.php` content, then applied `$db = $koneksi` change.
- **Files modified:** `database/migrate.php`
- **Verification:** File now contains `require_once __DIR__ . '/../koneksi.php';` and `$db = $koneksi;`
- **Committed in:** `c7dd6f2`

---

**Total deviations:** 1 auto-fixed (1 blocking)
**Impact on plan:** Fix was necessary to complete required artifact with no scope expansion.

## Issues Encountered
- `mysql` CLI is unavailable in this environment (`mysql: command not found`).
- Local MySQL service was unreachable from PHP (`connection actively refused`), so import/query runtime verification could not be completed here.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Database artifacts and connection contract are in place for auth and CRUD phases.
- Local MySQL service should be started before running Phase 15 runtime checks.

---
*Phase: 14-database-foundation*
*Completed: 2026-03-05*

## Self-Check: PASSED
- FOUND: database/sicuti_hrd.sql
- FOUND: koneksi.php
- FOUND: database/migrate.php
- FOUND: 62eb882
- FOUND: c7dd6f2
