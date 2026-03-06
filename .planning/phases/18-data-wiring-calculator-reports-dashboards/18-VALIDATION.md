---
phase: 18
slug: data-wiring-calculator-reports-dashboards
status: ready
nyquist_compliant: true
wave_0_complete: true
created: 2026-03-06
updated: 2026-03-06
---

# Phase 18 — Validation Strategy

> Kontrak validasi Phase 18 untuk memastikan rewiring calculator, laporan, export, dan dashboard bisa dicek cepat dengan langkah yang sama di setiap plan.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | Plain PHP script checks + manual browser checklist |
| **Config file** | none |
| **Quick run command** | `php tests/phase18_data_wiring_smoke.php` |
| **Full suite command** | `php tests/phase18_data_wiring_smoke.php && php tests/phase18_data_wiring_smoke.php --group=calculator && php tests/phase18_data_wiring_smoke.php --group=reports && php tests/phase18_data_wiring_smoke.php --group=dashboards` |
| **Estimated runtime** | < 10 detik |
| **Wave 0 artifacts** | `tests/phase18_data_wiring_smoke.php`, `tests/phase18_manual_checks.md` |

---

## Sampling Rate

- **Sesudah setiap task commit:** jalankan `php tests/phase18_data_wiring_smoke.php` atau group yang relevan.
- **Sesudah setiap plan selesai:** jalankan full suite smoke Phase 18.
- **Sebelum `/gsd-verify-work`:** jalankan full suite smoke + checklist manual pada `tests/phase18_manual_checks.md`.
- **Max feedback latency:** < 10 detik untuk smoke script.

---

## Wave 0 Deliverables

- [x] `tests/phase18_data_wiring_smoke.php` — smoke script grup `calculator`, `reports`, `dashboards`, plus default run semua grup.
- [x] `tests/phase18_manual_checks.md` — checklist browser dan export untuk calculator, laporan, dashboard HR, dan dashboard employee.
- [x] Semua command otomatis mengarah ke `tests/phase18_data_wiring_smoke.php`.
- [x] Frontmatter Phase 18 validation siap dipakai checker (`nyquist_compliant: true`, `wave_0_complete: true`).

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement Mapping | Test Type | Automated Command | Manual Follow-up | Artifact Status |
|---------|------|------|---------------------|-----------|-------------------|------------------|-----------------|
| 18-01-01 | 18-01 | 1 | CALC-01, CALC-02, CALC-03, CALC-04, RPT-01, RPT-02, RPT-03, RPT-04, DASH-01, DASH-05 | smoke | `php tests/phase18_data_wiring_smoke.php` | none | ✅ ready |
| 18-01-02 | 18-01 | 1 | CALC-01, CALC-02, CALC-03, RPT-01, RPT-02, RPT-03, RPT-04, DASH-01, DASH-05 | smoke + docs | `php tests/phase18_data_wiring_smoke.php && php tests/phase18_data_wiring_smoke.php --group=calculator && php tests/phase18_data_wiring_smoke.php --group=reports && php tests/phase18_data_wiring_smoke.php --group=dashboards` | Review `tests/phase18_manual_checks.md` for browser/export flow | ✅ ready |
| 18-02-01 | 18-02 | 2 | CALC-01, CALC-02, CALC-04 | smoke + lint | `php tests/phase18_data_wiring_smoke.php --group=calculator && php -l hr/kalkulator.php` | Bagian A pada `tests/phase18_manual_checks.md` | ⬜ pending |
| 18-02-02 | 18-02 | 2 | CALC-01 | smoke + lint | `php tests/phase18_data_wiring_smoke.php --group=calculator && php -l hr/karyawan-detail.php` | Bagian A pada `tests/phase18_manual_checks.md` | ⬜ pending |
| 18-03-01 | 18-03 | 2 | RPT-01, RPT-02, RPT-04, CALC-04 | smoke + lint | `php tests/phase18_data_wiring_smoke.php --group=reports && php -l hr/laporan.php` | Bagian B pada `tests/phase18_manual_checks.md` | ⬜ pending |
| 18-03-02 | 18-03 | 2 | RPT-03, RPT-04, CALC-04 | smoke + lint | `php tests/phase18_data_wiring_smoke.php --group=reports && php -l hr/export.php` | Bagian C pada `tests/phase18_manual_checks.md` | ⬜ pending |
| 18-04-01 | 18-04 | 2 | DASH-01, DASH-05 | smoke + lint | `php tests/phase18_data_wiring_smoke.php --group=dashboards && php -l hr/dashboard.php` | Bagian D pada `tests/phase18_manual_checks.md` | ⬜ pending |
| 18-04-02 | 18-04 | 2 | CALC-03, DASH-05 | smoke + lint | `php tests/phase18_data_wiring_smoke.php --group=dashboards && php -l employee/dashboard.php` | Bagian E pada `tests/phase18_manual_checks.md` | ⬜ pending |

**Status legend:** ⬜ pending · ✅ ready/green · ❌ failed

---

## Group Coverage Contract

### Calculator Group
- Target file: `hr/kalkulator.php`
- Command: `php tests/phase18_data_wiring_smoke.php --group=calculator`
- Goal: memastikan marker rewiring calculator bisa dicek cepat selama Plan 18-02.

### Reports Group
- Target files: `hr/laporan.php`, `hr/export.php`
- Command: `php tests/phase18_data_wiring_smoke.php --group=reports`
- Goal: memastikan laporan live DB dan export XLSX bisa dicek dengan satu group selama Plan 18-03.

### Dashboards Group
- Target files: `hr/dashboard.php`, `employee/dashboard.php`
- Command: `php tests/phase18_data_wiring_smoke.php --group=dashboards`
- Goal: memastikan counter HR dan focus view employee tetap terjaga selama Plan 18-04.

---

## Manual Verification Matrix

| Behavior | Requirement | Why Manual | Checklist Reference |
|----------|-------------|------------|---------------------|
| HR membuka kalkulator dari detail karyawan dan melihat employee context yang sama | CALC-01, CALC-02 | Perlu cek alur klik dan kesesuaian data visual | Bagian A |
| Laporan HR menampilkan data live dari database dan aksi detail kembali ke employee record | RPT-01, RPT-02, RPT-04 | Perlu cek tabel, filter, dan alur navigasi | Bagian B |
| Export Excel menghasilkan file XLSX yang bisa dibuka dan jumlah baris cocok dengan laporan | RPT-03 | Integritas file download perlu dicek manual | Bagian C |
| Dashboard HR menunjukkan total karyawan, akun employee, dan siap cuti tahun ini dari data live | DASH-01 | Perlu pembandingan dengan SQL/DB lokal | Bagian D |
| Dashboard employee hanya menonjolkan tahun 6, 7, dan 8 untuk akun yang login | CALC-03, DASH-05 | Perlu cek tampilan akhir dan copy netral | Bagian E |

---

## Execution Rules

1. Jangan ubah nama file smoke script selama Phase 18 berlangsung.
2. Setiap plan Phase 18 wajib memakai command group yang sesuai sebelum commit task.
3. Jika smoke gagal, perbaiki dulu sebelum lanjut ke task berikutnya.
4. Jika perilaku lolos smoke tetapi gagal manual, catat di summary plan terkait.
5. Semua requirement Phase 18 harus terlacak ke minimal satu baris task di tabel verifikasi ini.

---

## Validation Sign-Off

- [x] Semua 8 task executable untuk Plan 18-01 s.d. 18-04 sudah tercantum.
- [x] Semua task punya wave number, plan ID, dan mapping requirement yang jelas.
- [x] Semua command otomatis mengarah ke `tests/phase18_data_wiring_smoke.php`.
- [x] Tidak ada row `MISSING`, placeholder command, atau mapping plan/wave yang salah.
- [x] Kontrak Wave 0 siap dipakai checker tanpa inferensi tambahan.

**Approval:** ready
