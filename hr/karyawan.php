<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';

$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$karyawan_list = [];
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
            $karyawan_list[] = $row;
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
    'page_title' => 'Kelola Karyawan - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Kelola Karyawan', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="h3 mb-0 text-gray-800">Kelola Karyawan</h2>
    <a href="/hr/karyawan-tambah.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Karyawan
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?> alert-dismissible fade show mb-4" role="alert">
        <?php echo htmlspecialchars($flash['message'] ?? 'Informasi diperbarui.'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($query_error !== null): ?>
    <div class="alert alert-danger mb-4" role="alert">
        <?php echo htmlspecialchars($query_error); ?>
    </div>
<?php endif; ?>

<?php if ($query_error === null && count($karyawan_list) === 0): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <div class="mb-3">
                <i class="bi bi-people" style="font-size: 3.5rem; opacity: 0.45;"></i>
            </div>
            <h3 class="h5 mb-2">Belum ada data karyawan</h3>
            <p class="text-muted mb-4">Silakan tambah data karyawan dulu supaya bisa dikelola dari halaman ini.</p>
            <a href="/hr/karyawan-tambah.php" class="btn btn-primary px-4">
                <i class="bi bi-plus-circle me-2"></i>Tambah Karyawan
            </a>
        </div>
    </div>
<?php endif; ?>

<?php if ($query_error === null && count($karyawan_list) > 0): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4">NIK</th>
                                <th class="py-3">Nama</th>
                                <th class="py-3">Departemen</th>
                                <th class="py-3">Jabatan</th>
                                <th class="py-3">Tanggal Bergabung</th>
                                <th class="py-3">Akun Login</th>
                                <th class="py-3 text-center pe-4">Aksi</th>
                            </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($karyawan_list as $row): ?>
                            <?php
                            $id = (int) $row['id'];
                            $tanggal_bergabung = '-';
                            $akun_login_status = !empty($row['user_id']) ? 'Sudah ada' : 'Belum dibuat';
                            if (!empty($row['tanggal_bergabung'])) {
                                $tanggal_bergabung = date('d M Y', strtotime($row['tanggal_bergabung']));
                            }
                            ?>
                            <tr class="karyawan-row" data-edit-url="/hr/karyawan-edit.php?id=<?php echo $id; ?>">
                                <td class="ps-4 fw-medium"><?php echo htmlspecialchars($row['nik'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['departemen'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($row['jabatan'] ?: '-'); ?></td>
                                <td><?php echo htmlspecialchars($tanggal_bergabung); ?></td>
                                <td>
                                    <span class="badge <?php echo $akun_login_status === 'Sudah ada' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'; ?>">
                                        <?php echo htmlspecialchars($akun_login_status); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4 action-area">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="/hr/karyawan-detail.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                                        <?php if ($akun_login_status === 'Belum dibuat'): ?>
                                            <form method="post" action="/hr/karyawan-provision.php" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Buat Akun Login</button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="/hr/karyawan-edit.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="post" action="/hr/karyawan-hapus.php" onsubmit="return confirm('Data karyawan akan dihapus permanen dan akun login yang terhubung ikut terhapus. Lanjutkan?');" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.querySelectorAll('.karyawan-row').forEach(function (row) {
    row.addEventListener('dblclick', function (event) {
        if (event.target.closest('.action-area') || event.target.closest('a') || event.target.closest('button') || event.target.closest('form') || event.target.closest('input')) {
            return;
        }

        var editUrl = row.getAttribute('data-edit-url');
        if (editUrl) {
            window.location.href = editUrl;
        }
    });
});
</script>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>
