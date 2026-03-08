<?php
function build_provision_password($nik, $tanggal_lahir)
{
    $nik = trim((string) $nik);
    if ($nik === '') {
        return null;
    }

    $tanggal_lahir_text = trim((string) $tanggal_lahir);
    $date = DateTime::createFromFormat('Y-m-d', $tanggal_lahir_text);
    $date_errors = DateTime::getLastErrors();
    $has_date_error = is_array($date_errors) && (($date_errors['warning_count'] ?? 0) > 0 || ($date_errors['error_count'] ?? 0) > 0);

    if (!$date || $has_date_error || $date->format('Y-m-d') !== $tanggal_lahir_text) {
        return null;
    }

    return $nik . $date->format('dmY');
}

if (defined('KARYAWAN_PROVISION_TEST_MODE')) {
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = [
        'type' => 'info',
        'message' => 'Aksi hanya bisa lewat tombol Buat Akun Login.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak valid. Silakan ulangi dari daftar karyawan.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$karyawan = null;
$sql_get = 'SELECT k.id, k.nik, k.tanggal_lahir, u.id AS user_id
            FROM karyawan k
            LEFT JOIN users u ON u.karyawan_id = k.id
            WHERE k.id = ?
            LIMIT 1';
$stmt_get = mysqli_prepare($koneksi, $sql_get);

if (!$stmt_get) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Akun login belum bisa dibuat saat ini. Silakan coba lagi.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

mysqli_stmt_bind_param($stmt_get, 'i', $id);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);

if ($result_get) {
    $karyawan = mysqli_fetch_assoc($result_get);
}

mysqli_stmt_close($stmt_get);

if (!$karyawan) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan atau sudah dihapus.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

if (!empty($karyawan['user_id'])) {
    $_SESSION['flash'] = [
        'type' => 'info',
        'message' => 'Akun login untuk karyawan ini sudah ada. Tidak ada perubahan data.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$username = trim((string) ($karyawan['nik'] ?? ''));
$password_plain = build_provision_password($username, $karyawan['tanggal_lahir'] ?? '');

if ($password_plain === null) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Tanggal lahir karyawan tidak valid, akun login belum bisa dibuat.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
$role = 'employee';

$sql_insert = 'INSERT INTO users (karyawan_id, username, password, role, is_active) VALUES (?, ?, ?, ?, 1)';
$stmt_insert = mysqli_prepare($koneksi, $sql_insert);

if (!$stmt_insert) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Akun login belum bisa dibuat saat ini. Silakan coba lagi.'
    ];
    header('Location: /hr/employees.php');
    exit;
}

$karyawan_id = (int) $karyawan['id'];
mysqli_stmt_bind_param($stmt_insert, 'isss', $karyawan_id, $username, $password_hashed, $role);
$ok_insert = mysqli_stmt_execute($stmt_insert);
mysqli_stmt_close($stmt_insert);

if ($ok_insert) {
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Akun login karyawan berhasil dibuat.',
        'credentials' => [
            'username' => $username,
            'password_awal' => $password_plain,
            'pattern_example' => $password_plain,
        ],
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Akun login gagal dibuat. Silakan cek data NIK karyawan dan coba lagi.'
    ];
}

header('Location: /hr/employees.php');
exit;
