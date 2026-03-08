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
