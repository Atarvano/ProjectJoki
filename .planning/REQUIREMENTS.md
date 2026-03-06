# Requirements: Sicuti HRD Cuti Tracker (v2.0)

**Defined:** 2026-03-05
**Milestone:** v2.0 - Backend Native PHP + HR-First Employee Onboarding
**Core Value:** HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data.

## v2.0 Requirements

### Database Foundation

- [x] **DATA-01**: App has a single `koneksi.php` file as the MySQLi connection source (`mysqli_connect`) for Laragon localhost
- [x] **DATA-02**: App provides a SQL schema file for manual import (phpMyAdmin/CLI) containing CREATE DATABASE, all tables, and HR admin seed
- [x] **DATA-03**: Database has `karyawan` table with fields: id, nik (unique), nama, tanggal_lahir, email, telepon, alamat, jabatan, departemen, tanggal_bergabung, created_at, updated_at
- [x] **DATA-04**: Database has `users` table with fields: id, karyawan_id (FK nullable), username, password (VARCHAR 255), role ENUM('hr','employee'), is_active, created_at
- [x] **DATA-05**: SQL file includes seeded HR admin user so someone can log in after first import
- [x] **DATA-06**: All queries with user input use MySQLi prepared statements (`mysqli_prepare` + `mysqli_stmt_bind_param`)

### Employee CRUD

- [x] **CRUD-01**: HR can add a new employee via form with 9 fields (nama, NIK, jabatan, tanggal_bergabung, tanggal_lahir, email, telepon, alamat, departemen)
- [x] **CRUD-02**: HR can view a list of all employees from the database in an HTML table
- [x] **CRUD-03**: HR can edit an existing employee's data via pre-filled form
- [ ] **CRUD-04**: HR can permanently delete an employee (hard delete), which also removes their user account via CASCADE
- [x] **CRUD-05**: Forms validate required fields, NIK uniqueness, and email format on the server side

### Authentication & Sessions

- [ ] **AUTH-01**: User can log in with NIK + password via POST form, validated against database with `password_verify()`
- [ ] **AUTH-02**: User can log out, which destroys the session and redirects to login page
- [ ] **AUTH-03**: After login, user is redirected to the correct dashboard based on role (HR or Employee)

### Role-Based Access Control

- [ ] **RBAC-01**: App has a single `auth-guard.php` include file with `cekLogin()` and `cekRole()` functions
- [ ] **RBAC-02**: All HR pages are protected — only role='hr' can access them
- [ ] **RBAC-03**: All employee pages are protected — only role='employee' can access them, showing only own data
- [ ] **RBAC-04**: Unauthorized access redirects to login page (not logged in) or own dashboard (wrong role)
- [ ] **RBAC-05**: All demo badges, "Demo v1" notices, and "Akses demo" labels are removed from the UI

### HR-First Provisioning

- [ ] **PROV-01**: HR can click "Buat Akun Login" button to create a login account for an existing employee
- [ ] **PROV-02**: Password is auto-generated from employee data (NIK + birthdate), hashed with `password_hash()`
- [ ] **PROV-03**: System creates a user record linked to employee via karyawan_id FK, with role='employee'
- [ ] **PROV-04**: After provisioning, generated credentials (NIK + plaintext password) are shown once via flash message

### Calculator Integration

- [x] **CALC-01**: HR calculator uses employee dropdown populated from karyawan DB table instead of manual input
- [x] **CALC-02**: `hitungHakCuti()` engine receives real DB join date data (no engine changes needed)
- [x] **CALC-03**: Employee dashboard auto-loads own leave data from DB based on session user
- [x] **CALC-04**: All hardcoded preset_karyawan demo arrays are removed

### Reports & Export

- [x] **RPT-01**: Reports are live-computed from karyawan table + `hitungHakCuti()` — no session storage
- [x] **RPT-02**: Existing laporan.php table UI is maintained, with data source swapped to DB
- [x] **RPT-03**: Excel export (PhpSpreadsheet) is fed from DB query results instead of session data
- [x] **RPT-04**: "Preset Demo" badges, sample labels, and session reset button are removed

### Dashboard & Navigation

- [x] **DASH-01**: HR dashboard shows real employee count and stats from DB queries
- [ ] **DASH-02**: Topbar shows logged-in user's real name and role from session/DB
- [x] **DASH-03**: HR sidebar includes "Kelola Karyawan" navigation link to CRUD pages
- [ ] **DASH-04**: Sidebar/topbar includes logout ("Keluar") button
- [x] **DASH-05**: All "Demo v1" badges and demo notices are removed from dashboards

## Future Requirements (Deferred)

- **SEARCH-01**: Search/filter on employee list page
- **PAGE-01**: Pagination on employee list
- **BATCH-01**: Batch account provisioning for multiple employees
- **RESET-01**: HR can reset employee password
- **BADGE-01**: Employee count badge in sidebar
- **DATEFILTER-01**: Date range filter on reports
- **RICHEXPORT-01**: Richer Excel export with extra employee detail columns

## Out of Scope (v2.0)

| Feature | Reason |
|---------|--------|
| Framework/OOP refactor (Laravel, class-based architecture) | Scope v2.0 is native procedural PHP per project constraint |
| Frontend redesign | Focus is backend, CRUD, auth, and session — visual is not priority |
| Open employee self-signup | Contradicts HR-first provisioning flow |
| Token/JWT auth migration | Session native PHP is correct for server-rendered app |
| Leave request/approval workflow | Beyond v2 scope — app shows entitlements only |
| Employee self-edit profile | Only HR can edit employee data |
| PDF export | Would need new library — stick with Excel via PhpSpreadsheet |
| Password change feature | Defer to future — HR re-provisions if needed |
| CSRF protection | Skipped per user decision — localhost only |
| Soft delete | Hard delete chosen for simplicity |
| Image/avatar upload | Not in scope |

## Implementation Constraints (Must Follow)

- Use **native procedural PHP** (no OOP/class architecture, no frameworks).
- DB connection via single `koneksi.php` with MySQLi procedural. Variable name: `$koneksi`.
- Target environment: **Laragon localhost** (compatible with XAMPP local).
- Flow: **HR creates karyawan data -> HR provisions login account -> Employee logs in**.
- Login field: **NIK** (employee ID number).
- Password formula: **NIK + tanggal_lahir** (e.g. `EMP00115012000`), hashed with `password_hash()`.
- Employee delete: **Hard delete** (permanent, CASCADE removes user account).
- Reports: **Live-computed** from karyawan table (no separate reports table).
- HR admin: **Standalone user** in `users` table only (not in `karyawan` table).
- DB setup: **SQL file for manual import** via phpMyAdmin or CLI.
- Reference style: basic beginner-level PHP CRUD as seen in user-provided repos.

## Traceability

| Requirement | Phase | Status |
|-------------|-------|--------|
| DATA-01 | Phase 14 | Complete |
| DATA-02 | Phase 14 | Complete |
| DATA-03 | Phase 14 | Complete |
| DATA-04 | Phase 14 | Complete |
| DATA-05 | Phase 14 | Complete |
| DATA-06 | Phase 14 | Complete |
| AUTH-01 | Phase 20 | Pending |
| AUTH-02 | Phase 20 | Pending |
| AUTH-03 | Phase 20 | Pending |
| RBAC-01 | Phase 20 | Pending |
| RBAC-02 | Phase 20 | Pending |
| RBAC-03 | Phase 19 | Pending |
| RBAC-04 | Phase 19 | Pending |
| RBAC-05 | Phase 20 | Pending |
| CRUD-01 | Phase 16 | Complete |
| CRUD-02 | Phase 16 | Complete |
| CRUD-03 | Phase 16 | Complete |
| CRUD-04 | Phase 19 | Pending |
| CRUD-05 | Phase 16 | Complete |
| PROV-01 | Phase 20 | Pending |
| PROV-02 | Phase 20 | Pending |
| PROV-03 | Phase 20 | Pending |
| PROV-04 | Phase 20 | Pending |
| CALC-01 | Phase 18 | Complete |
| CALC-02 | Phase 18 | Complete |
| CALC-03 | Phase 18 | Complete |
| CALC-04 | Phase 18 | Complete |
| RPT-01 | Phase 18 | Complete |
| RPT-02 | Phase 18 | Complete |
| RPT-03 | Phase 18 | Complete |
| RPT-04 | Phase 18 | Complete |
| DASH-01 | Phase 18 | Complete |
| DASH-02 | Phase 19 | Pending |
| DASH-03 | Phase 16 | Complete |
| DASH-04 | Phase 20 | Pending |
| DASH-05 | Phase 18 | Complete |

**Coverage:**
- v2.0 requirements: 36 total
- Mapped to phases: 36/36 ✓
- Unmapped: 0
- Pending gap-closure reassignment: 14

---
*Requirements defined: 2026-03-05*
*Last updated: 2026-03-05 after milestone v2.0 re-initialization*
