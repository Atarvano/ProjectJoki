# Phase 17 Account Provisioning - Manual Test Checklist

Dokumen ini dipakai untuk validasi ulang PROV-01 sampai PROV-04 secara end-to-end.

## Persiapan

1. Pastikan aplikasi berjalan di Laragon dan database `sicuti_hrd` sudah aktif.
2. Login sebagai HR dari `http://localhost/SicutiHrd/login.php`.
3. Siapkan minimal 1 data karyawan yang belum punya akun login.

## A. Validasi PROV-01 (Visibilitas Tombol Provisioning)

1. Buka halaman daftar karyawan: `http://localhost/SicutiHrd/hr/karyawan.php`.
2. Cari baris karyawan dengan status akun **Belum dibuat**.
3. Pastikan tombol **Buat Akun Login** terlihat pada baris tersebut.
4. Cari baris karyawan dengan status akun **Sudah ada**.
5. Pastikan tombol **Buat Akun Login** tidak muncul pada baris tersebut.
6. Klik tombol **Detail** pada karyawan yang statusnya **Belum dibuat**.
7. Di halaman detail (`hr/karyawan-detail.php?id=...`), pastikan CTA provisioning tampil sebagai aksi utama.
8. Buka detail karyawan yang statusnya **Sudah ada**.
9. Pastikan yang tampil adalah badge/status non-aksi (bukan tombol provisioning).

## B. Validasi PROV-02 + PROV-03 + PROV-04 (Provisioning Sukses + Flash Sekali)

1. Dari list atau detail, klik **Buat Akun Login** pada karyawan yang belum punya akun.
2. Pastikan diarahkan kembali ke daftar karyawan (`/hr/karyawan.php`).
3. Di flash sukses, pastikan ada 2 baris kredensial dengan label tepat:
   - `NIK (Username): ...`
   - `Password awal: ...`
4. Pastikan flash menampilkan peringatan bahwa kredensial hanya ditampilkan sekali, dan instruksi untuk segera mencatat/menyampaikan.
5. Catat nilai `NIK (Username)` dan `Password awal` untuk uji login.
6. Refresh halaman (`F5`).
7. Pastikan flash kredensial sudah hilang (tidak tampil lagi).

## C. Validasi Penolakan Provisioning Ulang (Duplicate Rejection)

1. Pada karyawan yang baru diprovision, coba akses provisioning lagi (contoh lewat halaman detail/list jika masih tersedia, atau submit POST manual jika perlu untuk uji teknis).
2. Pastikan sistem menolak dengan flash info bahwa akun login sudah ada.
3. Pastikan tidak ada akun user tambahan yang tercipta untuk karyawan tersebut.

## D. Validasi Login End-to-End dengan Kredensial Hasil Provisioning

1. Logout dari akun HR.
2. Buka login page: `http://localhost/SicutiHrd/login.php`.
3. Isi:
   - Username: nilai dari `NIK (Username)`
   - Password: nilai dari `Password awal`
4. Klik tombol login.
5. Pastikan login berhasil ke dashboard employee (bukan HR).
6. Pastikan nama/role pada topbar sesuai akun employee.

## SQL Verifikasi (Copy-Paste)

> Ganti `NIK_TARGET` dengan NIK karyawan yang diuji.

### 1) Verifikasi linkage users -> karyawan + role + active flag

```sql
SELECT
  k.id AS karyawan_id,
  k.nik,
  k.nama,
  u.id AS user_id,
  u.karyawan_id,
  u.username,
  u.role,
  u.is_active
FROM karyawan k
LEFT JOIN users u ON u.karyawan_id = k.id
WHERE k.nik = 'NIK_TARGET';
```

Ekspektasi:
- `u.karyawan_id` sama dengan `karyawan_id`
- `u.username` = `k.nik`
- `u.role` = `employee`
- `u.is_active` = `1`

### 2) Verifikasi tidak ada akun duplikat untuk NIK yang sama

```sql
SELECT username, COUNT(*) AS total
FROM users
WHERE username = 'NIK_TARGET'
GROUP BY username;
```

Ekspektasi:
- `total` harus `1`

### 3) Verifikasi tidak ada lebih dari 1 akun untuk satu karyawan

```sql
SELECT karyawan_id, COUNT(*) AS total
FROM users
WHERE karyawan_id = (
  SELECT id FROM karyawan WHERE nik = 'NIK_TARGET' LIMIT 1
)
GROUP BY karyawan_id;
```

Ekspektasi:
- `total` harus `1`

## Catatan Hasil Uji

- PROV-01: [ ] PASS / [ ] FAIL
- PROV-02: [ ] PASS / [ ] FAIL
- PROV-03: [ ] PASS / [ ] FAIL
- PROV-04: [ ] PASS / [ ] FAIL
