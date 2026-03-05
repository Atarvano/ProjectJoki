<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
}

if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Silakan pilih data dari daftar karyawan.',
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

$errors = [];
$summary_error = '';

$old = [
    'nama' => '',
    'nik' => '',
    'jabatan' => '',
    'tanggal_bergabung' => '',
    'tanggal_lahir' => '',
    'email' => '',
    'telepon' => '',
    'alamat' => '',
    'departemen' => '',
];

$load_sql = 'SELECT id, nama, nik, jabatan, tanggal_bergabung, tanggal_lahir, email, telepon, alamat, departemen FROM karyawan WHERE id = ? LIMIT 1';
$load_stmt = mysqli_prepare($koneksi, $load_sql);

if (!$load_stmt) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Halaman edit belum bisa dibuka sekarang. Silakan coba lagi.',
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

mysqli_stmt_bind_param($load_stmt, 'i', $id);
mysqli_stmt_execute($load_stmt);
$load_result = mysqli_stmt_get_result($load_stmt);
$existing = $load_result ? mysqli_fetch_assoc($load_result) : null;
mysqli_stmt_close($load_stmt);

if (!$existing) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Mungkin sudah dihapus sebelumnya.',
    ];
    header('Location: /hr/karyawan.php');
    exit;
}

foreach ($old as $field => $value) {
    $old[$field] = (string) ($existing[$field] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $field => $value) {
        $old[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if ($old['nama'] === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    }

    if ($old['nik'] === '') {
        $errors['nik'] = 'NIK wajib diisi.';
    }

    if ($old['tanggal_bergabung'] === '') {
        $errors['tanggal_bergabung'] = 'Tanggal bergabung wajib diisi.';
    }

    if ($old['tanggal_lahir'] === '') {
        $errors['tanggal_lahir'] = 'Tanggal lahir wajib diisi.';
    }

    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email belum benar. Contoh: nama@perusahaan.com';
    }

    if ($old['nik'] !== '') {
        $check_nik_sql = 'SELECT id FROM karyawan WHERE nik = ? AND id != ? LIMIT 1';
        $check_nik_stmt = mysqli_prepare($koneksi, $check_nik_sql);

        if ($check_nik_stmt) {
            mysqli_stmt_bind_param($check_nik_stmt, 'si', $old['nik'], $id);
            mysqli_stmt_execute($check_nik_stmt);
            $check_nik_result = mysqli_stmt_get_result($check_nik_stmt);

            if ($check_nik_result && mysqli_num_rows($check_nik_result) > 0) {
                $errors['nik'] = 'NIK sudah dipakai karyawan lain. Silakan gunakan NIK yang berbeda.';
            }

            mysqli_stmt_close($check_nik_stmt);
        } else {
            $summary_error = 'Validasi NIK sedang bermasalah. Silakan coba lagi.';
        }
    }

    if (empty($errors) && $summary_error === '') {
        $update_sql = 'UPDATE karyawan SET nama = ?, nik = ?, jabatan = ?, tanggal_bergabung = ?, tanggal_lahir = ?, email = ?, telepon = ?, alamat = ?, departemen = ? WHERE id = ?';
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            $jabatan_value = $old['jabatan'] !== '' ? $old['jabatan'] : null;
            $email_value = $old['email'] !== '' ? $old['email'] : null;
            $telepon_value = $old['telepon'] !== '' ? $old['telepon'] : null;
            $alamat_value = $old['alamat'] !== '' ? $old['alamat'] : null;
            $departemen_value = $old['departemen'] !== '' ? $old['departemen'] : null;

            mysqli_stmt_bind_param(
                $update_stmt,
                'sssssssssi',
                $old['nama'],
                $old['nik'],
                $jabatan_value,
                $old['tanggal_bergabung'],
                $old['tanggal_lahir'],
                $email_value,
                $telepon_value,
                $alamat_value,
                $departemen_value,
                $id
            );

            $update_ok = mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);

            if ($update_ok) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Data karyawan berhasil diperbarui. Silakan cek kembali pada daftar karyawan.',
                ];
                header('Location: /hr/karyawan.php');
                exit;
            }

            $summary_error = 'Perubahan belum berhasil disimpan. Silakan cek lagi lalu coba ulang.';
        } else {
            $summary_error = 'Form edit belum bisa diproses sekarang. Silakan coba lagi sebentar.';
        }
    }

    if (!empty($errors) && $summary_error === '') {
        $summary_error = 'Mohon periksa kembali data yang ditandai agar perubahan bisa disimpan.';
    }
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
    'page_title' => 'Edit Karyawan - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Kelola Karyawan', 'url' => '/hr/karyawan.php'],
        ['label' => 'Edit Karyawan', 'url' => '#'],
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="h3 mb-0 text-gray-800">Edit Karyawan</h2>
    <a href="/hr/karyawan.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
    </a>
</div>

<?php if ($summary_error !== ''): ?>
    <div class="alert alert-danger mb-4" role="alert">
        <strong>Perubahan belum bisa disimpan.</strong><br>
        <?php echo htmlspecialchars($summary_error); ?>
        <?php if (!empty($errors)): ?>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error_message): ?>
                    <li><?php echo htmlspecialchars($error_message); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="post" action="/hr/karyawan-edit.php?id=<?php echo $id; ?>" novalidate>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['nama']) ? 'is-invalid' : ''; ?>" id="nama" name="nama" value="<?php echo htmlspecialchars($old['nama']); ?>">
                    <?php if (isset($errors['nama'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['nama']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['nik']) ? 'is-invalid' : ''; ?>" id="nik" name="nik" value="<?php echo htmlspecialchars($old['nik']); ?>">
                    <?php if (isset($errors['nik'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['nik']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($old['jabatan']); ?>">
                </div>

                <div class="col-md-6">
                    <label for="departemen" class="form-label">Departemen</label>
                    <input type="text" class="form-control" id="departemen" name="departemen" value="<?php echo htmlspecialchars($old['departemen']); ?>">
                </div>

                <div class="col-md-6">
                    <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?php echo isset($errors['tanggal_bergabung']) ? 'is-invalid' : ''; ?>" id="tanggal_bergabung" name="tanggal_bergabung" value="<?php echo htmlspecialchars($old['tanggal_bergabung']); ?>">
                    <?php if (isset($errors['tanggal_bergabung'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['tanggal_bergabung']); ?></div>
                    <?php endif; ?>
                    <div class="form-text">Isi dengan format tanggal: YYYY-MM-DD.</div>
                </div>

                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?php echo isset($errors['tanggal_lahir']) ? 'is-invalid' : ''; ?>" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($old['tanggal_lahir']); ?>">
                    <?php if (isset($errors['tanggal_lahir'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['tanggal_lahir']); ?></div>
                    <?php endif; ?>
                    <div class="form-text">Isi dengan format tanggal: YYYY-MM-DD.</div>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($old['email']); ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                    <div class="form-text">Contoh: nama@perusahaan.com. Boleh dikosongkan jika belum ada.</div>
                </div>

                <div class="col-md-6">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($old['telepon']); ?>">
                    <div class="form-text">Isi nomor aktif (contoh: 08123456789). Boleh dikosongkan.</div>
                </div>

                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($old['alamat']); ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
                <a href="/hr/karyawan.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>
