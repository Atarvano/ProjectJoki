---
gsd_state_version: 1.0
milestone: v2.0
milestone_name: milestone
status: planning
last_updated: "2026-03-08T16:03:18.012Z"
progress:
  total_phases: 7
  completed_phases: 6
  total_plans: 20
  completed_plans: 19
  percent: 100
---

# State: Sicuti HRD Cuti Tracker

## Project Reference

**Core Value:** HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data.
**Current Milestone:** v2.0 — Backend Native PHP + HR-First Employee Onboarding
**Roadmap:** 7 phases (14–20), 36 requirements

## Current Position

**Phase:** 20 — Provisioning E2E Verification & Flash Contract Alignment
**Plan:** 01 of 02 completed (`20-01-PLAN.md`)
**Status:** Ready for next plan
**Progress:** [█████████░] 95%

## Performance Metrics

| Metric | Value |
|--------|-------|
| Phases completed | 6/7 |
| Requirements completed | 36/36 |
| Plans completed | 19/20 |
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
| Phase 18 P01 | 1 min | 2 tasks | 3 files |
| Phase 18 P02 | 2 min | 2 tasks | 3 files |
| Phase 18 P03 | 5 min | 2 tasks | 4 files |
| Phase 18 P04 | 4 min | 2 tasks | 3 files |
| Phase 19 P00 | 0 min | 2 tasks | 3 files |
| Phase 19 P01 | 0 min | 1 tasks | 3 files |
| Phase 19 P02 | 2 min | 2 tasks | 5 files |
| Phase 19 P03 | 0 min | 1 tasks | 1 files |
| Phase 20 P01 | 6 min | 2 tasks | 4 files |

## Accumulated Context

### Key Decisions
| Decision | Rationale | Phase |
|----------|-----------|-------|
| Wave 0 uses one plain PHP smoke script with calculator, reports, and dashboards groups | Future Phase 18 plans can run one fast grouped command after each rewiring task without adding a test framework | 18 |
| Phase 18 validation is tracked in one 8-task matrix across Plans 18-01 through 18-04 | Keeping all plan, wave, requirement, and command mappings in one contract prevents stale verification rows during execution | 18 |
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
| Calculator now uses GET karyawan_id selection so HR can refresh and bookmark a selected employee context | Menjaga alur kalkulator sederhana, bisa di-refresh, dan tetap terikat ke data karyawan nyata | 18 |
| Join year is derived from karyawan.tanggal_bergabung and passed into hitungHakCuti() without changing the cuti engine | Memenuhi wiring DB Phase 18 sambil mempertahankan engine cuti v1 yang sudah stabil | 18 |
| Employee detail is the main HR entry point into leave entitlement via a simple query-string link | Menegaskan employee record sebagai sumber kebenaran untuk melihat hak cuti | 18 |
| Laporan dan export sama-sama membangun row live langsung dari query karyawan terurut NIK agar output halaman dan XLSX tetap sinkron | Menjaga parity antara recap di browser dan file export tanpa storage session terpisah | 18 |
| Filter status laporan diteruskan ke export supaya hasil unduhan mengikuti recap yang sedang dilihat HR | Memudahkan HR mengekspor subset data yang sedang direview tanpa logika export terpisah | 18 |
| HR dashboard menghitung total karyawan dan akun employee langsung dari tabel karyawan dan users | Ringkasan HR selalu mengikuti kondisi database terbaru tanpa counter preset atau laporan session | 18 |
| Metrik "siap cuti tahun ini" menghitung status tahun berjalan Tersedia atau Menunggu | Definisi ini dikunci di context Phase 18 dan harus diikuti persis pada dashboard HR | 18 |
| Dashboard employee menonjolkan hanya baris tahun ke-6, 7, dan 8 sambil tetap memakai session-linked own-data lookup | Menjaga tampilan employee tetap fokus dan aman sesuai batasan akses data pribadi | 18 |
| Use AUTH_GUARD_TEST_MODE as one tiny procedural seam so smoke tests can inspect redirect outcomes without real headers | Menjaga auth guard tetap sederhana untuk pemula sambil memberi jalur observasi CLI untuk Phase 19 | 19 |
| Keep Phase 19 Wave 0 coverage in one plain PHP smoke script with named placeholder cases before live revalidation logic is added | Memberi command verifikasi nyata dan bentuk test procedural stabil untuk plan-plan berikutnya | 19 |
| Keep stale-session rejection centralized in includes/auth-guard.php so every protected page gets the same live user check | Menutup gap delete-cascade dan akun inactive dalam satu guard prosedural bersama | 19 |
| Treat missing or inactive users as logout-to-login, but keep valid wrong-role sessions on their old dashboard redirect flow | Memisahkan logout stale session dari redirect role agar behavior lama yang valid tidak rusak | 19 |
| Hydrate HR session display name from users.username at login time instead of a hardcoded label | Menjaga topbar tetap session-driven tetapi identitas HR sekarang berasal dari data akun tersimpan | 19 |
| Keep topbar identity session-driven and add simple fallback variables in hr/dashboard.php instead of new per-request DB profile queries | Menutup gap identitas tanpa menambah profile system atau query baru di setiap request | 19 |
| Keep Phase 19 validation as one short checklist tied to the existing smoke commands | Checklist tetap mudah dipakai verifier dan tidak keluar dari pola smoke test phase ini | 19 |
| Keep the final browser-only evidence in 19-VALIDATION.md instead of adding more runtime code or test scaffolding | Plan 19-03 hanya checkpoint browser, jadi bukti final paling sederhana adalah approval yang ditulis di artefak validasi phase yang sudah ada | 19 |
| Keep provisioning success on one structured flash.credentials contract while preserving a generic message fallback | Renderer `hr/karyawan.php` sudah mendukung kontrak ini, jadi endpoint cukup disejajarkan tanpa redesign flash global | 20 |
| Use one lightweight Phase 20 validation file to lock the final browser walkthrough instead of adding new automation tooling | Gap tersisa adalah bukti runtime, jadi checklist fokus lebih murah dan cocok untuk project PHP prosedural pemula | 20 |

### Implementation Guardrails
- Native procedural PHP only (no OOP/framework)
- MySQLi via `koneksi.php` (variable: `$koneksi`)
- Native PHP sessions for auth/role guard
- SQL file for manual DB import (no auto-bootstrap)
- Prepared statements for ALL user input queries
- `password_hash()` / `password_verify()` for credentials
- HR admin is standalone user (not in karyawan table)

### TODOs
- [x] Phase 19 Plan 00 complete — Wave 0 smoke scaffold and validation baseline are in place.
- [x] Phase 19 Plan 01 complete — live auth session revalidation now blocks stale employee access in the shared guard.
- [x] Phase 19 Plan 02 complete — login identity hydration and validation checklist now use saved account identity.
- [x] Continue with `19-03-PLAN.md` for browser verification evidence.
- [x] Phase 20 Plan 01 complete — flash contract provisioning sekarang sejajar dan checklist browser final sudah siap.
- [ ] Continue with `20-02-PLAN.md` for browser walkthrough evidence and verification closure.

### Blockers
- None.

## Session Continuity

### Last Session
- **Date:** 2026-03-08
- **Activity:** Completed Phase 20 Plan 01 (flash contract alignment + Phase 20 validation checklist)
- **Outcome:** `hr/karyawan-provision.php` now writes structured `flash.credentials`, `tests/provisioning_endpoint_test.php` checks that contract, and `20-VALIDATION.md` locks the final browser walkthrough.
- **Next:** Execute `20-02-PLAN.md` to collect browser evidence and close remaining auth/provisioning verification debt.

### Context for Next Session
- Start from `.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-01-SUMMARY.md` for the exact flash-contract and walkthrough handoff.
- Keep topbar identity session-driven and reuse the existing Phase 19 smoke baseline during final browser closure.
- Plan 20-02 should execute the locked walkthrough from `20-VALIDATION.md` and update the remaining verification artifacts.

---
*State initialized: 2026-03-05*
*Last updated: 2026-03-08*