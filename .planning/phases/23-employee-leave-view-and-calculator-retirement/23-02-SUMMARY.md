---
phase: 23-employee-leave-view-and-calculator-retirement
plan: 02
subsystem: ui
tags: [php, mysqli, navigation, redirect, reports, employee-self-view]
requires:
  - phase: 23-00
    provides: "Phase 23 smoke coverage for navigation cleanup and calculator retirement"
  - phase: 23-01
    provides: "Live employee self-view replacement path at /employee/dashboard.php"
provides:
  - "Shared sidebar and footer links without calculator-first destinations"
  - "HR dashboard guidance centered on reports.php -> employee-detail.php"
  - "Authenticated redirect bridge from /hr/kalkulator.php to /hr/reports.php"
affects: [phase-24-product-copy, hr-navigation, employee-dashboard, compatibility-bridges]
tech-stack:
  added: []
  patterns: ["Shared navigation points only to live destinations", "Retired HR routes stay as thin authenticated redirect bridges"]
key-files:
  created: []
  modified: [includes/layout/dashboard-sidebar.php, includes/layout/footer.php, hr/dashboard.php, hr/kalkulator.php, tests/phase21_structure_smoke.php]
key-decisions:
  - "Keep /hr/kalkulator.php in place as an authenticated redirect bridge to /hr/reports.php instead of deleting the route."
  - "Make reports.php the primary HR review path and link employee Hak Cuti Saya directly to /employee/dashboard.php."
patterns-established:
  - "Post-calculator cleanup updates shared sidebar, footer, and HR helper copy together so no dead links survive in common layouts."
  - "Legacy beginner-style bridge routes can be retired safely with guard checks plus header redirect and exit."
requirements-completed: [LEAV-03, LEAV-04]
duration: 0 min
completed: 2026-03-09
---

# Phase 23 Plan 02: Calculator Navigation Retirement Summary

**Reports-first HR leave review with direct employee self-view navigation and a silent calculator redirect bridge.**

## Performance

- **Duration:** 0 min
- **Started:** 2026-03-08T22:27:15Z
- **Completed:** 2026-03-08T22:38:06Z
- **Tasks:** 2
- **Files modified:** 5

## Accomplishments
- Removed the HR calculator entry from shared sidebar and footer navigation.
- Turned employee `Hak Cuti Saya` into a real sidebar link to `/employee/dashboard.php`.
- Rewrote HR dashboard guidance so the main review flow is `reports.php -> employee-detail.php`.
- Retired `hr/kalkulator.php` into an authenticated redirect bridge to `/hr/reports.php`.
- Updated legacy structure smoke assertions to match the new bridge contract.

## Task Commits

Each task was committed atomically:

1. **Task 1: Remove calculator-first links from shared navigation and HR guidance** - `d2a5e27` (feat)
2. **Task 2: Retire hr/kalkulator.php into a thin redirect bridge and update old structure proof** - `d769db1` (fix)

**Plan metadata:** pending docs commit

## Files Created/Modified
- `includes/layout/dashboard-sidebar.php` - Removes HR calculator nav and makes employee leave nav clickable.
- `includes/layout/footer.php` - Trims shared footer links down to live HR and employee destinations.
- `hr/dashboard.php` - Reorients helper copy and CTAs toward `reports.php -> employee-detail.php`.
- `hr/kalkulator.php` - Keeps the old route alive only as an authenticated redirect bridge.
- `tests/phase21_structure_smoke.php` - Updates structure assertions to expect the retired calculator bridge behavior.

## Decisions Made
- Kept `/hr/kalkulator.php` as a compatibility bridge instead of deleting it, so older bookmarks keep working while users are silently moved to `reports.php`.
- Made `reports.php` the clearest HR review hub and kept `/employee/dashboard.php` as the direct self-view replacement destination exposed in shared navigation.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- `php tests/phase21_structure_smoke.php` failed on a pre-existing out-of-scope assertion in `hr/logic/employee-create.php`, where the older smoke still expects a redirect back to `/hr/employees.php` while the current shipped flow returns to `employee-detail.php`. I logged this in `deferred-items.md` and used the task-scoped `includes` and `bridges` groups required for this calculator-retirement work.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Phase 23 calculator retirement is complete, and shared navigation now points only at live replacement destinations.
- Phase 24 can focus on final product and dashboard copy cleanup without carrying old calculator-first links.

## Self-Check: PASSED
- Found summary file: `.planning/phases/23-employee-leave-view-and-calculator-retirement/23-02-SUMMARY.md`
- Found task commit: `d2a5e27`
- Found task commit: `d769db1`

---
*Phase: 23-employee-leave-view-and-calculator-retirement*
*Completed: 2026-03-09*
