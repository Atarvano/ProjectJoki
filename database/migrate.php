<?php
// database/migrate.php - Runner migration pending-only
// Jalankan: php database/migrate.php
// Atau dipanggil otomatis dari bootstrap.php
// Script ini HANYA boleh dijalankan dari CLI (command line)

// ============================================================
// Pastikan dijalankan dari CLI
// ============================================================
if (PHP_SAPI !== 'cli') {
    echo "ERROR: Script ini hanya boleh dijalankan dari command line (CLI).\n";
    echo "Contoh: php database/migrate.php\n";
    exit(1);
}

// ============================================================
// Load koneksi dan konek ke database sicuti_hrd
// ============================================================
require_once __DIR__ . '/../koneksi.php';
$db = db_connect();

// ============================================================
// Pastikan tabel schema_migrations ada untuk tracking
// ============================================================
echo "  - Cek tabel schema_migrations... ";
$create_tracking = "CREATE TABLE IF NOT EXISTS schema_migrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";
mysqli_query($db, $create_tracking);
echo "OK\n";

// ============================================================
// Baca semua file .sql di folder migrations, urutkan berdasarkan nama
// ============================================================
$migration_dir = __DIR__ . '/migrations';
$files = glob($migration_dir . '/*.sql');

if ($files === false || count($files) === 0) {
    echo "  - Tidak ada file migration ditemukan di database/migrations/\n";
    echo "  - Migration selesai (0 file diproses)\n";
    mysqli_close($db);
    return;
}

sort($files, SORT_STRING);

// ============================================================
// Jalankan migration yang belum pernah dijalankan (pending-only)
// ============================================================
$applied_count = 0;
$skipped_count = 0;

foreach ($files as $file) {
    $filename = basename($file);

    // Cek apakah migration ini sudah pernah dijalankan
    $check = mysqli_prepare($db, 'SELECT 1 FROM schema_migrations WHERE migration = ? LIMIT 1');
    mysqli_stmt_bind_param($check, 's', $filename);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    $already_applied = mysqli_stmt_num_rows($check) > 0;
    mysqli_stmt_close($check);

    if ($already_applied) {
        echo "  - SKIP (sudah): $filename\n";
        $skipped_count++;
        continue;
    }

    // Baca isi file SQL
    $sql = file_get_contents($file);

    if (empty(trim($sql))) {
        echo "  - SKIP (kosong): $filename\n";
        $skipped_count++;
        continue;
    }

    // Eksekusi SQL (bisa berisi banyak statement)
    echo "  - APPLY: $filename... ";
    $result = mysqli_multi_query($db, $sql);

    if (!$result) {
        echo "GAGAL!\n";
        echo "    ERROR: " . mysqli_error($db) . "\n";
        echo "    Migration dihentikan.\n";
        mysqli_close($db);
        exit(1);
    }

    // Flush semua hasil query supaya tidak "commands out of sync"
    do {
        $store = mysqli_store_result($db);
        if ($store instanceof mysqli_result) {
            mysqli_free_result($store);
        }
    } while (mysqli_more_results($db) && mysqli_next_result($db));

    // Catat migration ini sudah dijalankan
    $insert = mysqli_prepare($db, 'INSERT INTO schema_migrations (migration) VALUES (?)');
    mysqli_stmt_bind_param($insert, 's', $filename);
    mysqli_stmt_execute($insert);
    mysqli_stmt_close($insert);

    echo "OK\n";
    $applied_count++;
}

// ============================================================
// Ringkasan
// ============================================================
echo "  - Selesai: $applied_count migration baru, $skipped_count sudah ada\n";

mysqli_close($db);
