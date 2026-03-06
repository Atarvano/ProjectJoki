# Phase 18 Manual Checks

Panduan ini dipakai setelah Plan 18-02, 18-03, dan 18-04 selesai agar verifikasi browser dan export bisa diulang dengan langkah yang sama.

## Prasyarat

1. Jalankan aplikasi di Laragon seperti biasa.
2. Pastikan database sudah berisi minimal satu data karyawan valid.
3. Pastikan minimal satu akun employee sudah diprovision dari Phase 17.
4. Siapkan satu akun HR dan satu akun employee untuk login terpisah.
5. Jika perlu pembanding angka, buka phpMyAdmin atau SQL client lokal.

## A. HR Calculator dari Detail Karyawan

1. Login sebagai HR.
2. Buka halaman `http://localhost/SicutiHrd/hr/karyawan.php`.
3. Klik salah satu karyawan lalu masuk ke halaman detail karyawan.
4. Cari tombol/link untuk membuka kalkulator hak cuti dari detail karyawan.
5. Klik tombol/link tersebut.
6. Pastikan halaman `hr/kalkulator.php` terbuka dengan karyawan yang sama sudah terpilih.
7. Pastikan HR tidak lagi diminta mengetik tahun bergabung manual.
8. Cocokkan nama/NIK karyawan di kalkulator dengan data pada halaman detail.
9. Cocokkan tahun bergabung hasil kalkulator dengan `tanggal_bergabung` pada data karyawan.
10. Pastikan tabel hasil cuti muncul tanpa perlu menyimpan laporan ke session.

## B. Laporan HR Live dari Database

1. Saat masih login sebagai HR, buka `http://localhost/SicutiHrd/hr/laporan.php`.
2. Pastikan tabel laporan menampilkan data karyawan dari database, bukan data contoh/preset.
3. Pastikan tidak ada tombol reset laporan session.
4. Pastikan tidak ada badge atau copy yang menyebut data demo, preset, atau laporan tersimpan manual.
5. Uji filter status tahun ini dan pastikan hasil tabel berubah sesuai filter.
6. Klik aksi detail pada salah satu baris.
7. Pastikan aksi detail kembali ke `hr/karyawan-detail.php?id=...` atau alur employee-first yang setara.
8. Bandingkan jumlah baris laporan dengan jumlah karyawan valid di database.

## C. Export Excel dari Data Live

1. Dari halaman laporan HR, klik tombol `Export Excel`.
2. Pastikan browser mengunduh file XLSX tanpa error PHP atau output rusak.
3. Buka file di Microsoft Excel atau LibreOffice.
4. Pastikan sheet `Ringkasan` terbuka normal.
5. Pastikan sheet `Detail` juga ada dan bisa dibuka.
6. Cocokkan jumlah baris karyawan pada sheet `Ringkasan` dengan jumlah baris pada halaman `hr/laporan.php`.
7. Cocokkan satu contoh nama karyawan, tahun bergabung, dan status tahun ini dengan data di halaman laporan.
8. Pastikan export tidak bergantung pada laporan yang pernah disimpan ke session.

## D. Dashboard HR Live

1. Login sebagai HR lalu buka `http://localhost/SicutiHrd/hr/dashboard.php`.
2. Catat angka `Total Karyawan`.
3. Catat angka `Akun Employee Aktif` atau label setara untuk akun login employee yang sudah diprovision.
4. Catat angka `Siap Cuti Tahun Ini`.
5. Bandingkan `Total Karyawan` dengan hasil SQL `SELECT COUNT(*) FROM karyawan;`.
6. Bandingkan jumlah akun employee dengan hasil SQL `SELECT COUNT(*) FROM users WHERE role = 'employee' AND karyawan_id IS NOT NULL;`.
7. Pastikan `Siap Cuti Tahun Ini` menghitung status `Tersedia` dan `Menunggu`.
8. Pastikan tidak ada wording demo, preset, atau laporan tersimpan pada kartu statistik HR.

## E. Dashboard Employee Fokus Tahun 6/7/8

1. Logout dari HR lalu login sebagai employee yang sudah terhubung ke data karyawan.
2. Buka `http://localhost/SicutiHrd/employee/dashboard.php`.
3. Pastikan halaman langsung memuat data cuti milik akun yang login tanpa dropdown atau input manual.
4. Pastikan halaman menonjolkan hanya tahun ke-6, ke-7, dan ke-8.
5. Pastikan baris tahun 1 sampai 5 tidak lagi menjadi fokus utama tampilan.
6. Jika status tahun berjalan belum aktif, pastikan copy yang tampil tetap netral dan informatif.
7. Pastikan tidak ada badge demo, preset, atau label role-play lama.
8. Cocokkan nama karyawan dan tahun bergabung dengan data karyawan di database.

## Hasil Verifikasi

- Tulis `PASS` jika semua bagian A-E sesuai.
- Tulis `FAIL` lalu sebutkan halaman/langkah yang berbeda jika ada perilaku yang tidak sesuai.
