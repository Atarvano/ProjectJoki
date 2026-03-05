# Architecture Research

**Domain:** Integrasi fitur backend v2.0 pada aplikasi procedural PHP existing
**Researched:** 2026-03-05
**Confidence:** HIGH

## Standard Architecture

### System Overview

```text
┌──────────────────────────────────────────────────────────────────────────────┐
│                           HTTP / Page Controllers                           │
├──────────────────────────────────────────────────────────────────────────────┤
│ index.php  login.php  logout.php  hr/*.php  employee/dashboard.php          │
└───────────────┬───────────────────────────────┬──────────────────────────────┘
                │                               │
                ▼                               ▼
┌──────────────────────────────────┐   ┌───────────────────────────────────────┐
│         Access + Session         │   │       Existing Domain Engine          │
│ includes/auth.php (NEW)          │   │ includes/cuti-calculator.php          │
│ - session bootstrap               │   │ (tetap dipakai; logic inti tidak      │
│ - login/logout                    │   │ dipecah)                              │
│ - role guard                      │   └───────────────────────────────────────┘
└──────────────────┬───────────────┘
                   │
                   ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                    Procedural Data Access (Repository)                      │
├──────────────────────────────────────────────────────────────────────────────┤
│ includes/koneksi.php (NEW)                                                   │
│ includes/employee-repo.php (NEW)                                             │
│ includes/user-repo.php (NEW)                                                 │
│ includes/report-repo.php (NEW)                                               │
│ includes/reports-data.php (MODIFIED compatibility facade)                    │
└──────────────────┬───────────────────────────────────────────────────────────┘
                   ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                               MySQL Core Tables                              │
├──────────────────────────────────────────────────────────────────────────────┤
│ employees | users | leave_reports | leave_report_rows | schema_migrations    │
└──────────────────────────────────────────────────────────────────────────────┘
```

### Component Responsibilities

| Component | Responsibility | Typical Implementation |
|-----------|----------------|------------------------|
| Page controllers (`hr/*.php`, `employee/dashboard.php`, `login.php`) | Terima request, validasi input, panggil guard + repo, render/redirect | Procedural top-of-file controller block (pattern existing) |
| `includes/auth.php` | Session lifecycle, login verify, role guard, logout | `session_start()`, `session_regenerate_id()`, session helpers |
| `includes/koneksi.php` | Single source DB connection | MySQLi procedural connect + charset utf8mb4 |
| `includes/employee-repo.php` | CRUD master karyawan | Prepared statements untuk seluruh input user |
| `includes/user-repo.php` | Provisioning akun + lookup login | `password_hash()` / `password_verify()` |
| `includes/report-repo.php` | Persist/report list/detail/export dataset | Header + rows transactional write |
| `includes/reports-data.php` | Bridge dari API v1 ke DB v2 | Fungsi lama (`getReports`, `saveReport`) delegasi ke repo |

## Recommended Project Structure

```text
.
├── login.php                                # MODIFIED: real auth POST
├── logout.php                               # NEW: terminate session safely
├── hr/
│   ├── dashboard.php                        # MODIFIED: DB metrics + HR guard
│   ├── kalkulator.php                       # MODIFIED: save DB report by employee
│   ├── laporan.php                          # MODIFIED: DB list/filter
│   ├── export.php                           # MODIFIED: DB source parity
│   ├── employees.php                        # NEW: HR employee CRUD
│   └── provisioning.php                     # NEW: HR provision/activate account
├── employee/
│   └── dashboard.php                        # MODIFIED: authenticated self-view
└── includes/
    ├── koneksi.php                          # NEW
    ├── auth.php                             # NEW
    ├── employee-repo.php                    # NEW
    ├── user-repo.php                        # NEW
    ├── report-repo.php                      # NEW
    ├── reports-data.php                     # MODIFIED (facade)
    ├── dashboard-sidebar.php                # MODIFIED (add HR nav)
    └── dashboard-topbar.php                 # MODIFIED (real profile/logout)
```

### Structure Rationale

- **No framework rewrite:** semua tambahan ditempatkan di `includes/` + page controller existing agar tetap procedural native.
- **Bridge-first migration:** `reports-data.php` dipertahankan sebagai facade supaya migrasi session-array -> DB tidak memaksa rewrite serentak.
- **Flow-aligned pages:** onboarding HR-first dipisah eksplisit (`employees.php` + `provisioning.php`) agar dependency bisnis terlihat jelas.

## Integration Points (Focus v2.0)

### New vs Modified Components (Explicit)

| File/Module | New/Modified | Integration Point | Notes |
|-------------|--------------|-------------------|-------|
| `includes/koneksi.php` | **NEW** | Semua repo include ini | Satu entrypoint koneksi MySQLi |
| `includes/auth.php` | **NEW** | `login.php`, `logout.php`, semua halaman protected | Menjadi guard standar role HR/employee |
| `includes/employee-repo.php` | **NEW** | `hr/employees.php`, `employee/dashboard.php` | Master data karyawan DB canonical |
| `includes/user-repo.php` | **NEW** | `login.php`, `hr/provisioning.php` | Provisioning + lookup credential |
| `includes/report-repo.php` | **NEW** | `hr/kalkulator.php`, `hr/laporan.php`, `hr/export.php` | Single source laporan |
| `hr/employees.php` | **NEW** | Sidebar HR + repo employee | CRUD karyawan |
| `hr/provisioning.php` | **NEW** | Dari list employee ke provisioning akun | Enforce HR-first activation |
| `logout.php` | **NEW** | Topbar logout link | Destroy session + redirect login |
| `login.php` | **MODIFIED** | Replaces demo role switch | POST auth DB + redirect by role |
| `employee/dashboard.php` | **MODIFIED** | Reads session identity | Hilangkan preset demo sebagai source utama |
| `hr/kalkulator.php` | **MODIFIED** | Save hasil ke DB report tables | Tetap pakai engine kalkulasi existing |
| `hr/laporan.php` | **MODIFIED** | List/filter DB data | No more mixed session data |
| `hr/export.php` | **MODIFIED** | Export dataset from same DB source | Parity list vs export |
| `includes/reports-data.php` | **MODIFIED** | Compatibility API layer | Minimalkan breakage caller lama |
| `includes/dashboard-sidebar.php` | **MODIFIED** | Tambah nav karyawan/provisioning | Navigasi flow baru |
| `includes/dashboard-topbar.php` | **MODIFIED** | Real user label + `/logout.php` | Hapus demo-only behavior |

### Route/Data Flow Updates

| Route | Old Flow | New Flow |
|-------|----------|----------|
| `/login.php` | `?role=` visual-only -> direct dashboard | POST email/password -> verify DB -> session role -> redirect |
| `/hr/dashboard.php` | Open demo page | `requireRole('hr')` -> DB metrics |
| `/hr/kalkulator.php` | Save to `$_SESSION['reports']` | Save to `leave_reports` + `leave_report_rows` |
| `/hr/laporan.php` | Read from session-array | Read from report repo (DB) |
| `/hr/export.php` | Export session dataset | Export exact same DB dataset as laporan |
| `/employee/dashboard.php` | Preset dropdown simulation | Self-view by logged-in user/employee mapping |

## Architectural Patterns

### Pattern 1: Guard-First Controller
**What:** semua halaman protected memanggil guard sebelum logic lain.
**When to use:** seluruh route HR/employee/export.
**Trade-offs:** sedikit boilerplate, tetapi mencegah akses lintas-role.

```php
<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('hr');
// lanjut controller logic
```

### Pattern 2: Repository Boundary (Procedural)
**What:** SQL hanya di `includes/*-repo.php`, bukan di page file.
**When to use:** auth, CRUD, reporting.
**Trade-offs:** tambah file, tapi mengurangi duplikasi SQL dan bug integrasi.

```php
function findUserByEmail(mysqli $conn, string $email): ?array {
    $stmt = mysqli_prepare($conn, 'SELECT id,email,password_hash,role,is_active FROM users WHERE email=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}
```

### Pattern 3: Compatibility Facade Migration
**What:** API lama tetap ada, storage backend diubah ke DB.
**When to use:** transisi dari v1 session-array ke v2 DB.
**Trade-offs:** layer sementara tambahan, tetapi rollout lebih aman.

## Data Flow

### Request Flow (v2 integration)

```text
[User Action]
    ↓
[Page Controller]
    ↓
[auth guard + validation]
    ↓
[repo function(s)] → [koneksi.php] → [MySQL]
    ↓
[cuti-calculator.php for entitlement computation]
    ↓
[render/redirect]
```

### Key Data Flows

1. **HR-first onboarding**  
   HR login -> create employee (`employees`) -> provision account (`users` linked ke employee) -> account active.

2. **Native login/session role**  
   Login POST -> `user-repo` lookup -> `password_verify` -> `session_regenerate_id` -> session store `{user_id, role}`.

3. **Report persistence parity**  
   Kalkulator compute -> save summary + rows ke DB -> laporan read DB -> export read DB yang sama.

4. **Cuti output focus tahun 6/7/8**  
   Engine tetap hitung 8 tahun penuh; layer laporan/UI menampilkan default fokus tahun ke-6/7/8 (derived from row `tahun_ke IN (6,7,8)`), tanpa mengubah rumus inti.

## Safe Build Order (Minimize Breakage)

1. **Phase A — DB Foundation (non-invasive)**
   - Tambah migration + `koneksi.php`.
   - Verifikasi connect + table creation idempotent.
   - Tidak menyentuh route existing dulu.

2. **Phase B — Auth Core + Guards**
   - Tambah `auth.php`, `user-repo.php`, `logout.php`.
   - Upgrade `login.php` ke auth DB.
   - Guard di halaman HR/employee (read-only changes dulu).

3. **Phase C — HR Employee CRUD**
   - Tambah `employee-repo.php` + `hr/employees.php`.
   - Integrasi nav sidebar.
   - Pastikan data master jalan sebelum provisioning.

4. **Phase D — HR-First Provisioning**
   - Tambah `hr/provisioning.php` + relasi employee-user.
   - Enforce rule: employee tanpa provisioning tidak bisa login.

5. **Phase E — Reporting Storage Migration (Bridge)**
   - Tambah `report-repo.php`.
   - Modifikasi `reports-data.php` jadi facade DB.
   - Keep signature fungsi lama agar `hr/kalkulator.php`/`laporan.php` tetap hidup selama transisi.

6. **Phase F — Route Parity Hardening**
   - Update `hr/laporan.php` + `hr/export.php` ke DB canonical.
   - Update `employee/dashboard.php` ke self-view auth.
   - Implement filter/focus tahun 6/7/8 pada presentasi output.

### Dependency Chain

```text
koneksi.php
  -> auth core
  -> employee CRUD
  -> provisioning
  -> report repo + facade migration
  -> laporan/export parity
  -> employee self-view finalization
```

## Anti-Patterns

### Anti-Pattern 1: Mixed canonical data (session + DB in production path)
**What people do:** sebagian route pakai `$_SESSION['reports']`, sebagian DB.  
**Why it's wrong:** mismatch angka di dashboard/laporan/export.  
**Do this instead:** semua read/write laporan lewat `report-repo` (langsung atau via facade transisional).

### Anti-Pattern 2: Role from URL/query
**What people do:** mempertahankan `login.php?role=...` sebagai akses.  
**Why it's wrong:** bypass auth boundary dan tidak memenuhi AUTH requirements.  
**Do this instead:** role hanya dari session hasil login DB.

### Anti-Pattern 3: SQL in page templates
**What people do:** query langsung di `hr/*.php`.  
**Why it's wrong:** sulit audit prepared statement dan meningkat risiko regresi.  
**Do this instead:** query dipusatkan di repo include procedural.

## Sources

- Internal project context & current codebase (HIGH):
  - `.planning/PROJECT.md`
  - `.planning/REQUIREMENTS.md`
  - `.planning/ROADMAP.md`
  - `login.php`, `hr/*.php`, `employee/dashboard.php`, `includes/*.php`
- PHP official docs (HIGH):
  - https://www.php.net/manual/en/function.password-hash.php
  - https://www.php.net/manual/en/function.password-verify.php
  - https://www.php.net/manual/en/function.session-regenerate-id.php
  - https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php

---
*Architecture research for: v2.0 backend integration in procedural PHP app*
*Researched: 2026-03-05*