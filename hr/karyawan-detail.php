<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Silakan pilih data dari daftar karyawan.'
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

$karyawan = null;
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
        $karyawan = mysqli_fetch_assoc($result);
    }

    mysqli_stmt_close($stmt);
}

if (!$karyawan) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan atau sudah dihapus.'
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

$akun_login_status = !empty($karyawan['user_id']) ? 'Sudah ada' : 'Belum dibuat';

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
    'page_title' => 'Detail Karyawan - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Kelola Karyawan', 'url' => '/hr/karyawan.php'],
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
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="h3 mb-0 text-gray-800">Detail Karyawan</h2>
    <a href="/hr/karyawan.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <p class="mb-1"><strong>Akun login:</strong> <?php echo htmlspecialchars($akun_login_status); ?></p>
                <p class="text-muted mb-0">Jika data karyawan ini dihapus permanen, akun login yang terhubung juga akan ikut terhapus otomatis.</p>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="/hr/kalkulator.php?karyawan_id=<?php echo (int) $karyawan['id']; ?>" class="btn btn-outline-primary">
                    <i class="bi bi-calculator me-2"></i>Lihat Hak Cuti
                </a>
                <?php if ($akun_login_status === 'Belum dibuat'): ?>
                    <form method="post" action="/hr/karyawan-provision.php" class="m-0">
                        <input type="hidden" name="id" value="<?php echo (int) $karyawan['id']; ?>">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-person-plus me-2"></i>Buat Akun Login
                        </button>
                    </form>
                <?php else: ?>
                    <span class="badge bg-success-subtle text-success-emphasis fs-6">Akun login sudah dibuat</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <tbody>
                    <?php foreach ($fields as $field_key => $field_label): ?>
                        <?php
                        $value = trim((string) ($karyawan[$field_key] ?? ''));
                        if ($value === '') {
                            $value = '-';
                        }

                        if (($field_key === 'tanggal_bergabung' || $field_key === 'tanggal_lahir') && $value !== '-') {
                            $timestamp = strtotime($value);
                            if ($timestamp !== false) {
                                $value = date('d M Y', $timestamp);
                            }
                        }
                        ?>
                        <tr>
                            <th class="w-25 bg-light px-4 py-3"><?php echo htmlspecialchars($field_label); ?></th>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>
