<?php
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$employee_list = [];
$query_error = null;

$sql = 'SELECT k.id, k.nik, k.nama, k.departemen, k.jabatan, k.tanggal_bergabung, u.id AS user_id
        FROM karyawan k
        LEFT JOIN users u ON u.karyawan_id = k.id
        ORDER BY k.id DESC';
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $employee_list[] = $row;
        }
    } else {
        $query_error = 'Data karyawan belum bisa ditampilkan saat ini. Silakan coba lagi sebentar.';
    }

    mysqli_stmt_close($stmt);
} else {
    $query_error = 'Data karyawan belum bisa ditampilkan saat ini. Silakan coba lagi sebentar.';
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
    'active_nav' => 'kelola-karyawan',
    'page_title' => 'Employees - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Employees', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
require __DIR__ . '/../views/employees.php';
$page_content = ob_get_clean();
