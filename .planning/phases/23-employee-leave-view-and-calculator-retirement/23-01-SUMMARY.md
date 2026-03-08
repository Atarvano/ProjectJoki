---
phase: 23-employee-leave-view-and-calculator-retirement
plan: 01
subsystem: ui
tags: [php, mysqli, employee-dashboard, leave-self-view, auth-guard, smoke-tests]
requires:
  - phase: 23-00
    provides: phase 23 smoke groups for employee self-view and missing-data behavior
  - phase: 22-hr-detail-first-crud-flow
    provides: years 6-8 leave focus and detail-first leave review pattern
provides:
  - employee self-view dashboard that shows years 6-8 leave entitlement directly from the logged-in session
  - employee guard behavior that allows missing-row warning states without weakening role or inactive-user protection
  - updated auth and phase 23 smoke coverage for missing-row and invalid join-date warning behavior
affects: [phase-23-navigation, calculator-retirement, employee-self-view, auth-guard, smoke-tests]
tech-stack:
  added: []
  patterns: [narrow self-view warning exception in guard, session-linked employee dashboard logic, dominant years-6-8 leave table]
key-files:
  created: []
  modified:
    - includes/auth/auth-guard.php
    - tests/phase19_auth_session_smoke.php
    - tests/phase23_employee_leave_retirement_smoke.php
    - employee/logic/dashboard.php
    - employee/views/dashboard.php
key-decisions:
  - "Allow missing linked employee rows to reach the employee self-view shell so the page can render an inline HR-contact warning instead of forcing logout."
  - "Keep the employee dashboard route in place and reshape its logic/view so the years 6-8 leave table becomes the main self-view content."
patterns-established:
  - "Employee self-view warning states stay on-page for missing employee data and invalid join dates instead of redirecting away."
  - "Guard relaxations must be documented as self-view-specific behavior and backed by smoke coverage so broader role protection stays intact."
requirements-completed: [CRUD-03, LEAV-02]
duration: 2 min
completed: 2026-03-08
---

# Phase 23 Plan 01: Employee self-view warning-state summary

**Employee self-view now shows a direct years 6-8 leave table from the logged-in session, while missing employee data stays on-page with brief HR guidance instead of forced logout.**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-08T22:25:04Z
- **Completed:** 2026-03-08T22:27:15Z
- **Tasks:** 2
- **Files modified:** 5

## Accomplishments
- Relaxed the employee guard only for the narrow missing-row self-view case so valid employee sessions can reach warning states.
- Rebuilt the employee dashboard around the years 6-8 leave table with minimal identity context above it.
- Kept missing employee rows and invalid join dates on the page shell with plain HR-contact messaging.

## Task Commits

Each task was committed atomically:

1. **Task 1: Relax the employee guard just enough for self-view warning states** - `5afdf25` (feat)
2. **Task 2: Rebuild the employee dashboard around a dominant years 6-8 self-view table** - `7f66e10` (feat)

**Plan metadata:** pending until state/docs commit

## Files Created/Modified
- `includes/auth/auth-guard.php` - Keeps role and active-user protection intact while allowing the narrow self-view warning-state exception for missing employee rows.
- `tests/phase19_auth_session_smoke.php` - Updates auth smoke expectations for the new self-view warning-state rule.
- `tests/phase23_employee_leave_retirement_smoke.php` - Verifies missing-data behavior without falsely treating unrelated logout paths as regressions.
- `employee/logic/dashboard.php` - Loads the session-linked employee record, formats join-date context, and prepares warning states plus years 6-8 leave rows.
- `employee/views/dashboard.php` - Makes the years 6-8 leave table the dominant block and keeps HR guidance brief and visible.

## Decisions Made
- Allowed authenticated employee sessions with a missing linked `karyawan` row to stay alive long enough for `/employee/dashboard.php` to show its warning state.
- Kept the existing `employee/dashboard.php` route and its route -> logic -> view structure instead of introducing a new employee leave page.
- Reduced employee dashboard chrome so the leave table leads the page and profile context stays minimal.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Tightened the missing-data smoke assertion to inspect only the missing-row branch**
- **Found during:** Task 1 (Relax the employee guard just enough for self-view warning states)
- **Issue:** The Phase 23 missing-data smoke test failed by scanning the whole auth guard for generic logout lines, which incorrectly flagged unrelated logout handling that should still exist.
- **Fix:** Added a small branch-scoped assertion helper and updated the smoke check to verify only the missing employee-row path.
- **Files modified:** `tests/phase23_employee_leave_retirement_smoke.php`
- **Verification:** `php tests/phase23_employee_leave_retirement_smoke.php --group=missing-data`
- **Committed in:** `5afdf25` (part of task commit)

---

**Total deviations:** 1 auto-fixed (1 blocking)
**Impact on plan:** Small verification fix only. It removed a false negative and kept the intended guard protections and warning-state behavior intact.

## Issues Encountered
- The first Phase 23 missing-data smoke check used an overly broad string assertion against `auth-guard.php`; narrowing it to the relevant branch resolved the false failure cleanly.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Employee self-view replacement path is now live on `/employee/dashboard.php`.
- Phase 23 can continue with shared navigation cleanup and calculator route retirement without leaving employees dependent on the old calculator flow.

## Self-Check: PASSED
- Found `.planning/phases/23-employee-leave-view-and-calculator-retirement/23-01-SUMMARY.md` on disk.
- Found `includes/auth/auth-guard.php`.
- Found `employee/logic/dashboard.php`.
- Found `employee/views/dashboard.php`.
- Verified task commits `5afdf25` and `7f66e10` exist in git history.

---
*Phase: 23-employee-leave-view-and-calculator-retirement*
*Completed: 2026-03-08*
