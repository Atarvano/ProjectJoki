<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = [
        'type' => 'info',
        'message' => 'Penghapusan karyawan hanya bisa dilakukan lewat tombol Hapus pada daftar karyawan.'
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan yang akan dihapus tidak valid. Silakan coba lagi dari daftar karyawan.'
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

$sql = 'DELETE FROM karyawan WHERE id = ? LIMIT 1';
$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan belum bisa dihapus saat ini. Silakan coba lagi beberapa saat lagi.'
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

if ($ok && mysqli_stmt_affected_rows($stmt) > 0) {
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Data karyawan berhasil dihapus permanen. Jika ada akun login yang terhubung, akun tersebut ikut terhapus otomatis.'
    ];
} elseif ($ok) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan atau sudah dihapus sebelumnya.'
    ];
} else {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Penghapusan data karyawan gagal diproses. Silakan coba lagi.'
    ];
}

mysqli_stmt_close($stmt);

header('Location: /hr/karyawan.php');
exit;
?>
