---
phase: 20
slug: provisioning-e2e-verification-flash-contract-alignment
status: draft
nyquist_compliant: true
wave_0_complete: true
created: 2026-03-08
---

# Phase 20 — Validation Strategy

> Kontrak validasi Phase 20 untuk menutup bukti runtime auth + provisioning sebelum milestone ditutup.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | Plain PHP smoke scripts + browser checklist manual |
| **Config file** | none — file markdown + script PHP mandiri |
| **Quick run command** | `php tests/provisioning_endpoint_test.php && php tests/phase19_auth_session_smoke.php` |
| **Full suite command** | `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php` |
| **Estimated runtime** | ~15 detik + waktu browser manual |

---

## Sampling Rate

- **Setelah setiap task commit:** Jalankan `php tests/provisioning_endpoint_test.php && php tests/phase19_auth_session_smoke.php`
- **Sebelum tutup plan:** Jalankan `php tests/provisioning_endpoint_test.php && php tests/phase19_auth_session_smoke.php`
- **Sebelum browser walkthrough final:** Pastikan full suite hijau
- **Max feedback latency:** < 20 detik untuk smoke command

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 20-01-01 | 01 | 1 | PROV-01, PROV-02, PROV-03, PROV-04 | smoke | `php tests/provisioning_endpoint_test.php && php -l hr/karyawan-provision.php` | via plan files | ✅ green |
| 20-01-02 | 01 | 1 | AUTH-01, AUTH-02, AUTH-03, RBAC-01, RBAC-02, RBAC-05, PROV-01, PROV-04, DASH-04 | docs + manual checkpoint prep | `php -r "exit(file_exists('.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-VALIDATION.md') ? 0 : 1);"` | created in this plan | ✅ green |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [x] `tests/provisioning_endpoint_test.php` memeriksa kontrak flash terstruktur `credentials.username`, `credentials.password_awal`, dan `pattern_example`
- [x] `tests/phase19_auth_session_smoke.php` tetap jadi baseline guard/auth untuk redirect login dan wrong-role
- [x] File `20-VALIDATION.md` menyimpan walkthrough browser terkunci untuk proof milestone

### Wave 0 Baseline Notes

- Fokus Phase 20 tetap ringan: smoke PHP untuk baseline, browser manual untuk bukti akhir.
- Halaman utama walkthrough dikunci di `hr/karyawan.php` karena action provisioning dimulai dari sana dan flash sukses kembali ke sana.
- Jangan tambah framework test baru, screenshot wajib, atau matrix enterprise.

---

## Manual-Only Verifications

Ikuti urutan ini persis saat plan browser final dijalankan:

1. **HR login**
   - Buka `/login.php`.
   - Login sebagai user HR yang valid.
   - **Hasil yang harus terlihat:** redirect ke dashboard HR, dan halaman yang diuji tidak menampilkan `Demo v1` atau `Akses demo`.

2. **Masuk ke daftar karyawan dan provision dari row action**
   - Buka `/hr/karyawan.php`.
   - Cari 1 karyawan dengan status akun `Belum dibuat`.
   - Klik tombol **Buat Akun Login** pada row karyawan itu.
   - **Hasil yang harus terlihat:** kembali ke `/hr/karyawan.php`.

3. **Tangkap flash kredensial terstruktur**
   - Di alert sukses, pastikan ada blok khusus kredensial.
   - **Hasil yang harus terlihat:** ada `NIK (Username)`, `Password awal`, catatan pola `NIK + tanggal lahir (DDMMYYYY)`, dan warning bahwa kredensial hanya tampil sekali.
   - Catat username dan password awal yang muncul.

4. **Refresh untuk bukti one-time flash**
   - Refresh `/hr/karyawan.php` sekali.
   - **Hasil yang harus terlihat:** blok kredensial tadi hilang dan tidak muncul lagi.

5. **HR logout dengan kontrol nyata**
   - Klik tombol/tautan **Keluar** yang benar-benar ada di UI HR.
   - **Hasil yang harus terlihat:** redirect ke `/login.php`.

6. **Employee login dengan kredensial hasil provision**
   - Di `/login.php`, login memakai username NIK dan password awal yang tadi dicatat.
   - **Hasil yang harus terlihat:** redirect ke `/employee/dashboard.php` dan halaman tidak menampilkan `Demo v1` atau `Akses demo`.

7. **Bukti akses dashboard employee**
   - Pastikan dashboard employee terbuka normal.
   - **Hasil yang harus terlihat:** data employee terbuka tanpa error auth.

8. **Bukti wrong-role redirect**
   - Saat masih login sebagai employee, buka halaman HR seperti `/hr/karyawan.php`.
   - **Hasil yang harus terlihat:** employee tidak boleh masuk halaman HR dan diarahkan kembali ke `/employee/dashboard.php`.

9. **Employee logout dengan kontrol nyata**
   - Klik tombol/tautan **Keluar** dari UI employee.
   - **Hasil yang harus terlihat:** redirect ke `/login.php`.

10. **Bukti post-logout guard**
    - Buka ulang atau refresh halaman protected terakhir yang tadi dipakai employee, misalnya `/employee/dashboard.php`.
    - **Hasil yang harus terlihat:** browser diarahkan ke `/login.php`.

---

## Demo-Free Proof Notes

Catat singkat saat browser walkthrough:

- `login.php` tidak menampilkan `Demo v1`
- `login.php` tidak menampilkan `Akses demo`
- `hr/karyawan.php` tidak menampilkan `Demo v1`
- `hr/karyawan.php` tidak menampilkan `Akses demo`
- `employee/dashboard.php` tidak menampilkan `Demo v1`
- `employee/dashboard.php` tidak menampilkan `Akses demo`

---

## Command Checklist

- **Quick run saat edit:** `php tests/provisioning_endpoint_test.php && php tests/phase19_auth_session_smoke.php`
- **Syntax check endpoint:** `php -l hr/karyawan-provision.php`
- **Full suite sebelum tutup phase browser plan:** `php tests/provisioning_endpoint_test.php && php tests/phase18_data_wiring_smoke.php && php tests/phase19_auth_session_smoke.php`

---

## Optional DB Spot Check

Jika verifier ingin cek DB tanpa menambah scope besar, boleh pakai catatan ini setelah provisioning sukses:

```sql
SELECT id, karyawan_id, username, role, is_active
FROM users
WHERE username = 'NIK_YANG_BARU_DIPROVISION';
```

**Hasil yang diharapkan:** satu row `users` dengan `role = 'employee'`, `is_active = 1`, dan `karyawan_id` terisi.

---

## Validation Sign-Off

- [x] Semua task di plan punya verify command atau artifact check
- [x] Walkthrough browser dikunci ke alur `HR login -> provisioning from employee list -> flash -> logout -> employee login -> guard proof -> logout -> reopen protected page`
- [x] Dua logout proof tercakup
- [x] Wrong-role employee -> HR redirect tercakup
- [x] Bukti demo-free UI tercakup
- [x] `nyquist_compliant: true` set di frontmatter

**Approval:** Menunggu browser walkthrough pada Plan 20-02.
