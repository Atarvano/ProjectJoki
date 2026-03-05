---
phase: 17-account-provisioning
plan: 01
subsystem: auth
tags: [php, mysqli, provisioning, hr, password-hash]

requires:
  - phase: 16-employee-crud-hr-navigation
    provides: employee list and detail CRUD pages with account linkage status
provides:
  - Conditional "Buat Akun Login" action on HR employee list for unprovisioned rows only
  - Conditional primary provisioning CTA on employee detail page when account status is Belum dibuat
  - POST-only provisioning endpoint creating linked employee users with hashed deterministic default password
affects: [phase-17-plan-02, hr-onboarding, employee-login]

tech-stack:
  added: []
  patterns: [procedural PHP mutation endpoint, PRG flash redirect, deterministic password seed with DateTime+password_hash]

key-files:
  created:
    - hr/karyawan-provision.php
    - tests/provisioning_endpoint_test.php
  modified:
    - hr/karyawan.php
    - hr/karyawan-detail.php

key-decisions:
  - "Provisioning endpoint validates account existence server-side, not only via hidden UI action"
  - "Default password generation is strictly locked to NIK + DDMMYYYY(tanggal_lahir) and aborted on malformed dates"

patterns-established:
  - "List/detail provisioning entrypoints post to one POST-only endpoint with HR guard"
  - "Provisioning flow always redirects to /hr/karyawan.php with flash response (PRG)"

requirements-completed: [PROV-01, PROV-02, PROV-03]

duration: 7 min
completed: 2026-03-05
---

# Phase 17 Plan 01: Account Provisioning Summary

**HR kini dapat memprovision akun login karyawan dari halaman list/detail, lalu endpoint POST khusus membuat akun employee terhubung dengan password awal NIK+DDMMYYYY yang di-hash.**

## Performance

- **Duration:** 7 min
- **Started:** 2026-03-05T17:50:46Z
- **Completed:** 2026-03-05T17:57:40Z
- **Tasks:** 3
- **Files modified:** 4

## Accomplishments
- Menambahkan visibilitas aksi `Buat Akun Login` di `hr/karyawan.php` hanya untuk status `Belum dibuat`, dengan placement setelah tombol Detail dan style `btn-outline-success`.
- Menambahkan CTA provisioning utama di kartu status akun pada `hr/karyawan-detail.php` saat akun belum dibuat, serta badge non-aksi saat akun sudah ada.
- Membuat endpoint `hr/karyawan-provision.php` POST-only ber-guard HR untuk validasi id, cek duplikasi provisioning, generate password `NIK+DDMMYYYY`, hash via `password_hash()`, lalu insert `users` terhubung `karyawan_id` dengan role `employee` dan `is_active=1`.

## Task Commits

Each task was committed atomically:

1. **Task 1: Add conditional provisioning action on employee list** - `574ed31` (feat)
2. **Task 2: Add conditional primary provisioning CTA on employee detail** - `d8cc997` (feat)
3. **Task 3 (TDD RED): Create failing provisioning endpoint contract checks** - `d84ccb6` (test)
4. **Task 3 (TDD GREEN): Implement POST-only provisioning endpoint** - `d82e8b5` (feat)

## Files Created/Modified
- `hr/karyawan.php` - query list diperluas dengan LEFT JOIN users, status akun `Sudah ada/Belum dibuat`, dan tombol provisioning kondisional.
- `hr/karyawan-detail.php` - kartu status akun menampilkan CTA provisioning utama atau badge status non-aksi.
- `hr/karyawan-provision.php` - endpoint provisioning POST-only dengan guard HR, validasi input, generate+hash password, dan insert user employee.
- `tests/provisioning_endpoint_test.php` - test script ringan untuk kontrak endpoint (POST-only, rumus password, hash+insert markers, redirect target).

## Decisions Made
- Menjaga endpoint provisioning tetap idempotent secara bisnis dengan pengecekan linked user (`users.karyawan_id`) sebelum insert untuk menolak provisioning ulang.
- Menolak provisioning jika parsing `tanggal_lahir` gagal agar tidak membuat kredensial salah format yang berisiko menggagalkan login.

## Deviations from Plan

None - plan executed exactly as written.

## Authentication Gates
None.

## Issues Encountered
None.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Fondasi endpoint provisioning dan entrypoint UI sudah siap untuk Plan 17-02.
- Siap menuntaskan UX one-time credential flash dan checklist validasi manual end-to-end.

## Self-Check: PASSED
- FOUND: `.planning/phases/17-account-provisioning/17-01-SUMMARY.md`
- FOUND COMMIT: `574ed31`
- FOUND COMMIT: `d8cc997`
- FOUND COMMIT: `d84ccb6`
- FOUND COMMIT: `d82e8b5`
