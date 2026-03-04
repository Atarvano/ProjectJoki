# Phase 12 Verification Evidence

## Locked 6-Moment Checklist

| # | Moment | Description | Requirement | Status |
|---|--------|-------------|-------------|--------|
| 1 | Seeded list visible | Open `hr/laporan.php` in a fresh session; verify 3 preset rows (Budi Santoso, Siti Rahayu, Ahmad Fauzi) are visible and Export button is enabled. | RPT-02, RPT-03 | Pending |
| 2 | Save success | Save one new calculator result from `hr/kalkulator.php`, return to `hr/laporan.php`, verify new row appears with existing data. | RPT-01, RPT-02 | Pending |
| 3 | Duplicate update overwrite | Save same name + join year again from kalkulator, verify laporan updates existing row (no duplicate entry). | RPT-01, RPT-02 | Pending |
| 4 | Reset confirmation + resulting empty state | Click Reset and confirm updated destructive copy; verify zero rows, empty-state message shown, Export disabled with tooltip, and Reset button hidden. | RPT-01, RPT-02, RPT-03 | Pending |
| 5 | Blocked empty export message | With empty list, open `hr/export.php`; verify redirect back to `hr/laporan.php` with info flash: “Tidak ada data untuk diekspor. Simpan hasil kalkulator terlebih dahulu.” | RPT-03 | Pending |
| 6 | Non-empty export file proof | Save at least one report, verify Export enabled, export XLSX, and confirm Ringkasan rows exactly match laporan rows. | RPT-02, RPT-03 | Pending |

## Requirement Coverage

### RPT-01
- Covered by moments: **2, 3, 4**
- Focus: Save/update/reset behavior and report-list data integrity.

### RPT-02
- Covered by moments: **1, 2, 3, 4, 6**
- Focus: Consolidated report table visibility, consistency, and post-action state correctness.

### RPT-03
- Covered by moments: **1, 4, 5, 6**
- Focus: Export availability rules, empty-export guard behavior, and exported-data parity.

## Evidence Notes (to fill during verification)

- Screenshot paths or links:
  - Moment 1:
  - Moment 2:
  - Moment 3:
  - Moment 4:
  - Moment 5:
  - Moment 6:

- Additional observations:
  -
