<?php
// Session guard — idempotent, safe to call multiple times
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/cuti-calculator.php';

function getSeedReports() {
    $seeds = [
        ['nama' => 'Budi Santoso', 'tahun_bergabung' => 2018],
        ['nama' => 'Siti Rahayu', 'tahun_bergabung' => 2020],
        ['nama' => 'Ahmad Fauzi', 'tahun_bergabung' => 2022]
    ];

    $result = [];
    foreach ($seeds as $seed) {
        $calc = hitungHakCuti($seed['tahun_bergabung']);
        $total_cuti = array_sum(array_column($calc['data'], 'hari_cuti'));
        $result[] = [
            'nama' => $seed['nama'],
            'tahun_bergabung' => $seed['tahun_bergabung'],
            'total_cuti' => $total_cuti,
            'is_sample' => true
        ];
    }
    return $result;
}

function initReports() {
    if (!isset($_SESSION['reports'])) {
        $_SESSION['reports'] = getSeedReports();
    }
    return $_SESSION['reports'];
}

function getReports() {
    return isset($_SESSION['reports']) ? $_SESSION['reports'] : [];
}

function saveReport($nama, $tahunBergabung) {
    $calc = hitungHakCuti($tahunBergabung);
    $total_cuti = array_sum(array_column($calc['data'], 'hari_cuti'));
    
    $entry = [
        'nama' => $nama,
        'tahun_bergabung' => (int)$tahunBergabung,
        'total_cuti' => $total_cuti,
        'is_sample' => false
    ];

    $index = findReportIndex($nama, $tahunBergabung);
    
    if ($index !== false) {
        $_SESSION['reports'][$index] = $entry;
        return 'updated';
    } else {
        if (!isset($_SESSION['reports'])) {
            $_SESSION['reports'] = [];
        }
        $_SESSION['reports'][] = $entry;
        return 'created';
    }
}

function findReportIndex($nama, $tahunBergabung) {
    if (!isset($_SESSION['reports'])) return false;
    
    foreach ($_SESSION['reports'] as $index => $report) {
        if ($report['nama'] === $nama && (int)$report['tahun_bergabung'] === (int)$tahunBergabung) {
            return $index;
        }
    }
    return false;
}

function resetReports() {
    unset($_SESSION['reports']);
}

function countReports() {
    return isset($_SESSION['reports']) ? count($_SESSION['reports']) : 0;
}

function countPresetKaryawan() {
    return 3;
}
