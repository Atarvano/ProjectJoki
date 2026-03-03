# Sicuti HRD

## What This Is

Sicuti HRD adalah website internal sederhana untuk membantu HR/Admin menghitung hak cuti karyawan berdasarkan tahun masuk kerja. Aplikasi v1 fokus pada alur landing page, login, lalu dashboard kalkulasi cuti dengan data dummy namun tetap berbasis file `.php`. Target utamanya adalah validasi alur bisnis cuti dan format laporan sebelum masuk ke implementasi data real di v2.

## Core Value

HR/Admin bisa menghitung hak cuti karyawan dengan cepat dan konsisten dari tahun masuk, lalu menyimpan hasilnya sebagai laporan yang bisa diekspor ke Excel.

## Requirements

### Validated

(None yet - ship to validate)

### Active

- [ ] Website memiliki landing page, login page, dan dashboard kalkulasi cuti
- [ ] User dapat memasukkan tahun/tanggal masuk karyawan untuk menghitung skema hak cuti
- [ ] Sistem menampilkan tabel cuti besar 6 tahun berturut-turut serta alokasi tahun ke-7 dan ke-8 masing-masing 6 hari
- [ ] Hasil perhitungan dapat disimpan sebagai laporan cuti
- [ ] Laporan cuti dapat diekspor dalam format Excel
- [ ] Implementasi v1 tetap menggunakan PHP native prosedural tanpa OOP

### Out of Scope

- Integrasi database produksi - v1 hanya dummy data untuk validasi alur
- Integrasi autentikasi enterprise (SSO, RBAC kompleks) - v1 cukup login sederhana
- Framework PHP/Laravel/OOP architecture - dibatasi ke PHP native prosedural

## Context

Inisiatif ini dimulai karena kebutuhan menghitung cuti terasa susah dijelaskan secara verbal dan perlu visualisasi alur end-to-end. User meminta brainstorming awal namun tetap ingin deliverable konkret berupa web sederhana yang langsung bisa dipakai untuk simulasi hitung cuti. Domain utama adalah administrasi HR dengan fokus cepat pada kalkulasi hak cuti berdasarkan masa kerja.

## Constraints

- **Tech stack**: PHP native prosedural (`.php`) - harus konsisten dengan preferensi implementasi
- **Release scope**: v1 frontend + dummy data - untuk validasi cepat sebelum logic data real
- **Architecture**: Tanpa OOP dan tanpa framework - menjaga kesederhanaan implementasi awal
- **Output format**: Wajib ada export Excel - dibutuhkan untuk alur laporan HR

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| v1 dibangun sebagai web `.php` dengan dummy data | Mempercepat validasi alur UX dan business rule tanpa beban integrasi backend penuh | - Pending |
| Stack tetap PHP native prosedural (tanpa OOP/framework) | Sesuai preferensi user dan memudahkan maintainability sesuai gaya tim | - Pending |
| Scope awal mencakup landing -> login -> dashboard hitung cuti -> laporan -> export Excel | Ini adalah alur minimum yang langsung bernilai untuk use case HR | - Pending |

---
*Last updated: 2026-03-03 after initialization*
