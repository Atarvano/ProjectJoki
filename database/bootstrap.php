<?php
// database/bootstrap.php - Satu command untuk setup database dari nol
// Jalankan: php database/bootstrap.php
// Script ini HANYA boleh dijalankan dari CLI (command line)

// ============================================================
// LANGKAH 0: Pastikan dijalankan dari CLI, bukan dari browser
// ============================================================
if (PHP_SAPI !== 'cli') {
    echo "ERROR: Script ini hanya boleh dijalankan dari command line (CLI).\n";
    echo "Contoh: php database/bootstrap.php\n";
    exit(1);
}

echo "==============================================\n";
echo " SICUTI HRD - Database Bootstrap\n";
echo "==============================================\n\n";

// ============================================================
// LANGKAH 1: Load file koneksi
// ============================================================
echo "[1/4] LOAD KONEKSI... ";
require_once __DIR__ . '/../koneksi.php';
echo "OK\n";

// ============================================================
// LANGKAH 2: Konek ke server MySQL (tanpa pilih database)
// ============================================================
echo "[2/4] CONNECT SERVER... ";
$server = db_connect_server();
echo "OK\n";

// ============================================================
// LANGKAH 3: Buat database kalau belum ada
// ============================================================
echo "[3/4] CREATE DATABASE IF NOT EXISTS sicuti_hrd... ";
mysqli_query($server, 'CREATE DATABASE IF NOT EXISTS sicuti_hrd');
echo "OK\n";

// Tutup koneksi server, karena migrate akan buka koneksi sendiri ke database
mysqli_close($server);

// ============================================================
// LANGKAH 4: Jalankan migration
// ============================================================
echo "[4/4] RUN MIGRATE...\n";
require __DIR__ . '/migrate.php';

echo "\n==============================================\n";
echo " DONE! Database sicuti_hrd siap dipakai.\n";
echo "==============================================\n";
