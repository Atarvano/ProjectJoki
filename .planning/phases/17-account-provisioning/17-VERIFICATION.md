---
phase: 17-account-provisioning
verified: 2026-03-08T00:00:00.000Z
status: verified
score: 6/6 must-haves verified
human_verification: []
---

# Phase 17: Account Provisioning Verification Report

**Phase Goal:** HR can provision login accounts for employees, completing the HR-first onboarding flow.
**Verified:** 2026-03-08T00:00:00.000Z
**Status:** verified
**Re-verification:** Yes — runtime closure recorded in Phase 20

## Goal Achievement

### Observable Truths

| # | Truth | Status | Evidence |
| --- | --- | --- | --- |
| 1 | HR sees Buat Akun Login action on employee list only when account status is Belum dibuat. | ✓ VERIFIED | `hr/karyawan.php` computes `$akun_login_status` from `LEFT JOIN users`; button rendered only inside `if ($akun_login_status === 'Belum dibuat')` (lines 154, 173-178). |
| 2 | HR sees a primary provisioning CTA on employee detail only when account is Belum dibuat. | ✓ VERIFIED | `hr/karyawan-detail.php` shows POST form/button to `/hr/karyawan-provision.php` only when status is `Belum dibuat`; else shows non-action badge (lines 102-111). |
| 3 | Submitting provisioning creates one linked employee user with hashed password and role=employee. | ✓ VERIFIED | `hr/karyawan-provision.php` is POST-only, checks existing `u.id AS user_id`, builds password from NIK+dob, hashes via `password_hash`, inserts `users(karyawan_id, username, password, role, is_active)` with `role='employee'` (lines 32-39, 52-57, 87-94, 97-111, 124-126). |
| 4 | After successful provisioning, HR sees generated credentials once on karyawan list page. | ✓ VERIFIED | Endpoint redirects to `/hr/karyawan.php` with success flash containing credential lines; list page consumes `$_SESSION['flash']` then unsets it (one-time) (provision lines 129-136, 144; list lines 8-11, 71-110). |
| 5 | Flash output clearly shows 2 credential lines: NIK (Username) and Password awal. | ✓ VERIFIED | List renderer supports structured credentials and fallback flash text; labels appear as `NIK (Username)` and `Password awal` (karyawan.php lines 91-93). Endpoint success message also includes both labels (karyawan-provision.php lines 131-134). |
| 6 | HR can validate end-to-end login works with generated credentials. | ✓ VERIFIED | Approved Phase 20 walkthrough provisioned one employee from `hr/karyawan.php`, captured the one-time flash credentials, logged out HR, then logged in successfully at `/login.php` and landed on `/employee/dashboard.php` (`.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-VALIDATION.md`). |

**Score:** 5/6 truths verified

### Required Artifacts

| Artifact | Expected | Status | Details |
| --- | --- | --- | --- |
| `hr/karyawan.php` | List action visibility + status wording + one-time credential flash rendering | ✓ VERIFIED | Exists, substantive implementation (query + conditional UI + flash lifecycle), wired by redirects and links. |
| `hr/karyawan-detail.php` | Detail account-status card with conditional provisioning CTA | ✓ VERIFIED | Exists, substantive account-status card and conditional CTA/badge, wired from list detail link. |
| `hr/karyawan-provision.php` | POST-only provisioning endpoint with password generation + users insert | ✓ VERIFIED | Exists, substantive guards/validation/password formula/hash/insert/redirect; wired by POST forms in list/detail. |
| `.planning/phases/17-account-provisioning/17-MANUAL-TEST.md` | Manual checklist for PROV-01..PROV-04 with SQL checks | ✓ VERIFIED | Exists and contains end-to-end steps + SQL verification snippets. |

### Key Link Verification

| From | To | Via | Status | Details |
| --- | --- | --- | --- | --- |
| `hr/karyawan.php` | `hr/karyawan-provision.php` | POST form action per employee row | ✓ WIRED | `<form method="post" action="/hr/karyawan-provision.php">` in row actions (line 174). |
| `hr/karyawan-detail.php` | `hr/karyawan-provision.php` | Primary CTA form/button in account-status card | ✓ WIRED | Conditional form posts to provisioning endpoint (lines 103-107). |
| `hr/karyawan-provision.php` | `users` table | Prepared INSERT by `karyawan_id` | ✓ WIRED | `INSERT INTO users (karyawan_id, username, password, role, is_active)` + bind/execute (lines 111-126). |
| `hr/karyawan-provision.php` | `hr/karyawan.php` | Session flash + PRG redirect | ✓ WIRED | Multiple `header('Location: /hr/karyawan.php')` branches including success path. |
| `hr/karyawan.php` | `login.php` | Displayed credentials used in login form | ⚠️ PARTIAL (human runtime) | Credential labels/rendering exist; `login.php` consumes username/password with `password_verify()`. Actual login success must be confirmed manually. |

### Requirements Coverage

| Requirement | Source Plan | Description | Status | Evidence |
| --- | --- | --- | --- | --- |
| PROV-01 | 17-01-PLAN.md | HR can click "Buat Akun Login" for existing employee | ✓ SATISFIED | Conditional buttons/forms in list and detail pages posting to provisioning endpoint. |
| PROV-02 | 17-01-PLAN.md | Auto-generate password (NIK + birthdate), hash with `password_hash()` | ✓ SATISFIED | `build_provision_password()` uses `nik . dmY(tanggal_lahir)` and `password_hash(..., PASSWORD_DEFAULT)`. |
| PROV-03 | 17-01-PLAN.md | Create linked `users` record via `karyawan_id`, role='employee' | ✓ SATISFIED | Prepared insert into users with `karyawan_id`, `role='employee'`, `is_active=1`; duplicate guarded by existing `user_id` check. |
| PROV-04 | 17-02-PLAN.md | Show generated credentials once via flash | ✓ SATISFIED | Flash lifecycle is one-time (`unset($_SESSION['flash'])`), credentials shown with required labels/warning text. |

**Orphaned requirement check:** None. All Phase 17 requirement IDs in `REQUIREMENTS.md` (PROV-01..PROV-04) are declared in phase 17 plan frontmatter and accounted for.

### Anti-Patterns Found

| File | Line | Pattern | Severity | Impact |
| --- | --- | --- | --- | --- |
| - | - | No blocking TODO/FIXME/placeholders or stub handlers found in phase files | ℹ️ Info | No blocker anti-pattern detected for phase goal. |

### Human Verification Required

None. The remaining browser-only provisioning proof was closed by the approved Phase 20 walkthrough recorded in `.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-VALIDATION.md`, including row-action provisioning, one-time flash behavior, and successful employee login with captured credentials.

## Gaps Summary

No implementation gaps found in code for must-have artifacts and core wiring. The final runtime-only acceptance gap was closed in Phase 20, so provisioning is now verified end-to-end.

---

_Verified: 2026-03-05T18:13:42.401Z_
_Verifier: Claude (gsd-verifier)_
