<?php
// koneksi.php - Satu-satunya file koneksi MySQLi untuk seluruh aplikasi
// Jangan buat mysqli_connect di file lain, selalu pakai file ini

// Aktifkan error reporting MySQLi supaya error langsung kelihatan
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Konfigurasi database - ubah sesuai kebutuhan lokal
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'sicuti_hrd';
$db_port = 3306;

/**
 * Konek ke server MySQL TANPA pilih database
 * Dipakai oleh bootstrap.php untuk CREATE DATABASE
 */
function db_connect_server() {
    global $db_host, $db_user, $db_pass, $db_port;

    $conn = mysqli_connect($db_host, $db_user, $db_pass, null, $db_port);

    if (!$conn) {
        echo "ERROR: Gagal konek ke server MySQL - " . mysqli_connect_error() . "\n";
        exit(1);
    }

    return $conn;
}

/**
 * Konek ke server MySQL DENGAN pilih database sicuti_hrd
 * Dipakai oleh migrate.php dan semua file CRUD aplikasi
 */
function db_connect() {
    global $db_host, $db_user, $db_pass, $db_name, $db_port;

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

    if (!$conn) {
        echo "ERROR: Gagal konek ke database $db_name - " . mysqli_connect_error() . "\n";
        exit(1);
    }

    return $conn;
}
