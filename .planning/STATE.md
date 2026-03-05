---
gsd_state_version: 1.0
milestone: v2.0
milestone_name: milestone
status: executing
last_updated: "2026-03-05T18:14:00.000Z"
progress:
  total_phases: 5
  completed_phases: 4
  total_plans: 10
  completed_plans: 10
  percent: 100
---

# State: Sicuti HRD Cuti Tracker

## Project Reference

**Core Value:** HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data.
**Current Milestone:** v2.0 — Backend Native PHP + HR-First Employee Onboarding
**Roadmap:** 5 phases (14–18), 36 requirements

## Current Position

**Phase:** 18 — Data Wiring — Calculator, Reports & Dashboards
**Plan:** Pending planning/execution
**Status:** Ready for transition
**Progress:** [██████████] 100%

## Performance Metrics

| Metric | Value |
|--------|-------|
| Phases completed | 4/5 |
| Requirements completed | 25/36 |
| Plans completed | 10/10 |
| Current streak | - |
| Phase 14 P01 | 2 min | 2 tasks | 3 files |
| Phase 14 P02 | 3 min | 2 tasks | 2 files |
| Phase 15 P01 | 0 min | 2 tasks | 3 files |
| Phase 15 P02 | 0 min | 3 tasks | 8 files |
| Phase 15 P03 | 4 min | 3 tasks | 4 files |
| Phase 16 P01 | 5 min | 2 tasks | 2 files |
| Phase 16 P03 | 2 min | 2 tasks | 2 files |
| Phase 16 P02 | 6 min | 2 tasks | 2 files |
| Phase 17 P01 | 7 min | 3 tasks | 4 files |
| Phase 17 P02 | 1 min | 2 tasks | 2 files |

## Accumulated Context

### Key Decisions
| Decision | Rationale | Phase |
|----------|-----------|-------|
| 5 phases derived from dependency chain | DB → Auth → CRUD → Provisioning → Wiring follows natural build order | Roadmap |
| DASH requirements distributed across phases | Dashboard items assigned to the phase that creates the feature they depend on (auth UI → P15, nav → P16, stats → P18) | Roadmap |
| Phase 18 combines calculator + reports + dashboards | All are data-wiring tasks with same pattern (swap demo → DB); independent internally but share the same dependency set | Roadmap |
| Idempotent SQL import strategy for foundation schema | `CREATE IF NOT EXISTS` and upsert-style seed inserts enable safe repeated local imports | 14 |
| Silent koneksi sanity-check query | Avoids extra output side effects when `koneksi.php` is included by rendered PHP pages | 14 |
| Phase 14 verification finalized after clean phpMyAdmin import + full runtime PASS checks | Closes environment-related uncertainty and confirms DB foundation is operational in local setup | 14 |
| Keep authentication processing in one procedural POST block at the top of login.php | Ensures redirects happen before output and matches beginner PHP style constraints | 15 |
| Use absolute redirect paths for auth routing | Avoids nested URL path issues across root, HR, and employee pages | 15 |
| Normalize all dashboard logout links to `/logout.php` | Ensures every logout action destroys session through one endpoint | 15 |
| Remove residual demo labels in shared authenticated UI | Keeps authenticated dashboard UX consistent with production behavior | 15 |
| Employee dashboard must only query current session karyawan_id | Enforces own-data-only behavior and removes profile switching risk | 15 |
| Render explicit profile_role in shared topbar include | Guarantees DASH-02 shows both identity name and role on every protected page | 15 |
| Use one active nav id `kelola-karyawan` for HR CRUD pages | Menjaga highlight sidebar konsisten untuk list/add/edit/detail tanpa logic tambahan | 16 |
| Delete action on employee list must be POST + browser confirm copy | Menekan risiko hapus tidak sengaja dan menegaskan dampak hapus akun login terhubung | 16 |
| Detail karyawan menampilkan status akun login dari relasi users.karyawan_id | Membantu HR memastikan status provisioning dari halaman detail tanpa membuka tabel users | 16 |
| Endpoint hapus karyawan wajib POST-only dengan flash ramah untuk sukses/gagal | Menjaga alur hapus tetap aman, sederhana, dan mudah dipahami user HR | 16 |
| Add dan edit dipisah jadi dua halaman procedural terpisah | Alur lebih mudah dipahami pemula dan meminimalkan branching rumit dalam satu file | 16 |
| Pola validasi tambah/edit disamakan (summary + inline) | Feedback error jadi konsisten dan lebih mudah dipahami HR non-teknis | 16 |
| Provisioning endpoint wajib re-check linked account di server sebelum INSERT users | Mencegah provisioning ulang via submit manual meski tombol sudah disembunyikan di UI | 17 |
| Password provisioning dipatok ketat ke rumus NIK + DDMMYYYY(tanggal_lahir) dan abort jika tanggal invalid | Menjaga kredensial awal konsisten dengan requirement dan mencegah akun dibuat dengan password salah format | 17 |
| Flash sukses provisioning di list karyawan memprioritaskan payload `flash.credentials` dengan fallback pesan lama | Menjamin copy kredensial terkunci (2 baris wajib) tetap konsisten tanpa memutus kompatibilitas flash generik | 17 |
| Verifikasi provisioning Wave 0 didokumentasikan sebagai checklist manual + SQL snippet | Memastikan validasi PROV-01..PROV-04 bisa diulang oleh verifier secara operasional dan terukur | 17 |

### Implementation Guardrails
- Native procedural PHP only (no OOP/framework)
- MySQLi via `koneksi.php` (variable: `$koneksi`)
- Native PHP sessions for auth/role guard
- SQL file for manual DB import (no auto-bootstrap)
- Prepared statements for ALL user input queries
- `password_hash()` / `password_verify()` for credentials
- HR admin is standalone user (not in karyawan table)

### TODOs
- [ ] Start Phase 18 planning/execution (Data Wiring)

### Blockers
- None.

## Session Continuity

### Last Session
- **Date:** 2026-03-06
- **Activity:** Completed Phase 17 Plan 02 (one-time credential flash UX + Wave 0 manual validation checklist)
- **Outcome:** `hr/karyawan.php` now renders provisioning success credentials via structured flash payload with fixed labels and one-time warning; `.planning/phases/17-account-provisioning/17-MANUAL-TEST.md` added for repeatable PROV-01..PROV-04 verification + SQL checks
- **Next:** Start Phase 18 planning/execution (data wiring: calculator, reports, dashboards)

### Context for Next Session
- Phase 17 provisioning flow is fully closed (PROV-01..PROV-04 complete)
- Use `17-MANUAL-TEST.md` when re-validating account provisioning/login behavior
- Maintain procedural PHP + prepared statement + PRG flash patterns while replacing demo data with DB-backed data in Phase 18

---
*State initialized: 2026-03-05*
*Last updated: 2026-03-06*
