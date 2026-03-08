<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Silakan pilih data dari daftar karyawan.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$employee = null;
$query = 'SELECT k.id, k.nama, k.nik, k.jabatan, k.tanggal_bergabung, k.tanggal_lahir, k.email, k.telepon, k.alamat, k.departemen, u.id AS user_id
          FROM karyawan k
          LEFT JOIN users u ON u.karyawan_id = k.id
          WHERE k.id = ?
          LIMIT 1';
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $employee = mysqli_fetch_assoc($result);
    }

    mysqli_stmt_close($stmt);
}

if (!$employee) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan atau sudah dihapus.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$account_status = !empty($employee['user_id']) ? 'Sudah ada' : 'Belum dibuat';

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
    'page_title' => 'Employee Detail - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Employees', 'url' => '/hr/employees.php'],
        ['label' => 'Detail Karyawan', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

$fields = [
    'nama' => 'Nama',
    'nik' => 'NIK',
    'jabatan' => 'Jabatan',
    'tanggal_bergabung' => 'Tanggal Bergabung',
    'tanggal_lahir' => 'Tanggal Lahir',
    'email' => 'Email',
    'telepon' => 'Telepon',
    'alamat' => 'Alamat',
    'departemen' => 'Departemen',
];

ob_start();
require __DIR__ . '/../views/employee-detail.php';
$page_content = ob_get_clean();
