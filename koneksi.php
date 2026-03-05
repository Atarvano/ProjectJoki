<?php
// koneksi.php - Koneksi database MySQLi
// Gunakan: require_once 'koneksi.php'; lalu pakai $koneksi

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'sicuti_hrd';

$koneksi = mysqli_connect($host, $user, $pass, $dbname);

if (!$koneksi) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

// -- Sanity check: prepared statement contoh (relasi user<->karyawan) --
// Cek apakah tabel users dan karyawan sudah ada
$cek_tabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'users'");
if ($cek_tabel && mysqli_num_rows($cek_tabel) > 0) {
    $stmt = mysqli_prepare(
        $koneksi,
        'SELECT u.username, k.nama FROM users u LEFT JOIN karyawan k ON u.karyawan_id = k.id WHERE u.role = ? LIMIT 1'
    );

    if ($stmt) {
        $role = 'hr';
        mysqli_stmt_bind_param($stmt, 's', $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result instanceof mysqli_result) {
            mysqli_free_result($result);
        }

        // Relasi user<->karyawan siap
        mysqli_stmt_close($stmt);
    }
}
