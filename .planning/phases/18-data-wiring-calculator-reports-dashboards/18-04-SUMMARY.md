---
phase: 18-data-wiring-calculator-reports-dashboards
plan: 04
subsystem: ui
tags: [php, mysqli, dashboard, leave-calculator, employee]
requires:
  - phase: 17-account-provisioning
    provides: provisioned employee accounts linked to karyawan records
provides:
  - HR dashboard stat cards backed by live karyawan and users queries
  - Current-year leave-ready summary using Tersedia and Menunggu statuses
  - Employee dashboard focused on years 6, 7, and 8 for the logged-in account
affects: [phase-18, dashboards, employee-view, hr-view]
tech-stack:
  added: []
  patterns: [page-level mysqli counts, live leave-status loop, session-linked employee dashboard focus rows]
key-files:
  created: []
  modified: [hr/dashboard.php, employee/dashboard.php, tests/phase18_data_wiring_smoke.php]
key-decisions:
  - "HR dashboard counts total employees and provisioned employee accounts directly from karyawan and users tables."
  - "'Siap cuti tahun ini' counts employees whose current-year calculator row is Tersedia or Menunggu."
  - "Employee dashboard emphasizes only year 6, 7, and 8 rows while keeping session-linked own-data loading."
patterns-established:
  - "Dashboard stats: use simple COUNT(*) queries plus one procedural calculator loop for derived totals."
  - "Employee leave summary: filter hitungHakCuti() output by tahun_ke before rendering the main table."
requirements-completed: [CALC-03, DASH-01, DASH-05]
duration: 4 min
completed: 2026-03-06
---

# Phase 18 Plan 04: Dashboard Rewiring and Cleanup Summary

**Live HR dashboard counters from database queries plus a session-linked employee leave view focused on years 6, 7, and 8.**

## Performance

- **Duration:** 4 min
- **Started:** 2026-03-06T10:18:00Z
- **Completed:** 2026-03-06T10:22:46Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments
- Replaced HR dashboard preset/report counters with live totals from `karyawan` and provisioned `users` rows.
- Counted "siap cuti tahun ini" from current-year leave results using both `Tersedia` and `Menunggu`.
- Refocused employee dashboard output to the locked year 6/7/8 window with neutral status messaging.

## Task Commits

Each task was committed atomically:

1. **Task 1: Replace HR dashboard demo counters with real DB statistics** - `91d3cfe` (feat)
2. **Task 2: Focus employee dashboard on own year 6/7/8 leave rows** - `1165a14` (feat)

**Plan metadata:** Added in the final docs commit for summary/state updates.

## Files Created/Modified
- `hr/dashboard.php` - Loads live employee/account totals and derives current-year leave-ready counts from calculator output.
- `employee/dashboard.php` - Filters the logged-in employee leave rows to years 6/7/8 and updates helper copy/status messaging.
- `tests/phase18_data_wiring_smoke.php` - Updates dashboard smoke assertions to match live DB stats and focused employee presentation.

## Decisions Made
- HR dashboard stat cards now reflect current DB state instead of preset counters or saved-report concepts.
- The leave-ready metric follows the locked rule: current-year status `Tersedia` or `Menunggu` both count as ready.
- Employee-facing summary totals and the primary table now center on years 6, 7, and 8 rather than the full 8-year list.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- Manual DB count comparison from CLI could not be completed in this environment because `koneksi.php` could not connect to local MySQL (`target machine actively refused it`). Automated smoke checks and PHP lint still passed.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Dashboard rewiring for HR and employee pages is complete and smoke-covered.
- Remaining Phase 18 execution order still depends on Plan 18-02 and Plan 18-03 summaries being completed if they have not been executed yet.

## Self-Check: PASSED

---
*Phase: 18-data-wiring-calculator-reports-dashboards*
*Completed: 2026-03-06*
