# Roadmap: Sicuti HRD Cuti Tracker (v2.0)

**Milestone:** v2.0 — Backend Native PHP + HR-First Employee Onboarding
**Phases:** 5 (Phase 14–18, continuing from v1.0 which ended at Phase 13)
**Requirements:** 36 mapped across 5 phases
**Created:** 2026-03-05

## Implementation Guardrails

These constraints apply to ALL phases:

- **Architecture:** Native procedural PHP only — no OOP, no classes, no frameworks
- **Database API:** MySQLi procedural via single `koneksi.php` (variable: `$koneksi`)
- **Auth:** Native PHP sessions (`session_start`, `$_SESSION`, `session_destroy`)
- **DB Setup:** SQL file for manual import via phpMyAdmin/CLI — no auto-bootstrap
- **Security:** `password_hash()`/`password_verify()`, prepared statements for all user input
- **Flow:** HR creates employee → HR provisions account → Employee logs in
- **Style:** Basic beginner-level PHP CRUD code (reference repo patterns)

## Phases

- [x] **Phase 14: Database Foundation** — SQL schema, connection file, tables, and HR admin seed (completed 2026-03-05)
- [x] **Phase 15: Authentication & Access Control** — Real login/logout, role guards on all pages, auth-aware UI (completed 2026-03-05)
- [ ] **Phase 16: Employee CRUD & HR Navigation** — Full employee management with list/add/edit/delete and sidebar nav
- [ ] **Phase 17: Account Provisioning** — HR creates login accounts for employees with auto-generated passwords
- [ ] **Phase 18: Data Wiring — Calculator, Reports & Dashboards** — All pages swap from demo/session data to live DB data

## Phase Details

### Phase 14: Database Foundation
**Goal:** Application has a working MySQL database with connection, schema, and seeded HR admin account
**Depends on:** Nothing (foundation for all subsequent phases)
**Requirements:** DATA-01, DATA-02, DATA-03, DATA-04, DATA-05, DATA-06
**Success Criteria** (what must be TRUE):
  1. Any PHP file can `require_once 'koneksi.php'` and use `$koneksi` to execute a query against the MySQL database
  2. SQL file imports cleanly via phpMyAdmin/CLI, creating the database, `karyawan` table (12 fields, NIK unique), and `users` table (FK cascade to karyawan)
  3. Seeded HR admin account exists in `users` table with hashed password and role='hr' after import
  4. A sample prepared statement query (`mysqli_prepare` + `mysqli_stmt_bind_param`) works end-to-end, establishing the security pattern for all subsequent phases
**Plans:** 2/2 plans complete
Plans:
- [ ] 14-01-PLAN.md — SQL schema, connection file, seed data & prepared statement pattern

### Phase 15: Authentication & Access Control
**Goal:** Users can securely log in and out, with role-based page protection and real identity in the UI
**Depends on:** Phase 14 (needs DB + users table + HR admin seed)
**Requirements:** AUTH-01, AUTH-02, AUTH-03, RBAC-01, RBAC-02, RBAC-03, RBAC-04, RBAC-05, DASH-02, DASH-04
**Success Criteria** (what must be TRUE):
  1. User can log in at login.php with NIK + password (POST form), validated against DB with `password_verify()`, and is redirected to the correct dashboard by role
  2. User can log out from any page via "Keluar" button — session is destroyed and user is redirected to login page
  3. All HR pages reject non-HR users; all employee pages reject non-employees; unauthenticated users redirect to login (single `auth-guard.php` with `cekLogin()` + `cekRole()`)
  4. Topbar displays the logged-in user's real name and role from session/DB data
  5. All "Demo v1" badges, "Akses demo" labels, and demo notice text are removed from the UI
**Plans:** 3/3 plans complete
Plans:
- [ ] 15-01-PLAN.md — Core auth infrastructure: auth-guard.php, logout.php, and login.php rewrite with real POST authentication
- [ ] 15-02-PLAN.md — Guard all pages, session-aware dashboards, demo cleanup, logout button in sidebar

### Phase 16: Employee CRUD & HR Navigation
**Goal:** HR can fully manage employee records through a complete CRUD interface
**Depends on:** Phase 15 (CRUD pages need auth guards; HR role required)
**Requirements:** CRUD-01, CRUD-02, CRUD-03, CRUD-04, CRUD-05, DASH-03
**Success Criteria** (what must be TRUE):
  1. HR can add a new employee via 9-field form with server-side validation (required fields, NIK uniqueness, email format)
  2. HR can view all employees from the database in an HTML table on the employee list page
  3. HR can edit any employee's data via a pre-filled form and save updates to the database
  4. HR can permanently delete an employee (hard delete) and the associated user account is automatically removed via CASCADE
  5. HR sidebar includes a "Kelola Karyawan" navigation link that leads to the employee management pages
**Plans:** 1/3 plans executed
Plans:
- [ ] 16-01-PLAN.md — Sidebar `Kelola Karyawan` nav + employee list page (table, empty state, actions)
- [ ] 16-02-PLAN.md — Separate add/edit pages with server validation, prefill, and PRG flash flow
- [ ] 16-03-PLAN.md — Employee detail page + POST hard-delete endpoint with cascade-aware messaging

### Phase 17: Account Provisioning
**Goal:** HR can provision login accounts for employees, completing the HR-first onboarding flow
**Depends on:** Phase 16 (needs employees to exist in karyawan table)
**Requirements:** PROV-01, PROV-02, PROV-03, PROV-04
**Success Criteria** (what must be TRUE):
  1. HR can click "Buat Akun Login" for any existing employee who doesn't yet have an account
  2. System auto-generates password from employee data (NIK + tanggal_lahir), hashes it with `password_hash()`, and creates a `users` record linked via karyawan_id FK with role='employee'
  3. After provisioning, the generated credentials (NIK as username + plaintext password) are displayed exactly once via flash message
  4. A provisioned employee can successfully log in with their NIK and auto-generated password (end-to-end flow validated)
**Plans:** TBD

### Phase 18: Data Wiring — Calculator, Reports & Dashboards
**Goal:** All application pages display real DB data instead of demo/session data, completing the v1→v2 transformation
**Depends on:** Phase 17 (needs provisioned employee accounts for employee dashboard testing; needs real karyawan data for calculator/reports)
**Requirements:** CALC-01, CALC-02, CALC-03, CALC-04, RPT-01, RPT-02, RPT-03, RPT-04, DASH-01, DASH-05
**Success Criteria** (what must be TRUE):
  1. HR calculator page offers an employee dropdown populated from the karyawan DB table, and `hitungHakCuti()` receives real join-date data (no engine changes)
  2. Employee dashboard auto-loads own leave calculation from DB based on session user — no manual input, no preset selector
  3. Reports page computes leave data live from karyawan table + `hitungHakCuti()` (no session storage), and Excel export produces a valid XLSX from DB query results
  4. HR dashboard shows real employee count and statistics from database queries
  5. All hardcoded `preset_karyawan` arrays, "Preset Demo" badges, sample labels, and session reset buttons are removed from the codebase
**Plans:** TBD

## Requirement Coverage

| Requirement | Phase | Category |
|-------------|-------|----------|
| DATA-01 | Phase 14 | Database Foundation |
| DATA-02 | Phase 14 | Database Foundation |
| DATA-03 | Phase 14 | Database Foundation |
| DATA-04 | Phase 14 | Database Foundation |
| DATA-05 | Phase 14 | Database Foundation |
| DATA-06 | Phase 14 | Database Foundation |
| AUTH-01 | Phase 15 | Authentication |
| AUTH-02 | Phase 15 | Authentication |
| AUTH-03 | Phase 15 | Authentication |
| RBAC-01 | Phase 15 | Access Control |
| RBAC-02 | Phase 15 | Access Control |
| RBAC-03 | Phase 15 | Access Control |
| RBAC-04 | Phase 15 | Access Control |
| RBAC-05 | Phase 15 | Access Control |
| DASH-02 | Phase 15 | Dashboard (auth UI) |
| DASH-04 | Phase 15 | Dashboard (auth UI) |
| CRUD-01 | Phase 16 | Employee CRUD |
| CRUD-02 | Phase 16 | Employee CRUD |
| CRUD-03 | Phase 16 | Employee CRUD |
| CRUD-04 | Phase 16 | Employee CRUD |
| CRUD-05 | Phase 16 | Employee CRUD |
| DASH-03 | Phase 16 | Dashboard (CRUD nav) |
| PROV-01 | Phase 17 | Provisioning |
| PROV-02 | Phase 17 | Provisioning |
| PROV-03 | Phase 17 | Provisioning |
| PROV-04 | Phase 17 | Provisioning |
| CALC-01 | Phase 18 | Calculator |
| CALC-02 | Phase 18 | Calculator |
| CALC-03 | Phase 18 | Calculator |
| CALC-04 | Phase 18 | Calculator |
| RPT-01 | Phase 18 | Reports |
| RPT-02 | Phase 18 | Reports |
| RPT-03 | Phase 18 | Reports |
| RPT-04 | Phase 18 | Reports |
| DASH-01 | Phase 18 | Dashboard (stats) |
| DASH-05 | Phase 18 | Dashboard (cleanup) |

**Mapped: 36/36 ✓ — No orphans, no duplicates**

## Dependency Graph

```
Phase 14 (DB Foundation)
    └── Phase 15 (Auth & Access Control)
            └── Phase 16 (Employee CRUD)
                    └── Phase 17 (Account Provisioning)
                            └── Phase 18 (Data Wiring)
```

All phases are sequential. Each phase depends on its predecessor. No parallelization within the critical path (though Phase 18's internal work items — calculator, reports, dashboards — are independent of each other).

## Progress

| Phase | Plans Complete | Status | Completed |
|-------|---------------|--------|-----------|
| 14. Database Foundation | 2/2 | Complete   | 2026-03-05 |
| 15. Authentication & Access Control | 3/3 | Complete    | 2026-03-05 |
| 16. Employee CRUD & HR Navigation | 1/3 | In Progress|  |
| 17. Account Provisioning | 0/? | Not started | - |
| 18. Data Wiring — Calculator, Reports & Dashboards | 0/? | Not started | - |

---
*Roadmap created: 2026-03-05*
*Milestone: v2.0 — Backend Native PHP + HR-First Employee Onboarding*
