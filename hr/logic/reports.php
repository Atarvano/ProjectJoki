<?php
require_once __DIR__ . '/../../includes/cuti-calculator.php';

$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$filter_status = isset($_GET['filter_status']) ? trim((string) $_GET['filter_status']) : '';
$allowed_filters = ['Tersedia', 'Menunggu', 'Rencana'];
if (!in_array($filter_status, $allowed_filters, true)) {
    $filter_status = '';
}

$current_year = (int) date('Y');
$report_rows = [];
$invalid_join_date_count = 0;

$sql = 'SELECT id, nik, nama, tanggal_bergabung FROM karyawan ORDER BY nik ASC, nama ASC';
$result = mysqli_query($koneksi, $sql);

if ($result instanceof mysqli_result) {
    while ($karyawan = mysqli_fetch_assoc($result)) {
        $tanggal_bergabung = isset($karyawan['tanggal_bergabung']) ? trim((string) $karyawan['tanggal_bergabung']) : '';

        if ($tanggal_bergabung === '' || strtotime($tanggal_bergabung) === false) {
            $invalid_join_date_count++;
            continue;
        }

        $tahun_bergabung = (int) date('Y', strtotime($tanggal_bergabung));
        $calc = hitungHakCuti($tahun_bergabung);
        $status_tahun_ini = '—';
        $status_class = 'bg-secondary';

        foreach ($calc['data'] as $row) {
            if ((int) $row['tahun_kalender'] === $current_year) {
                $status_tahun_ini = $row['status'];
                $status_class = $row['status_class'];
                break;
            }
        }

        if ($filter_status !== '' && $status_tahun_ini !== $filter_status) {
            continue;
        }

        $report_rows[] = [
            'id' => (int) $karyawan['id'],
            'nik' => $karyawan['nik'],
            'nama' => $karyawan['nama'],
            'tahun_bergabung' => $tahun_bergabung,
            'total_cuti' => array_sum(array_column($calc['data'], 'hari_cuti')),
            'status_tahun_ini' => $status_tahun_ini,
            'status_class' => $status_class,
        ];
    }

    mysqli_free_result($result);
}

$profile_label = trim((string) ($_SESSION['nama'] ?? ''));
if ($profile_label === '') {
    $profile_label = trim((string) ($_SESSION['username'] ?? 'HR'));
}
if ($profile_label === '') {
    $profile_label = 'HR';
}

$profile_initials = strtoupper(substr($profile_label, 0, 2));

$dashboard_context = [
    'role' => 'hr',
    'active_nav' => 'reports',
    'page_title' => 'Reports - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => 'dashboard.php'],
        ['label' => 'Laporan Hak Cuti', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
require __DIR__ . '/../views/reports.php';
$page_content = ob_get_clean();
