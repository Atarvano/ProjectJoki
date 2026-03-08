---
phase: 21-beginner-crud-structure-foundation
plan: 05
subsystem: ui
tags: [php, mysqli, hr-reports, route-rename, navigation, smoke-tests]
requires:
  - phase: 21-02
    provides: final English HR employee routes reused by report links
  - phase: 21-01
    provides: grouped layout includes used by the shared navigation files
provides:
  - final English HR reports route with matching logic and view files
  - laporan bridge file that forwards old links to reports.php
  - shared HR navigation and smoke checks aligned to reports.php and employee-detail.php
affects: [phase-22, phase-24, hr-navigation, reports-page, smoke-tests]
tech-stack:
  added: []
  patterns: [thin protected route files, page-owned report logic files, mostly-markup report view files]
key-files:
  created: [hr/reports.php, hr/logic/reports.php, hr/views/reports.php]
  modified: [hr/laporan.php, hr/dashboard.php, includes/layout/dashboard-sidebar.php, includes/layout/footer.php, tests/phase18_data_wiring_smoke.php, tests/phase21_structure_smoke.php]
key-decisions:
  - "Use reports.php as the final visible HR report route while keeping laporan.php only as a redirect bridge during rollout."
  - "Keep report SQL and cuti calculation in hr/logic/reports.php so the new route stays thin and beginner-readable."
patterns-established:
  - "HR report pages follow the same auth -> db -> logic -> layout recipe as the other renamed HR pages."
  - "Shared navigation should point to final English HR route names even while old Indonesian filenames still exist as bridges."
requirements-completed: [STRU-01, STRU-02, STRU-03]
duration: 1 min
completed: 2026-03-08
---

# Phase 21 Plan 05: HR Reports Route Cleanup Summary

**HR reports now load through a final `reports.php` route with matching logic/view files, while shared navigation and smoke checks point to the English report and employee routes.**

## Performance

- **Duration:** 1 min
- **Started:** 2026-03-08T19:10:50.000Z
- **Completed:** 2026-03-08T19:12:10.000Z
- **Tasks:** 2
- **Files modified:** 9

## Accomplishments
- Split the old mixed `hr/laporan.php` page into `hr/reports.php`, `hr/logic/reports.php`, and `hr/views/reports.php`.
- Kept the old Indonesian filename as a tiny redirect bridge so older links still land on the final report route.
- Updated HR dashboard links, grouped sidebar/footer navigation, and smoke coverage so the touched app tree now points to `reports.php`, `employees.php`, and `employee-detail.php`.

## Task Commits

Each task was committed atomically:

1. **Task 1: Rename laporan to reports with the same route -> logic -> view recipe** - `687e5f1` (feat)
2. **Task 2: Sweep shared HR navigation and report-facing smoke checks to the final route names** - `43ee50b` (feat)

**Plan metadata:** Pending

## Files Created/Modified
- `hr/reports.php` - Thin final English HR report route using grouped auth, db, logic, and layout includes.
- `hr/logic/reports.php` - Report query loading, filter handling, cuti calculation, dashboard context, and view buffering.
- `hr/views/reports.php` - Mostly-markup HR report screen with report rows and detail links.
- `hr/laporan.php` - Tiny bridge redirect that forwards old report URLs to `/hr/reports.php`.
- `hr/dashboard.php` - HR dashboard report actions now point to `reports.php`.
- `includes/layout/dashboard-sidebar.php` - Shared HR sidebar now uses the final report route name.
- `includes/layout/footer.php` - Shared footer links now use the final report route name.
- `tests/phase18_data_wiring_smoke.php` - Report smoke now reads the split `reports.php` route family.
- `tests/phase21_structure_smoke.php` - Names smoke now explicitly proves the final `hr/reports.php` route, logic, and view files.

## Decisions Made
- Used `reports.php` as the visible final route name and left `laporan.php` as a redirect-only bridge so shared links can move immediately without breaking older entry points.
- Kept report SQL, filter parsing, and `hitungHakCuti()` work inside `hr/logic/reports.php` so the route file stays tiny and matches the beginner procedural pattern already established for other HR pages.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 - Bug] Adjusted report smoke assertions to the new route -> logic -> view split**
- **Found during:** Task 2 (Sweep shared HR navigation and report-facing smoke checks to the final route names)
- **Issue:** The existing report smoke expected SQL and detail-link markers directly inside the old single-file report page, so it failed after the planned split moved those markers into logic and view files.
- **Fix:** Updated `tests/phase18_data_wiring_smoke.php` to verify the thin route file, report logic file, and report view file separately while preserving the same behavior checks.
- **Files modified:** `tests/phase18_data_wiring_smoke.php`
- **Verification:** `php tests/phase18_data_wiring_smoke.php --group=reports && php tests/phase21_structure_smoke.php --group=names`
- **Committed in:** `43ee50b` (part of task commit)

---

**Total deviations:** 1 auto-fixed (1 bug)
**Impact on plan:** Small verification fix only. It kept smoke coverage aligned with the intended beginner split and did not expand scope.

## Issues Encountered
None

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- The HR reports screen now follows the same route naming and file split style as the main HR employee pages.
- Shared HR navigation in the touched files now reads consistently with the final English route names.
- Phase 21 still needs Plan 03 completed to finish the remaining HR action-route rename sweep.

## Self-Check: PASSED
- Found `.planning/phases/21-beginner-crud-structure-foundation/21-05-SUMMARY.md`
- Found `hr/reports.php`
- Found `hr/logic/reports.php`
- Found `hr/views/reports.php`
- Found commit `687e5f1`
- Found commit `43ee50b`
