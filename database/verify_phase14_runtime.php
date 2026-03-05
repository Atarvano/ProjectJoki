<?php

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "FAIL: CLI-only script. Run with: php database/verify_phase14_runtime.php\n");
    exit(1);
}

$failedChecks = [];

ob_start();
require_once __DIR__ . '/../koneksi.php';
$koneksiOutput = trim((string) ob_get_clean());

if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    echo "FAIL: koneksi.php provides mysqli connection in $koneksi\n";
    $failedChecks[] = 'connection-object';
    fwrite(STDERR, "FAILED CHECKS: " . implode(', ', $failedChecks) . "\n");
    exit(1);
}

if ($koneksiOutput !== '') {
    echo "PASS: koneksi.php included (suppressed output: {$koneksiOutput})\n";
} else {
    echo "PASS: koneksi.php included\n";
}

if (mysqli_ping($koneksi)) {
    echo "PASS: mysqli_ping connection to sicuti_hrd\n";
} else {
    echo "FAIL: mysqli_ping connection to sicuti_hrd\n";
    $failedChecks[] = 'mysqli-ping';
}

$tableNames = ['karyawan', 'users'];
foreach ($tableNames as $tableName) {
    $safeTableName = mysqli_real_escape_string($koneksi, $tableName);
    $tableCheckSql = "SELECT COUNT(*) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$safeTableName}'";
    $tableCheckResult = mysqli_query($koneksi, $tableCheckSql);

    if ($tableCheckResult) {
        $tableRow = mysqli_fetch_assoc($tableCheckResult);
        mysqli_free_result($tableCheckResult);

        if ((int) $tableRow['total'] > 0) {
            echo "PASS: table exists -> {$tableName}\n";
        } else {
            echo "FAIL: table exists -> {$tableName}\n";
            $failedChecks[] = 'table-' . $tableName;
        }
    } else {
        echo "FAIL: table exists -> {$tableName} (query error: " . mysqli_error($koneksi) . ")\n";
        $failedChecks[] = 'table-' . $tableName;
    }
}

$expectedKaryawanColumns = [
    'id',
    'nik',
    'nama',
    'tanggal_lahir',
    'email',
    'telepon',
    'alamat',
    'jabatan',
    'departemen',
    'tanggal_bergabung',
    'created_at',
    'updated_at',
];

$karyawanColumns = [];
$karyawanColumnResult = mysqli_query($koneksi, 'SHOW COLUMNS FROM `karyawan`');
if ($karyawanColumnResult) {
    while ($columnRow = mysqli_fetch_assoc($karyawanColumnResult)) {
        $karyawanColumns[] = $columnRow['Field'];
    }
    mysqli_free_result($karyawanColumnResult);

    $missingKaryawanColumns = array_diff($expectedKaryawanColumns, $karyawanColumns);
    if (count($karyawanColumns) === 12 && empty($missingKaryawanColumns)) {
        echo "PASS: karyawan has 12 expected columns\n";
    } else {
        echo "FAIL: karyawan has 12 expected columns\n";
        $failedChecks[] = 'karyawan-columns';
    }
} else {
    echo "FAIL: karyawan has 12 expected columns (query error: " . mysqli_error($koneksi) . ")\n";
    $failedChecks[] = 'karyawan-columns';
}

$requiredUsersColumns = [
    'id',
    'karyawan_id',
    'username',
    'password',
    'role',
    'is_active',
    'created_at',
];

$usersColumns = [];
$usersColumnResult = mysqli_query($koneksi, 'SHOW COLUMNS FROM `users`');
if ($usersColumnResult) {
    while ($usersRow = mysqli_fetch_assoc($usersColumnResult)) {
        $usersColumns[] = $usersRow['Field'];
    }
    mysqli_free_result($usersColumnResult);

    $missingUsersColumns = array_diff($requiredUsersColumns, $usersColumns);
    if (empty($missingUsersColumns)) {
        echo "PASS: users has required auth columns\n";
    } else {
        echo "FAIL: users has required auth columns\n";
        $failedChecks[] = 'users-columns';
    }
} else {
    echo "FAIL: users has required auth columns (query error: " . mysqli_error($koneksi) . ")\n";
    $failedChecks[] = 'users-columns';
}

$fkSql = "
    SELECT COUNT(*) AS total
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'karyawan_id'
      AND REFERENCED_TABLE_NAME = 'karyawan'
      AND REFERENCED_COLUMN_NAME = 'id'
";
$fkResult = mysqli_query($koneksi, $fkSql);
if ($fkResult) {
    $fkRow = mysqli_fetch_assoc($fkResult);
    mysqli_free_result($fkResult);

    if ((int) $fkRow['total'] > 0) {
        echo "PASS: FK users.karyawan_id -> karyawan.id exists\n";
    } else {
        echo "FAIL: FK users.karyawan_id -> karyawan.id exists\n";
        $failedChecks[] = 'users-fk-karyawan';
    }
} else {
    echo "FAIL: FK users.karyawan_id -> karyawan.id exists (query error: " . mysqli_error($koneksi) . ")\n";
    $failedChecks[] = 'users-fk-karyawan';
}

$seedSql = "SELECT id, username, role, is_active FROM users WHERE username = 'HR0001' AND role = 'hr' LIMIT 1";
$seedResult = mysqli_query($koneksi, $seedSql);
if ($seedResult) {
    if (mysqli_num_rows($seedResult) > 0) {
        $seedRow = mysqli_fetch_assoc($seedResult);
        echo "PASS: HR seed account exists -> " . $seedRow['username'] . " (role=" . $seedRow['role'] . ")\n";
    } else {
        echo "FAIL: HR seed account exists -> HR0001 (role=hr)\n";
        $failedChecks[] = 'hr-seed';
    }
    mysqli_free_result($seedResult);
} else {
    echo "FAIL: HR seed account exists -> HR0001 (query error: " . mysqli_error($koneksi) . ")\n";
    $failedChecks[] = 'hr-seed';
}

$role = 'hr';
$preparedSql = 'SELECT id, username, role FROM users WHERE role = ? LIMIT 5';
$preparedStmt = mysqli_prepare($koneksi, $preparedSql);

if ($preparedStmt) {
    mysqli_stmt_bind_param($preparedStmt, 's', $role);

    if (mysqli_stmt_execute($preparedStmt)) {
        $rowCount = 0;
        $result = mysqli_stmt_get_result($preparedStmt);

        if ($result !== false) {
            $rowCount = mysqli_num_rows($result);
            mysqli_free_result($result);
        } else {
            mysqli_stmt_store_result($preparedStmt);
            $rowCount = mysqli_stmt_num_rows($preparedStmt);
        }

        echo "PASS: prepared statement execute for role=hr (rows={$rowCount})\n";
    } else {
        echo "FAIL: prepared statement execute for role=hr (stmt error: " . mysqli_stmt_error($preparedStmt) . ")\n";
        $failedChecks[] = 'prepared-statement';
    }

    mysqli_stmt_close($preparedStmt);
} else {
    echo "FAIL: prepared statement execute for role=hr (prepare error: " . mysqli_error($koneksi) . ")\n";
    $failedChecks[] = 'prepared-statement';
}

if (empty($failedChecks)) {
    echo "PASS: all runtime checks passed\n";
    exit(0);
}

fwrite(STDERR, "FAILED CHECKS: " . implode(', ', $failedChecks) . "\n");
exit(1);
