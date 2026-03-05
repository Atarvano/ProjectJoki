# Sicuti HRD Cuti Tracker

## What This Is

Sicuti HRD Cuti Tracker adalah aplikasi web internal HR/employee untuk menghitung, menyimpan, dan meninjau hak cuti. Setelah milestone v1 (frontend demo) selesai, milestone v2 berfokus pada backend nyata berbasis native procedural PHP + MySQLi.

## Core Value

HR dapat membuat data karyawan terlebih dahulu, memprovisioning akun login, lalu karyawan login dengan session native PHP untuk melihat data cuti dengan alur yang valid.

## Current State

Milestone aktif: **v2.0 - Backend Native PHP + HR-First Onboarding** (started 2026-03-05).

Target utama v2.0:
- Koneksi DB terpusat lewat `koneksi.php` (MySQLi) di Laragon localhost.
- CRUD master karyawan oleh HR.
- Login nyata dari database + session native PHP (role `hr` / `employee`).
- Flow baru: HR buat data karyawan -> HR provisioning akun -> karyawan baru bisa login.
- Migrasi bertahap dari data session-array demo ke DB agar laporan/export konsisten.

## Current Milestone: v2.0 Backend Native PHP + HR-First Employee Onboarding

**Goal:** Mentransformasi demo v1 menjadi backend procedural PHP nyata dengan MySQLi di Laragon/XAMPP, session auth native PHP, dan alur HR-first onboarding karyawan.

**Target features:**
- HR dapat CRUD data karyawan ke database lewat `koneksi.php` + MySQLi prepared statements.
- HR membuat/mengaktifkan akun login dari data karyawan yang sudah dibuat.
- Login/logout serta guard role HR/Employee berjalan dengan session native PHP.
- Flow web baru: HR input data karyawan dulu, baru karyawan bisa login.
- Output cuti employee fokus tampilan tahun ke-6, ke-7, dan ke-8.

## Milestone History

### v1.0 (Shipped: 2026-03-04)

Delivered:
- Frontend-first leave entitlement demo (landing, role login demo, HR/employee dashboard).
- Deterministic 8-year calculator engine reused across HR and employee views.
- Session-backed report save/list/detail flow.
- Excel-compatible export via isolated PhpSpreadsheet usage.

Known debt accepted at v1 completion:
- Data layer masih campuran demo/session, belum DB canonical.
- Login masih visual-only (belum real auth).
- Route demo masih bisa diakses langsung.

## Requirements

### Validated

- ✓ Seluruh requirement v1.0 terpenuhi (archive ada di `.planning/milestones/v1.0-REQUIREMENTS.md`).

### Active (v2.0)

Lihat detail lengkap di `.planning/REQUIREMENTS.md`.

Fokus aktif:
- DATA-01..03 (fondasi DB + koneksi + migration repeatable)
- EMPCRUD-01..04 (CRUD karyawan oleh HR)
- FLOW-01..03 (HR-first provisioning)
- AUTH-01..04 (login/session/logout/role guard)
- SEC-01..02 (password hash + prepared statements)

### Out of Scope

- Refactor ke framework/OOP (Laravel/class architecture).
- Open self-signup karyawan.
- Migrasi token/JWT auth.
- Redesign UI besar; visual bukan prioritas milestone ini.

## Constraints

- **Arsitektur:** Native procedural PHP (tanpa OOP app architecture).
- **Database API:** MySQLi procedural via `koneksi.php`.
- **Environment:** Laragon localhost (harus tetap kompatibel XAMPP lokal).
- **Security baseline:** `password_hash()` / `password_verify()`, prepared statements untuk input user.
- **Flow bisnis utama:** HR-first onboarding, bukan employee self-registration.

## Implementation References (User-Provided)

- https://github.com/suryamsj/tutorial-crud-php-native
- https://github.com/thexdev/php-native-crud
- https://github.com/chapagain/crud-php-simple
- https://www.codepolitan.com/blog/tutorial-membuat-crud-php-dengan-mysql-59897c72d8470/

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Mulai milestone v2.0 dengan backend foundation dulu | Menghindari split-brain data dan rewrite yang berisiko | Active |
| Gunakan satu koneksi `koneksi.php` berbasis MySQLi | Menjaga konsistensi procedural native PHP | Active |
| Terapkan flow HR-first provisioning | Sesuai kebutuhan: HR buat data karyawan dulu baru login aktif | Active |
| Pertahankan engine kalkulasi cuti existing | Menjaga parity output saat backend dimigrasikan | Active |
| Session login wajib native PHP | Sesuai constraint proyek dan cocok untuk server-rendered app | Active |

---
*Last updated: 2026-03-05 (milestone v2.0 initialized)*
