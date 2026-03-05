---
phase: 16-employee-crud-hr-navigation
plan: 02
subsystem: api
tags: [php, mysqli, crud, hr, validation]

requires:
  - phase: 16-employee-crud-hr-navigation
    provides: HR sidebar nav and employee list page as CRUD entry point
provides:
  - Dedicated add employee page with 9-field form, server validation, and PRG flash redirect
  - Dedicated edit employee page with prefilled values and NIK uniqueness check excluding current employee
  - Consistent HR-friendly Indonesian validation feedback (summary + inline) for add/edit flows
affects: [phase-16-plan-03, employee-crud, provisioning-flow]

tech-stack:
  added: []
  patterns: [single-file procedural PHP form handler, MySQLi prepared INSERT/UPDATE, PRG with session flash]

key-files:
  created:
    - hr/karyawan-tambah.php
    - hr/karyawan-edit.php
  modified: []

key-decisions:
  - "Keep add and edit as separate standalone procedural PHP pages for beginner readability"
  - "Reuse one validation UX pattern (top summary + inline errors) in both pages for HR consistency"

patterns-established:
  - "Employee form pages do POST processing at top then render through shared dashboard layout"
  - "Edit uniqueness check uses WHERE nik = ? AND id != ? so unchanged NIK remains valid"

requirements-completed: [CRUD-01, CRUD-03, CRUD-05]

duration: 6 min
completed: 2026-03-05
---

# Phase 16 Plan 02: Employee CRUD HR Navigation Summary

**HR sekarang bisa menambah dan mengedit data karyawan lewat dua halaman form terpisah dengan validasi server-side jelas, prefill data edit, serta redirect PRG ke daftar karyawan.**

## Performance

- **Duration:** 6 min
- **Started:** 2026-03-05T10:37:00Z
- **Completed:** 2026-03-05T10:42:55Z
- **Tasks:** 2
- **Files modified:** 2

## Accomplishments
- Membuat `/hr/karyawan-tambah.php` dengan 9 field karyawan, validasi required, validasi email `filter_var`, cek NIK unik, dan INSERT prepared statement.
- Membuat `/hr/karyawan-edit.php` yang wajib membawa id valid, memuat data lama sebagai nilai awal form, dan UPDATE prepared statement.
- Menjaga UX validasi ramah HR: alert ringkasan di atas, error per field, helper text format input, plus flash sukses setelah redirect ke `/hr/karyawan.php`.

## Task Commits

Each task was committed atomically:

1. **Task 1: Build separate add page with server validation and PRG flash** - `f647654` (feat)
2. **Task 2: Build separate edit page with prefilled form and uniqueness-excluding-self validation** - `5537cb2` (feat)

## Files Created/Modified
- `hr/karyawan-tambah.php` - Halaman tambah karyawan dengan validasi server-side dan PRG flash.
- `hr/karyawan-edit.php` - Halaman edit karyawan prefilled dengan validasi unik NIK yang mengecualikan id saat ini.

## Decisions Made
- Menjaga flow tetap sederhana: satu file per halaman CRUD (tambah/edit), proses POST di atas, rendering form di bawah.
- Menyamakan gaya pesan validasi tambah dan edit agar HR lebih mudah memahami dan memperbaiki input.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
None.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Add/edit flow untuk data karyawan sudah siap dipakai oleh list dan detail flow.
- Siap lanjut ke `16-03-PLAN.md` untuk detail page dan hard delete endpoint.

## Self-Check: PASSED
- FOUND: `.planning/phases/16-employee-crud-hr-navigation/16-02-SUMMARY.md`
- FOUND: `hr/karyawan-tambah.php`
- FOUND: `hr/karyawan-edit.php`
- FOUND COMMIT: `f647654`
- FOUND COMMIT: `5537cb2`
