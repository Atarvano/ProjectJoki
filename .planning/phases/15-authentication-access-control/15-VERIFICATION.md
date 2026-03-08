---
phase: 15-authentication-access-control
verified: 2026-03-08T00:00:00.000Z
status: verified
score: 17/17 must-haves verified
re_verification:
  previous_status: gaps_found
  previous_score: 16/17
  gaps_closed:
    - "Topbar shows logged-in user's real name from session, not hardcoded identity"
    - "All demo badges/notices/labels are removed from the UI"
    - "Employee area shows only logged-in employee's own data"
  gaps_remaining: []
  regressions: []
human_verification: []
---

# Phase 15: Authentication & Access Control Verification Report

**Phase Goal:** Users can securely log in and out, with role-based page protection and real identity in the UI
**Verified:** 2026-03-08T00:00:00.000Z
**Status:** verified
**Re-verification:** Yes - after gap closure and runtime approval in Phase 20

## Goal Achievement

### Observable Truths

| # | Truth | Status | Evidence |
| --- | --- | --- | --- |
| 1 | User can POST NIK + password at `login.php` and redirect by role | ✓ VERIFIED | `login.php:16`, `login.php:25`, `login.php:34`, `login.php:63` |
| 2 | Invalid credentials show error on `login.php` without redirect | ✓ VERIFIED | `login.php:72`, `login.php:123` |
| 3 | Already logged-in user visiting `login.php` is redirected | ✓ VERIFIED | `login.php:4` |
| 4 | Visiting `logout.php` destroys session and redirects to login | ✓ VERIFIED | `logout.php:3`, `logout.php:4`, `logout.php:6` |
| 5 | `cekLogin()` redirects to `/login.php` when not logged in | ✓ VERIFIED | `includes/auth-guard.php:8` |
| 6 | `cekRole('hr')` redirects employee to employee dashboard | ✓ VERIFIED | `includes/auth-guard.php:18`, `includes/auth-guard.php:22` |
| 7 | `cekRole('employee')` redirects HR to HR dashboard | ✓ VERIFIED | `includes/auth-guard.php:19`, `includes/auth-guard.php:20` |
| 8 | `/hr/dashboard.php` unauthenticated access redirects to `/login.php` | ✓ VERIFIED | `hr/dashboard.php:3` via `cekLogin()` |
| 9 | `/hr/kalkulator.php` unauthenticated access redirects to `/login.php` | ✓ VERIFIED | `hr/kalkulator.php:3` via `cekLogin()` |
| 10 | `/hr/laporan.php` unauthenticated access redirects to `/login.php` | ✓ VERIFIED | `hr/laporan.php:3` via `cekLogin()` |
| 11 | `/hr/export.php` unauthenticated access redirects to `/login.php` | ✓ VERIFIED | `hr/export.php:8` via `cekRole()->cekLogin()` |
| 12 | `/employee/dashboard.php` unauthenticated access redirects to `/login.php` | ✓ VERIFIED | `employee/dashboard.php:3` via `cekLogin()` |
| 13 | Employee user visiting HR page is redirected to employee dashboard | ✓ VERIFIED | `hr/dashboard.php:4` + `includes/auth-guard.php:22` |
| 14 | HR user visiting employee page is redirected to HR dashboard | ✓ VERIFIED | `employee/dashboard.php:4` + `includes/auth-guard.php:20` |
| 15 | Topbar shows real logged-in user identity (not hardcoded page identity) | ✓ VERIFIED | Session-driven label in `hr/kalkulator.php:58`, `hr/laporan.php:49`, `employee/dashboard.php:10`; rendered in `includes/dashboard-topbar.php:57` |
| 16 | No demo badges/notices/labels remain in protected UI | ✓ VERIFIED | No matches for `Demo v1`, `demo_badge`, `Data demo`, `Preset Demo`, `Pilih Profil Demo`, `Akses demo` in active `*.php` files |
| 17 | Sidebar has `Keluar` button linking to `/logout.php` | ✓ VERIFIED | `includes/dashboard-sidebar.php:58`, `includes/dashboard-sidebar.php:100` |

**Score:** 17/17 truths verified

### Required Artifacts

| Artifact | Expected | Status | Details |
| --- | --- | --- | --- |
| `includes/auth-guard.php` | `cekLogin()` + `cekRole()` guards | ✓ VERIFIED | Exists, substantive guard logic, wired from all protected pages |
| `logout.php` | Session destroy + redirect | ✓ VERIFIED | Exists and destructive session flow implemented |
| `login.php` | Real POST login with DB + `password_verify()` | ✓ VERIFIED | Prepared statement auth, session writes, role redirects |
| `hr/dashboard.php` | HR-guarded dashboard with session identity | ✓ VERIFIED | Guarded and context uses `$_SESSION['nama']` |
| `hr/kalkulator.php` | HR-guarded calculator with session identity | ✓ VERIFIED | Guarded; `profile_label` and initials resolved from session fallback chain |
| `hr/laporan.php` | HR-guarded reports with session identity, demo-free copy | ✓ VERIFIED | Guarded; session identity resolved; neutralized non-demo labels |
| `hr/export.php` | HR-guarded export route | ✓ VERIFIED | `cekRole('hr')` enforced before export logic |
| `employee/dashboard.php` | Employee-guarded own-data dashboard | ✓ VERIFIED | Uses `$_SESSION['karyawan_id']`, prepared query, no profile-switch UI |
| `includes/dashboard-topbar.php` | Identity UI shows name + role | ✓ VERIFIED | Renders `profile_label` and `profile_role` with role fallback |
| `includes/dashboard-sidebar.php` | Logout button(s) | ✓ VERIFIED | Mobile + desktop `Keluar` buttons route to `/logout.php` |

### Key Link Verification

| From | To | Via | Status | Details |
| --- | --- | --- | --- | --- |
| `login.php` | `koneksi.php` | `require_once` in POST handler | WIRED | `login.php:17` |
| `login.php` | `users` table | prepared SELECT query | WIRED | `login.php:25` |
| `includes/auth-guard.php` | `$_SESSION` | session checks in guards | WIRED | `includes/auth-guard.php:8`, `includes/auth-guard.php:18` |
| `employee/dashboard.php` | `koneksi.php` | `require_once` before employee lookup | WIRED | `employee/dashboard.php:6` |
| `employee/dashboard.php` | `karyawan` table | prepared SELECT by session `karyawan_id` | WIRED | `employee/dashboard.php:31`, `employee/dashboard.php:40` |
| `employee/dashboard.php` | leave render | `hitungHakCuti()` result displayed in table/cards | WIRED | `employee/dashboard.php:61`, `employee/dashboard.php:167` |
| `hr/kalkulator.php` | `includes/dashboard-topbar.php` | dashboard context profile fields | WIRED | `hr/kalkulator.php:75`, `includes/dashboard-topbar.php:57` |
| `hr/laporan.php` | `includes/dashboard-topbar.php` | dashboard context profile fields | WIRED | `hr/laporan.php:66`, `includes/dashboard-topbar.php:57` |
| `includes/dashboard-sidebar.php` | `logout.php` | `href` on Keluar buttons | WIRED | `includes/dashboard-sidebar.php:58`, `includes/dashboard-sidebar.php:100` |

### Requirements Coverage

| Requirement | Source Plan | Description | Status | Evidence |
| --- | --- | --- | --- | --- |
| AUTH-01 | 15-01, 15-03 | Login with NIK+password via POST + `password_verify()` | ✓ SATISFIED | `login.php:16`, `login.php:34` |
| AUTH-02 | 15-01, 15-03 | Logout destroys session and redirects | ✓ SATISFIED | `logout.php:3`, `logout.php:4`, `logout.php:6` |
| AUTH-03 | 15-01, 15-03 | Redirect by role after login | ✓ SATISFIED | `login.php:63` |
| RBAC-01 | 15-01, 15-03 | Single `auth-guard.php` with `cekLogin()` + `cekRole()` | ✓ SATISFIED | `includes/auth-guard.php:6`, `includes/auth-guard.php:14` |
| RBAC-02 | 15-02, 15-03 | All HR pages protected for role=hr | ✓ SATISFIED | `hr/dashboard.php:4`, `hr/kalkulator.php:4`, `hr/laporan.php:4`, `hr/export.php:8` |
| RBAC-03 | 15-02, 15-03 | Employee page protected and shows only own data | ✓ SATISFIED | Guard `employee/dashboard.php:4`; own-data query `employee/dashboard.php:40`; no preset/demo selector in file |
| RBAC-04 | 15-01, 15-03 | Unauthorized redirects to login or own dashboard | ✓ SATISFIED | `includes/auth-guard.php:9`, `includes/auth-guard.php:20`, `includes/auth-guard.php:22` |
| RBAC-05 | 15-02, 15-03 | Remove all demo badges/notices/labels | ✓ SATISFIED | Repo scan in `*.php` found no `Demo v1`, `demo_badge`, `Data demo`, `Preset Demo`, `Pilih Profil Demo`, `Akses demo` |
| DASH-02 | 15-02, 15-03 | Topbar shows real user name and role from session/DB | ✓ SATISFIED | Name from page session contexts (`hr/kalkulator.php:58`, `hr/laporan.php:49`, `employee/dashboard.php:10`), role rendered in topbar (`includes/dashboard-topbar.php:58`) |
| DASH-04 | 15-02, 15-03 | Logout (`Keluar`) in sidebar/topbar | ✓ SATISFIED | `includes/dashboard-topbar.php:65`, `includes/dashboard-sidebar.php:58`, `includes/dashboard-sidebar.php:100` |

Orphaned requirements for Phase 15 in `REQUIREMENTS.md`: **none**. The Phase 15 traceability IDs are exactly: AUTH-01, AUTH-02, AUTH-03, RBAC-01, RBAC-02, RBAC-03, RBAC-04, RBAC-05, DASH-02, DASH-04, and all are declared in phase plan frontmatter.

### Anti-Patterns Found

| File | Line | Pattern | Severity | Impact |
| --- | --- | --- | --- | --- |
| _None_ | - | No TODO/FIXME/placeholder/stub indicators found in scoped Phase 15 implementation files | ℹ️ Info | No blocker or warning anti-patterns detected from static scan |

### Human Verification Required

None. The remaining browser-only auth evidence was closed by the approved Phase 20 walkthrough recorded in `.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-VALIDATION.md`, covering HR login, employee login, wrong-role redirect, logout, protected-page reopen blocking, and demo-free tested pages.

### Gaps Summary

No code-level gaps remain from prior verification. The last runtime-only gap was closed by approved browser evidence in Phase 20, so auth, role redirect, logout continuity, and tested-page demo cleanup are now verified in runtime as well as code.

---

_Verified: 2026-03-05T07:56:38.775Z_
_Verifier: Claude (gsd-verifier)_
