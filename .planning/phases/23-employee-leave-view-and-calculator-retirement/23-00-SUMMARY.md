---
phase: 23-employee-leave-view-and-calculator-retirement
plan: 00
subsystem: testing
tags: [php, smoke-test, employee-self-view, calculator-retirement, validation]
requires:
  - phase: 21-beginner-crud-structure-foundation
    provides: grouped include contract and employee route -> logic -> view pattern
  - phase: 22-hr-detail-first-crud-flow
    provides: years 6-8 leave focus and detail-first replacement path
provides:
  - procedural smoke coverage for employee self-view, navigation cleanup, calculator retirement, and missing-data states
  - named smoke groups later Phase 23 plans can run directly
  - validation map aligned to the final Phase 23 smoke commands
affects: [phase-23, employee-dashboard, dashboard-sidebar, footer, hr-kalkulator, smoke-tests]
tech-stack:
  added: []
  patterns: [plain procedural php cli smoke script, string-based route contract assertions, wave-0 validation map reuse]
key-files:
  created: [tests/phase23_employee_leave_retirement_smoke.php, .planning/phases/23-employee-leave-view-and-calculator-retirement/23-VALIDATION.md]
  modified: [tests/phase23_employee_leave_retirement_smoke.php, .planning/phases/23-employee-leave-view-and-calculator-retirement/23-VALIDATION.md]
key-decisions:
  - "Keep Phase 23 verification as one cheap procedural PHP smoke file with named groups instead of adding PHPUnit or framework tooling."
  - "Add a dedicated missing-data smoke group because Phase 23 must keep employees on their own page with brief HR-contact warnings."
patterns-established:
  - "Phase smoke scripts stay beginner-readable: parse --group, load files, assert strings, print PASS or FAIL."
  - "Phase 23 plans should reuse employee-self-view, navigation, retirement, and missing-data groups instead of inventing ad-hoc checks."
requirements-completed: [CRUD-03, LEAV-02, LEAV-03, LEAV-04]
duration: 1 min
completed: 2026-03-08
---

# Phase 23 Plan 00: Employee leave and calculator retirement smoke baseline Summary

**Procedural Phase 23 smoke coverage for employee self-view leave checks, missing-data warnings, navigation cleanup, and calculator-route retirement.**

## Performance

- **Duration:** 1 min
- **Started:** 2026-03-08T22:19:00Z
- **Completed:** 2026-03-08T22:20:10Z
- **Tasks:** 2
- **Files modified:** 2

## Accomplishments
- Added a new Phase 23 smoke script in the same beginner-style procedural PHP pattern used in earlier phases.
- Split the checks into reusable `employee-self-view`, `navigation`, `retirement`, and `missing-data` groups plus `all`.
- Updated the Phase 23 validation map so later plans can point at real smoke commands instead of Wave 0 placeholders.

## Task Commits

Each task was committed atomically:

1. **Task 1: Create the Phase 23 smoke scaffold with final named groups** - `db8d4e3` (test)
2. **Task 2: Align the phase validation contract to the new smoke groups** - `d68ee9a` (docs)

**Plan metadata:** pending

## Files Created/Modified
- `tests/phase23_employee_leave_retirement_smoke.php` - Cheap CLI smoke script that checks the intended employee self-view, navigation cleanup, redirect retirement, and missing-data contract.
- `.planning/phases/23-employee-leave-view-and-calculator-retirement/23-VALIDATION.md` - Validation map updated to point every Phase 23 task at the real smoke groups.

## Decisions Made
- Kept the smoke file fully procedural and file-content-based so it matches the repo's beginner PHP style and can run with one plain `php` command.
- Added `missing-data` as a first-class smoke group because Phase 23 explicitly needs inline warning coverage for missing employee rows and invalid join dates.
- Let the smoke script fail against current unfinished Phase 23 code so later rewiring work has a loud safety net instead of a placeholder test.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- The new smoke suite intentionally fails on current navigation, auth-guard, and calculator-route code because the replacement behavior is not implemented yet. This is expected for the Wave 0 baseline and confirms the checks are guarding real Phase 23 gaps.
- `.planning/` is ignored by git in this repository, so the task-2 validation file had to be staged with `git add -f` to preserve the required per-task atomic commit.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Phase 23 now has a concrete smoke command: `php tests/phase23_employee_leave_retirement_smoke.php`.
- Later plans can target `--group=employee-self-view`, `--group=missing-data`, `--group=navigation`, or `--group=retirement` depending on rewiring scope.
- Current red failures clearly point to the next implementation targets: employee sidebar cleanup, missing-data guard relaxation, and redirect-only calculator retirement.

## Self-Check: PASSED
- FOUND: `.planning/phases/23-employee-leave-view-and-calculator-retirement/23-00-SUMMARY.md`
- FOUND: `db8d4e3`
- FOUND: `d68ee9a`
