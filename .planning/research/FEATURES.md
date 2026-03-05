# Feature Research

**Domain:** Native procedural PHP HR leave backend (milestone v2.0)
**Researched:** 2026-03-05
**Confidence:** MEDIUM

## Feature Landscape

### Table Stakes (Users Expect These)

Features users assume exist for this milestone. Missing these = backend dianggap belum jadi.

| Feature | Why Expected | Complexity | Notes |
|---------|--------------|------------|-------|
| DB foundation terpusat (`koneksi.php` + schema inti + setup repeatable) | Tanpa DB canonical, CRUD/auth tidak bisa dipercaya | MEDIUM | Harus jadi single source of truth untuk `employees`, `users`, `leave_reports`, `leave_report_rows`, `schema_migrations`. Dependensi langsung untuk semua fitur v2. |
| HR Employee CRUD lengkap (create/list/update + delete/nonaktif sesuai aturan bisnis) | Ini fungsi inti HR sebelum login employee diaktifkan | MEDIUM | Gunakan prepared statements untuk input user. Untuk “delete”, default aman: nonaktif/soft delete agar histori laporan tidak rusak. |
| HR-first provisioning akun (employee master dulu, akun login setelahnya) | Sesuai alur bisnis HR internal; mencegah akun liar | MEDIUM | Practical behavior: akun belum diprovision = login ditolak dengan pesan jelas. Mapping `users.employee_id` wajib valid. |
| Login real DB + password hash + native session | v1 sudah demo visual; v2 wajib autentikasi nyata | MEDIUM | `password_hash()` saat set/reset password, `password_verify()` saat login. Setelah login sukses, regenerate session ID lalu simpan `user_id` + `role` di `$_SESSION`. |
| Role guard server-side untuk area HR vs Employee + logout benar | Akses langsung URL tanpa guard adalah celah umum di PHP native | LOW | Semua endpoint sensitif wajib cek session+role sebelum render/proses. Logout harus `session_unset()` + `session_destroy()` + invalidasi cookie session. |
| Cuti output fokus tahun ke-6/7/8 dari data DB (bukan session-array demo) | Milestone minta output fokus ini dan konsistensi hasil | MEDIUM | Reuse engine kalkulator existing (v1), tapi source data dan penyimpanan report harus DB agar parity HR/employee + export konsisten. |
| Baseline CSRF untuk semua aksi state-changing (login/CRUD/provisioning) | Session auth tanpa CSRF rawan request palsu | MEDIUM | Hidden token per form + verifikasi server. Minimal untuk POST/PUT-like actions. Ini bukan “nice to have” untuk app internal yang pakai cookie session. |

### Differentiators (Competitive Advantage)

Features that improve quality/safety beyond tutorial CRUD biasa, tetap realistis untuk v2.

| Feature | Value Proposition | Complexity | Notes |
|---------|-------------------|------------|-------|
| Provisioning status lifecycle yang eksplisit (`not_provisioned` → `active` → `suspended`) | HR bisa kontrol kesiapan akun tanpa bikin data karyawan ganda | MEDIUM | Lebih kuat daripada pola “langsung insert user”. Membantu operasi HR harian (joiner, hold, resign). |
| Guard rails untuk data integrity saat nonaktifkan karyawan | Mencegah report lama putus relasi atau orphan data | MEDIUM | Nonaktifkan employee/user tanpa hapus histori `leave_reports`. Tampilkan badge status di list supaya HR tidak salah operasi. |
| Preset output “Tahun 6/7/8” sebagai view terfokus HR | Sesuai use-case domain, mempercepat review kebijakan cuti matang | LOW | Bukan engine baru; ini penyajian/filter terfokus di atas kalkulator existing + data DB canonical. |
| Flash message + error states yang operasional (bukan debug dump) | Memudahkan HR menjalankan flow provisioning tanpa ambigu | LOW | Contoh: “Akun belum diprovision”, “NIP sudah digunakan”, “Data karyawan nonaktif”. Mengurangi salah langkah user internal. |

### Anti-Features (Commonly Requested, Often Problematic)

| Feature | Why Requested | Why Problematic | Alternative |
|---------|---------------|-----------------|-------------|
| Self-signup karyawan publik | Terlihat cepat agar employee daftar sendiri | Bertentangan langsung dengan FLOW-01..03 (HR-first), membuka akun tak tervalidasi | Tetap HR-first: HR buat master employee lalu provisioning akun |
| JWT/token auth untuk app server-rendered native PHP | Dianggap “modern” | Menambah kompleksitas token lifecycle tanpa nilai besar di scope ini | Session native PHP yang di-hardening (strict mode, regenerate id, cookie flags) |
| Hard delete employee + cascade delete histori | DB terlihat “bersih” | Risiko kehilangan histori cuti/laporan dan inkonsistensi audit | Soft delete/nonaktif + pembatasan akses login |
| Refactor besar ke framework/OOP di milestone ini | “Sekalian rapikan arsitektur” | Melanggar constraint proyek, memecah fokus deliverable v2 | Pertahankan procedural; rapikan via modul file/konvensi fungsi |
| Menambah fitur deferred (SSO, full audit trail, policy engine dinamis) dalam satu fase | Terlihat strategis | Scope creep tinggi, menghambat landing fitur inti backend | Simpan sebagai backlog setelah v2 stabil |

## Feature Dependencies

```text
[DB Foundation: koneksi + schema + migrations]
    └──requires──> [HR Employee CRUD]
                        └──requires──> [HR-first provisioning akun]
                                             └──requires──> [Login DB + Session Auth]
                                                                  └──requires──> [Role Guard + Logout]

[Calculator core v1 + employee view parity]
    └──enhances──> [Cuti output fokus tahun 6/7/8 dari DB]

[Session cookie auth]
    └──requires security──> [CSRF token + secure session settings]

[Hard delete employee]
    └──conflicts──> [Konsistensi histori leave_reports/export]
```

### Dependency Notes

- **CRUD dan provisioning wajib menunggu DB foundation:** tanpa schema canonical, validasi unik, relasi `employee-user`, dan migrasi repeatable akan rapuh.
- **Login real harus sesudah provisioning flow jelas:** kalau tidak, akun yatim (tanpa employee valid) akan muncul.
- **Role guard baru efektif setelah session auth stabil:** guard berbasis role di `$_SESSION` butuh login DB yang benar.
- **Output tahun 6/7/8 bergantung data DB, bukan session demo:** agar hasil HR/employee/export tetap sinkron.
- **Hard delete konflik dengan laporan historis:** nonaktif lebih aman untuk milestone ini.

## MVP Definition

### Launch With (v2.0)

Minimum viable milestone berdasarkan requirement aktif (DATA/EMPCRUD/FLOW/AUTH/SEC).

- [ ] DB canonical + setup repeatable (`koneksi.php`, schema inti, migration marker) — fondasi semua fitur baru.
- [ ] HR CRUD karyawan + status aktif/nonaktif — inti operasi data HR.
- [ ] HR-first provisioning akun + login DB/session + role guard/logout — alur bisnis utama end-to-end.
- [ ] Migrasi output cuti fokus tahun 6/7/8 ke data DB canonical — memastikan parity hasil dan konsistensi laporan/export.
- [ ] Baseline security: password hash/verify, prepared statements, CSRF form token, session hardening dasar — menutup celah paling umum.

### Add After Validation (v2.x)

- [ ] Reset password flow terkelola HR (forced reset first login optional) — tambah setelah flow provisioning stabil dipakai.
- [ ] Pencarian/filter employee lebih kaya (status, unit, masa kerja) — tambah saat volume data mulai mengganggu UX.

### Future Consideration (v3+)

- [ ] Audit trail detail per field + login anomaly dashboard — defer karena butuh desain data observability yang matang.
- [ ] SSO/OIDC integration — defer sesuai REQUIREMENTS future (SSO-01), bukan kebutuhan MVP backend native.

## Feature Prioritization Matrix

| Feature | User Value | Implementation Cost | Priority |
|---------|------------|---------------------|----------|
| DB foundation + repeatable setup | HIGH | MEDIUM | P1 |
| HR employee CRUD | HIGH | MEDIUM | P1 |
| HR-first provisioning akun | HIGH | MEDIUM | P1 |
| Login DB + session + role guard/logout | HIGH | MEDIUM | P1 |
| Output cuti fokus tahun 6/7/8 via DB | HIGH | MEDIUM | P1 |
| CSRF + session hardening baseline | HIGH | MEDIUM | P1 |
| Advanced filter/search employee | MEDIUM | LOW | P2 |
| Forced password reset first login | MEDIUM | MEDIUM | P2 |
| SSO/OIDC | LOW (for now) | HIGH | P3 |

**Priority key:**
- P1: Must have for launch
- P2: Should have, add when possible
- P3: Nice to have, future consideration

## Competitor Feature Analysis

| Feature | Competitor A (crud-php-simple) | Competitor B (tutorial-crud-php-native) | Our Approach |
|---------|-------------------------------|------------------------------------------|--------------|
| CRUD employee baseline | Ada CRUD sederhana file-per-action | Ada CRUD tutorial + UI scaffolding | Ambil pola sederhana, tambah constraint domain HR (status/nonaktif + relasi user) |
| Auth + role guard | Umumnya minim/tidak fokus auth production | Ada login/register tutorial-level | Wajib auth DB production-grade untuk internal HR-first (tanpa self-signup) |
| Security baseline | Sering belum komprehensif (contoh tutorial basic) | Fokus pembelajaran, bukan hardening penuh | Wajib `password_hash/verify`, prepared statements, CSRF token, session hardening |
| Provisioning flow HR-first | Umumnya tidak ada; user flow generic | Umumnya login/register generic | Jadikan fitur inti: employee master dulu, akun aktif belakangan oleh HR |
| Fokus output cuti tahun 6/7/8 | Tidak domain-spesifik | Tidak domain-spesifik | Domain-specific differentiator dengan reuse kalkulator v1 + data DB canonical |

## Sources

- PHP Manual — `password_hash()` (HIGH): https://www.php.net/manual/en/function.password-hash.php
- PHP Manual — `password_verify()` (HIGH): https://www.php.net/manual/en/function.password-verify.php
- PHP Manual — Session Handling (HIGH): https://www.php.net/manual/en/book.session.php
- PHP Manual — Session Management Basics (HIGH): https://www.php.net/manual/en/features.session.security.management.php
- PHP Manual — Securing Session INI Settings (HIGH): https://www.php.net/manual/en/session.security.ini.php
- PHP Manual — `session_regenerate_id()` (HIGH): https://www.php.net/manual/en/function.session-regenerate-id.php
- PHP Manual — MySQLi Prepared Statements (HIGH): https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php
- OWASP CSRF Prevention Cheat Sheet (MEDIUM-HIGH): https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
- Reference ecosystem examples (LOW-MEDIUM, tutorial/repo patterns, bukan standard resmi):
  - https://github.com/chapagain/crud-php-simple
  - https://github.com/suryamsj/tutorial-crud-php-native
  - https://github.com/thexdev/php-native-crud
  - https://www.codepolitan.com/blog/tutorial-membuat-crud-php-dengan-mysql-59897c72d8470/

---
*Feature research for: Sicuti HRD Cuti Tracker v2.0 backend milestone*
*Researched: 2026-03-05*