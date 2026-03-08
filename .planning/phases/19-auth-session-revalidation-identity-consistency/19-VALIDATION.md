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
| 19-03-01 | 03 | 3 | CRUD-04, RBAC-03, RBAC-04, DASH-02 | checkpoint baseline | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` | via 19-00 | ✅ green |

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

Gunakan checklist singkat ini saat ambil bukti browser Phase 19:

1. **Hapus karyawan lalu refresh tab dashboard employee lama**
   - Login sebagai employee dan biarkan tab `employee/dashboard.php` tetap terbuka.
   - Dari sesi HR lain, hapus data karyawan yang sama.
   - Kembali ke tab employee lama lalu refresh.
   - **Hasil yang harus terlihat:** browser langsung pindah ke `/login.php`.

2. **Pastikan employee valid masih bisa masuk normal**
   - Login sebagai employee yang masih valid.
   - Buka `employee/dashboard.php`.
   - **Hasil yang harus terlihat:** dashboard terbuka normal, tidak ada warning stale session atau akun tidak terhubung.

3. **Pastikan nama dan role topbar cocok dengan akun tersimpan**
   - Login sebagai HR lalu lihat topbar pada halaman HR.
   - Logout, lalu login sebagai employee dan lihat topbar pada halaman employee.
   - **Hasil yang harus terlihat:** nama topbar mengikuti identitas akun yang tersimpan, dan label role tetap benar (`HR` atau `Karyawan`).

## Command Checklist

- **Quick run saat edit:** `php tests/phase19_auth_session_smoke.php`
- **Full suite sebelum tutup plan:** `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php`

## Status Update

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 19-00-01 | 00 | 0 | CRUD-04, RBAC-03, RBAC-04, DASH-02 | smoke foundation | `php -l includes/auth-guard.php && php -l tests/phase19_auth_session_smoke.php && php tests/phase19_auth_session_smoke.php` | ready in Wave 0 | ✅ green |
| 19-01-01 | 01 | 1 | CRUD-04, RBAC-03, RBAC-04 | smoke | `php tests/phase19_auth_session_smoke.php` | via 19-00 | ✅ green |
| 19-02-01 | 02 | 2 | DASH-02 | smoke | `php tests/phase19_auth_session_smoke.php` | via 19-00 | ✅ green |
| 19-02-02 | 02 | 2 | DASH-02 | smoke + manual | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` | via 19-00 | ✅ green |
| 19-03-01 | 03 | 3 | CRUD-04, RBAC-03, RBAC-04, DASH-02 | checkpoint baseline | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` | via 19-00 | ⬜ pending |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 10s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** approved by human verifier on 2026-03-08 after browser checks for delete redirect, valid employee access, and topbar identity labels.
