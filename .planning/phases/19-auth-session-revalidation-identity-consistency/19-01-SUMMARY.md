---
phase: 19-auth-session-revalidation-identity-consistency
plan: 01
subsystem: auth
tags: [php, mysqli, sessions, rbac, smoke-test]

requires:
  - phase: 18-data-wiring-calculator-reports-dashboards
    provides: employee dashboard flow and shared auth usage on protected pages
provides:
  - shared session revalidation against live users rows on every protected request
  - employee-only karyawan linkage check before employee pages continue
  - focused smoke coverage for stale-session, inactive-account, and wrong-role redirect behavior
affects: [phase-19-plan-02, employee-dashboard, auth-guard, delete-flow]

tech-stack:
  added: []
  patterns:
    - per-request users revalidation in includes/auth-guard.php
    - employee linkage validation inside cekRole('employee')
    - plain PHP smoke test with auth guard test-mode arrays

key-files:
  created: []
  modified:
    - includes/auth-guard.php
    - employee/dashboard.php
    - tests/phase19_auth_session_smoke.php

key-decisions:
  - "Keep stale-session rejection centralized in includes/auth-guard.php so every protected page gets the same live user check."
  - "Treat missing or inactive users as logout-to-login, but keep valid wrong-role sessions on their old dashboard redirect flow."

patterns-established:
  - "Protected pages can still call cekLogin() and cekRole() at the top, while the guard itself owns the live database revalidation."
  - "Employee dashboard should assume the guard already blocked deleted-account access instead of showing stale-session fallback warnings."

requirements-completed: [CRUD-04, RBAC-03, RBAC-04]

duration: 0 min
completed: 2026-03-08
---

# Phase 19 Plan 01: Auth Session Revalidation & Identity Consistency Summary

**Shared auth guard now rechecks live users and employee linkage each request so deleted or inactive employee sessions are forced back to login while valid wrong-role sessions still redirect normally.**

## Performance

- **Duration:** 0 min
- **Started:** 2026-03-08T14:47:21.982Z
- **Completed:** 2026-03-08T14:47:21.982Z
- **Tasks:** 1
- **Files modified:** 3

## Accomplishments
- Added live `users` row revalidation and `is_active` checking inside `cekLogin()`.
- Added employee `karyawan` linkage validation inside `cekRole('employee')` before employee pages render.
- Removed employee dashboard dependence on stale-session warning messages as the main protection path.
- Replaced Phase 19 placeholder smoke coverage with real stale-session and redirect checks.

## Task Commits

Each task was committed atomically:

1. **Task 1: Revalidate live session state on every protected request** - `f4d172a` (feat)

## Files Created/Modified
- `includes/auth-guard.php` - Menambahkan helper redirect/logout test seam, query live `users`, dan validasi row `karyawan` untuk role employee.
- `employee/dashboard.php` - Tetap guard-first dan menghapus fallback stale-session yang sebelumnya jadi perlindungan utama.
- `tests/phase19_auth_session_smoke.php` - Menjalankan smoke check untuk missing user, inactive user, missing employee link, valid employee session, dan wrong-role redirect.

## Decisions Made
- Pusatkan semua penolakan stale session di `includes/auth-guard.php` supaya delete cascade dan akun inactive otomatis tertangani di semua halaman protected.
- Pertahankan otoritas role dari session untuk redirect wrong-role agar flow HR ke dashboard HR dan employee ke dashboard employee tidak berubah.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
None.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Shared guard now closes the delete-cascade stale-session gap for employee pages.
- Ready for `19-02-PLAN.md` to handle persistent identity hydration and the remaining Phase 19 checklist work.

## Self-Check: PASSED
- FOUND: `.planning/phases/19-auth-session-revalidation-identity-consistency/19-01-SUMMARY.md`
- FOUND COMMIT: `f4d172a`
