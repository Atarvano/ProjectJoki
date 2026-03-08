---
phase: 19-auth-session-revalidation-identity-consistency
plan: 00
subsystem: auth
tags: [php, sessions, auth-guard, smoke-test, validation]

requires:
  - phase: 18-data-wiring-calculator-reports-dashboards
    provides: wave-based smoke-test pattern and validation contract structure
provides:
  - phase 19 smoke scaffold for stale-session and identity checks
  - cli-safe auth-guard redirect seam for procedural smoke scripts
  - wave 0 validation baseline for later phase 19 plans
affects: [phase-19-plans, auth, employee-dashboard, hr-delete-flow]

tech-stack:
  added: [none]
  patterns: [plain-php-smoke-script, cli-safe-redirect-seam, wave-validation-baseline]

key-files:
  created:
    - tests/phase19_auth_session_smoke.php
    - .planning/phases/19-auth-session-revalidation-identity-consistency/19-VALIDATION.md
  modified:
    - includes/auth-guard.php

key-decisions:
  - "Use AUTH_GUARD_TEST_MODE as one tiny procedural seam so smoke tests can inspect redirect outcomes without real headers."
  - "Keep Phase 19 Wave 0 coverage in one plain PHP smoke script with named placeholder cases before live revalidation logic is added."

patterns-established:
  - "Pattern 1: auth-guard may return a small array in CLI smoke mode, but keeps header() + exit behavior in normal browser flow."
  - "Pattern 2: later Phase 19 tasks should run one quick smoke command and update task 19-00-01 references instead of writing MISSING placeholders."

requirements-completed: [CRUD-04, RBAC-03, RBAC-04, DASH-02]

duration: 0 min
completed: 2026-03-08
---

# Phase 19 Plan 00: Auth Session Revalidation & Identity Consistency Summary

**Wave 0 auth verification groundwork with a Phase 19 smoke scaffold, a CLI-safe auth-guard seam, and a ready validation map for later stale-session tasks**

## Performance

- **Duration:** 0 min
- **Started:** 2026-03-08T14:42:46Z
- **Completed:** 2026-03-08T14:42:46Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments
- Added one tiny procedural redirect seam in `includes/auth-guard.php` for CLI smoke inspection.
- Created `tests/phase19_auth_session_smoke.php` with named Phase 19 stale-session and identity case markers.
- Marked Wave 0 in `19-VALIDATION.md` so later plans can reference a real command and task row.

## Task Commits

Each task was committed atomically:

1. **Task 1: Create the Phase 19 smoke scaffold and tiny guard seam** - `b100c33` (feat)
2. **Task 2: Mark Wave 0 as the verification baseline** - `b8d904d` (docs)

**Plan metadata:** pending

## Files Created/Modified
- `includes/auth-guard.php` - Added `AUTH_GUARD_TEST_MODE` seam and procedural redirect helper for CLI-safe inspection.
- `tests/phase19_auth_session_smoke.php` - Added Wave 0 smoke scaffold with named stale-session, role, and identity placeholder cases.
- `.planning/phases/19-auth-session-revalidation-identity-consistency/19-VALIDATION.md` - Added ready Wave 0 task row and quick-run baseline notes.

## Decisions Made
- Used `AUTH_GUARD_TEST_MODE` constant instead of a bigger test helper layer so the guard stays beginner-level and procedural.
- Kept the smoke coverage as one plain PHP file with placeholder case names first, so later plans can add live DB behavior without changing the test style.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- `.planning/` is git-ignored in this repo, so the validation file had to be staged with `git add -f` for the task commit.
- Windows `date` command prompted for interactive input, so PHP date output was used for timestamps instead.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Phase 19 now has a runnable smoke command: `php tests/phase19_auth_session_smoke.php`.
- Later plans can extend real revalidation behavior in `includes/auth-guard.php` without inventing new verification placeholders.
- No blocker found for `19-01-PLAN.md`.

## Self-Check: PASSED
- Found summary file on disk.
- Confirmed task commits `b100c33` and `b8d904d` exist in git history.
