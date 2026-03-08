---
phase: 19
slug: auth-session-revalidation-identity-consistency
status: draft
nyquist_compliant: true
wave_0_complete: true
created: 2026-03-08
---

# Phase 19 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | Plain PHP smoke scripts |
| **Config file** | none — standalone PHP test files |
| **Quick run command** | `php tests/phase19_auth_session_smoke.php` |
| **Full suite command** | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` |
| **Estimated runtime** | ~10 seconds |

---

## Sampling Rate

- **After every task commit:** Run `php tests/phase19_auth_session_smoke.php`
- **After every plan wave:** Run `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 10 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 19-00-01 | 00 | 0 | CRUD-04, RBAC-03, RBAC-04, DASH-02 | smoke foundation | `php -l includes/auth-guard.php && php -l tests/phase19_auth_session_smoke.php && php tests/phase19_auth_session_smoke.php` | ready in Wave 0 | ✅ green |
| 19-01-01 | 01 | 1 | CRUD-04, RBAC-03, RBAC-04 | smoke | `php tests/phase19_auth_session_smoke.php` | via 19-00 | ⬜ pending |
| 19-02-01 | 02 | 2 | DASH-02 | smoke | `php tests/phase19_auth_session_smoke.php` | via 19-00 | ⬜ pending |
| 19-02-02 | 02 | 2 | DASH-02 | smoke + manual | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` | via 19-00 | ⬜ pending |
| 19-03-01 | 03 | 3 | CRUD-04, RBAC-03, RBAC-04, DASH-02 | checkpoint baseline | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` | via 19-00 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [x] `tests/phase19_auth_session_smoke.php` scaffold exists as Wave 0 foundation for CRUD-04, RBAC-03, RBAC-04, and DASH-02 smoke cases
- [x] `includes/auth-guard.php` exposes `AUTH_GUARD_TEST_MODE` seam so the smoke script can inspect redirect decisions without sending real headers during CLI runs
- [x] Manual verification notes for delete -> login redirect and identity consistency evidence stay tracked in this file

### Wave 0 Baseline Notes

- Run `php -l includes/auth-guard.php && php -l tests/phase19_auth_session_smoke.php && php tests/phase19_auth_session_smoke.php` after each Phase 19 guard change.
- Use `tests/phase19_auth_session_smoke.php` as the single quick smoke command for stale user row, inactive user row, missing employee link, valid employee session, wrong-role redirect, and session identity markers.
- Keep the guard seam tiny: normal browser flow still uses `header('Location: ...')` and `exit`, while CLI smoke mode reads the returned array.
- Later plans should point to task `19-00-01` instead of writing `MISSING` in verification notes.

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Reopen `employee/dashboard.php` in the same browser after HR deletes that employee | CRUD-04, RBAC-03, RBAC-04 | Needs real browser session cookie + redirect observation | Log in as employee, keep tab open, delete that employee from HR in another session, refresh employee dashboard, confirm redirect to `/login.php` |
| Confirm valid employee session still opens own dashboard after guard changes | RBAC-03 | Browser flow proves normal protected page access still works | Log in as a valid employee account and open `employee/dashboard.php`, confirm dashboard loads without stale-session warning |
| Confirm topbar identity stays session-driven but uses persistent login data | DASH-02 | Visual label check needs rendered UI | Log in as HR and employee, open protected page topbar, confirm displayed name matches stored account identity and role label remains correct |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 10s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
