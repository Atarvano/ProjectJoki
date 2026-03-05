# Roadmap: Sicuti HRD Cuti Tracker

## Milestones

- ✅ **v1.0 milestone** - shipped 2026-03-04. Archive: `.planning/milestones/v1.0-ROADMAP.md`
- 🚧 **v2.0 milestone** - in progress (started 2026-03-05). Focus: backend native procedural PHP, MySQLi CRUD, native PHP session auth/role, HR-first onboarding, dan DB bootstrap otomatis.

## Implementation Guardrails (Mandatory in Every Phase)

- Tetap **native procedural PHP** (tanpa migrasi OOP/framework).
- Tetap **MySQLi via `koneksi.php`** sebagai koneksi terpusat.
- Tetap **native PHP session** untuk auth dan role guard.
- Wajib ada **autonomous DB bootstrap**: setup database+migration dengan **satu command lokal**.

## Phases

- [ ] **Phase 14: Data Foundation & Autonomous Bootstrap** - Fondasi DB canonical siap pakai lewat satu command lokal.
- [ ] **Phase 15: Native Auth & Session Security Boundary** - Login/logout/role guard nyata berbasis DB + session native.
- [ ] **Phase 16: HR Employee Master CRUD** - HR mengelola data karyawan end-to-end di database.
- [ ] **Phase 17: HR-First Provisioning Lifecycle** - Akun karyawan hanya aktif setelah HR provisioning dari master employee.
- [ ] **Phase 18: Employee Self-View & Leave Output Integrity** - Employee hanya melihat data sendiri dan output cuti fokus Tahun 6/7/8.

## Phase Details

### Phase 14: Data Foundation & Autonomous Bootstrap
**Goal**: Fondasi database v2.0 stabil dan repeatable dengan satu koneksi MySQLi terpusat serta bootstrap otomatis satu command lokal.
**Depends on**: v1.0 shipped baseline
**Requirements**: DATA-01, DATA-02, DATA-03, DATA-04
**Success Criteria** (what must be TRUE):
  1. Operator lokal dapat menjalankan satu command untuk membuat database dan menjalankan migration tanpa phpMyAdmin/manual query.
  2. Aplikasi menggunakan `koneksi.php` sebagai satu-satunya entrypoint koneksi MySQLi.
  3. Tabel inti (`employees`, `users`, `leave_reports`, `leave_report_rows`, `schema_migrations`) tersedia konsisten setelah bootstrap.
  4. Bootstrap/migration dapat dijalankan ulang tanpa merusak state schema yang sudah benar (repeatable).
**Plans**: 2 plans

Plans:
- [ ] 14-01-PLAN.md - Build canonical MySQLi connection boundary and one-command bootstrap entrypoint.
- [ ] 14-02-PLAN.md - Add core schema migrations and harden repeatable apply-pending behavior.

### Phase 15: Native Auth & Session Security Boundary
**Goal**: Pengguna masuk/keluar aplikasi secara nyata dengan data DB, session native PHP, dan pemisahan akses role yang aman.
**Depends on**: Phase 14
**Requirements**: AUTH-01, AUTH-02, AUTH-03, AUTH-04, SEC-01, SEC-02
**Success Criteria** (what must be TRUE):
  1. Pengguna dengan kredensial valid dapat login, sementara kredensial tidak valid ditolak.
  2. Status login dan role tersimpan pada native PHP session dan dipakai lintas halaman yang dilindungi.
  3. Pengguna HR tidak dapat membuka halaman employee-only, dan employee tidak dapat membuka halaman HR-only.
  4. Logout mengakhiri sesi secara benar sehingga halaman terlindungi tidak dapat diakses tanpa login ulang.
  5. Kredensial tersimpan aman (`password_hash`/`password_verify`) dan query input-user pada alur auth memakai prepared statements MySQLi.
**Plans**: TBD

### Phase 16: HR Employee Master CRUD
**Goal**: HR memiliki kontrol penuh terhadap data master karyawan sebagai sumber kebenaran onboarding.
**Depends on**: Phase 15
**Requirements**: EMPCRUD-01, EMPCRUD-02, EMPCRUD-03, EMPCRUD-04
**Success Criteria** (what must be TRUE):
  1. HR dapat menambah data karyawan baru ke database.
  2. HR dapat melihat daftar karyawan dari database dengan data terbaru.
  3. HR dapat mengubah data karyawan yang sudah ada dan perubahan terlihat pada daftar/detail.
  4. HR dapat menghapus/nonaktifkan data karyawan sesuai aturan bisnis yang ditetapkan aplikasi.
**Plans**: TBD

### Phase 17: HR-First Provisioning Lifecycle
**Goal**: Alur bisnis HR-first berjalan end-to-end: master employee dulu, provisioning akun login sesudahnya.
**Depends on**: Phase 15, Phase 16
**Requirements**: FLOW-01, FLOW-02, FLOW-03
**Success Criteria** (what must be TRUE):
  1. HR hanya bisa membuat/mengaktifkan akun login untuk employee yang sudah ada di master data.
  2. Employee yang belum diprovisioning HR tidak bisa login ke dashboard employee.
  3. Setelah HR provisioning berhasil, employee yang bersangkutan dapat login sesuai hak akses role.
**Plans**: TBD

### Phase 18: Employee Self-View & Leave Output Integrity
**Goal**: Employee terautentikasi hanya dapat mengakses data milik sendiri dan melihat output entitlement fokus Tahun ke-6/7/8.
**Depends on**: Phase 15, Phase 17
**Requirements**: EMPVIEW-01, EMPVIEW-02, LEAVE-01
**Success Criteria** (what must be TRUE):
  1. Setelah login, employee hanya melihat dashboard dan data yang terkait akunnya sendiri.
  2. Aplikasi menolak akses ke data employee lain meskipun URL/parameter dimanipulasi.
  3. HR dan employee sama-sama melihat output entitlement yang menampilkan Tahun ke-6, ke-7, dan ke-8 saja pada flow v2.0.
**Plans**: TBD

## Progress

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 14. Data Foundation & Autonomous Bootstrap | 0/2 | Not started | - |
| 15. Native Auth & Session Security Boundary | 0/TBD | Not started | - |
| 16. HR Employee Master CRUD | 0/TBD | Not started | - |
| 17. HR-First Provisioning Lifecycle | 0/TBD | Not started | - |
| 18. Employee Self-View & Leave Output Integrity | 0/TBD | Not started | - |

## Next Command

- `/gsd-plan-phase 14`
