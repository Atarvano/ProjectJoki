---
phase: 18-data-wiring-calculator-reports-dashboards
plan: 03
subsystem: api
tags: [php, mysqli, phpspreadsheet, reports, export]
requires:
  - phase: 18-01
    provides: grouped smoke checks and manual verification checklist for reports/export rewiring
provides:
  - Live laporan page built from current karyawan rows
  - XLSX export generated from the same live leave-calculation loop
  - Removal of obsolete session-based report helper
affects: [phase-18, reports, export, dashboards]
tech-stack:
  added: []
  patterns: [page-level mysqli query + foreach leave calculation, live report/export parity via shared row shape]
key-files:
  created: []
  modified: [hr/laporan.php, hr/export.php, tests/phase18_data_wiring_smoke.php]
key-decisions:
  - "Laporan dan export sama-sama membangun row live langsung dari query karyawan terurut NIK agar output halaman dan XLSX tetap sinkron."
  - "Filter status laporan diteruskan ke export supaya hasil unduhan mengikuti recap yang sedang dilihat HR."
patterns-established:
  - "Reports pages query karyawan directly and derive join year from tanggal_bergabung before calling hitungHakCuti()."
  - "Session-era reporting helpers are removed once page-level live DB wiring is in place."
requirements-completed: [RPT-01, RPT-02, RPT-03, RPT-04, CALC-04]
duration: 5 min
completed: 2026-03-06
---

# Phase 18 Plan 03: Live reports and export wiring summary

**Live laporan dan export XLSX kini dihitung langsung dari data karyawan aktif dengan perhitungan hak cuti real-time dan link kembali ke detail karyawan.**

## Performance

- **Duration:** 5 min
- **Started:** 2026-03-06T10:18:00Z
- **Completed:** 2026-03-06T10:22:50Z
- **Tasks:** 2
- **Files modified:** 4

## Accomplishments
- Mengganti `hr/laporan.php` dari session report ke query live `karyawan` berurutan NIK/nama.
- Menjaga UI tabel laporan dan filter status sambil mengarahkan aksi detail kembali ke `hr/karyawan-detail.php`.
- Mengganti `hr/export.php` agar menulis XLSX dari row live yang sama dan menghapus helper `includes/reports-data.php`.

## Task Commits

Each task was committed atomically:

1. **Task 1: Rebuild laporan.php from live employee rows** - `c1dc8da` (feat)
2. **Task 2: Rewire Excel export and remove obsolete session report helper** - `6130897` (feat)

**Plan metadata:** pending

## Files Created/Modified
- `hr/laporan.php` - Rekap live semua karyawan dengan filter status tahun berjalan dan link ke detail karyawan.
- `hr/export.php` - Export multi-sheet XLSX dari query live karyawan dan hasil `hitungHakCuti()`.
- `includes/reports-data.php` - Dihapus agar helper report berbasis session tidak lagi bisa dipakai.
- `tests/phase18_data_wiring_smoke.php` - Marker smoke reports diperbarui untuk memverifikasi rewiring live DB.

## Decisions Made
- Laporan dan export sama-sama memakai query `karyawan` terurut `ORDER BY nik ASC, nama ASC` agar row count dan urutan data konsisten.
- Parameter `filter_status` diteruskan ke tombol export supaya file XLSX bisa mengikuti recap yang sedang difilter di halaman laporan.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Updated reports smoke markers for the new live DB implementation**
- **Found during:** Task 2 (Rewire Excel export and remove obsolete session report helper)
- **Issue:** Existing smoke test still asserted the old `reports-data.php` and `getReports()` session markers, so verification would fail after correct rewiring.
- **Fix:** Replaced those assertions with live DB markers for `koneksi.php`, `ORDER BY nik ASC, nama ASC`, `karyawan-detail.php?id=`, `hitungHakCuti`, and file deletion checks.
- **Files modified:** `tests/phase18_data_wiring_smoke.php`
- **Verification:** `php tests/phase18_data_wiring_smoke.php --group=reports`
- **Committed in:** `6130897`

---

**Total deviations:** 1 auto-fixed (1 blocking)
**Impact on plan:** Verification contract now matches the intended live DB behavior. No scope creep.

## Issues Encountered
- None

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Reports and export are now aligned on the same live employee data source.
- Ready for Plan 18-04 to replace dashboard demo counters with live database statistics.

## Self-Check: PASSED
- Found `.planning/phases/18-data-wiring-calculator-reports-dashboards/18-03-SUMMARY.md` on disk.
- Verified key runtime files `hr/laporan.php` and `hr/export.php` exist, and `includes/reports-data.php` is removed.
- Verified task commits `c1dc8da` and `6130897` exist in git history.

---
*Phase: 18-data-wiring-calculator-reports-dashboards*
*Completed: 2026-03-06*
