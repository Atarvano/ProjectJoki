<?php
$flash_type = 'info';
$flash_message = '';
$flash_credentials = null;
$has_structured_credentials = false;
$credential_username = '';
$credential_password = '';
$credential_pattern = '';

if ($flash) {
    $flash_type = (string) ($flash['type'] ?? 'info');
    $flash_message = trim((string) ($flash['message'] ?? ''));
    $flash_credentials = $flash['credentials'] ?? null;
    $has_structured_credentials = is_array($flash_credentials)
        && trim((string) ($flash_credentials['username'] ?? '')) !== ''
        && trim((string) ($flash_credentials['password_awal'] ?? '')) !== '';

    $credential_username = trim((string) ($flash_credentials['username'] ?? ''));
    $credential_password = trim((string) ($flash_credentials['password_awal'] ?? ''));
    $credential_pattern = trim((string) ($flash_credentials['pattern_example'] ?? ''));

    if ($credential_pattern === '' && $credential_username !== '') {
        $credential_pattern = $credential_username . 'DDMMYYYY';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-1 text-gray-800">Employees</h2>
        <p class="text-muted mb-0">Halaman ini adalah pusat CRUD karyawan. Mulai dari daftar ini, lalu buka detail karyawan untuk review data, hak cuti, edit, hapus, atau buat akun login.</p>
    </div>
    <a href="/hr/employee-create.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Karyawan Baru
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?> alert-dismissible fade show mb-4" role="alert">
        <?php if ($has_structured_credentials): ?>
            <p class="mb-2 fw-semibold">Akun login karyawan berhasil dibuat.</p>
            <ul class="list-unstyled mb-2">
                <li><strong>NIK (Username):</strong> <?php echo htmlspecialchars($credential_username); ?></li>
                <li><strong>Password awal:</strong> <?php echo htmlspecialchars($credential_password); ?></li>
            </ul>
            <p class="mb-2">
                <small>
                    Pola password awal: NIK + tanggal lahir (DDMMYYYY)
                    <?php if ($credential_pattern !== ''): ?>
                        (contoh: <?php echo htmlspecialchars($credential_pattern); ?>)
                    <?php endif; ?>
                </small>
            </p>
            <p class="mb-0 fw-semibold">Kredensial ini hanya ditampilkan sekali. Segera catat dan sampaikan ke karyawan.</p>
        <?php elseif ($flash_message !== ''): ?>
            <?php echo nl2br(htmlspecialchars($flash_message), false); ?>
        <?php else: ?>
            Informasi diperbarui.
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($query_error !== null): ?>
    <div class="alert alert-danger mb-4" role="alert">
        <?php echo htmlspecialchars($query_error); ?>
    </div>
<?php endif; ?>

<?php if ($query_error === null && count($employee_list) === 0): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <div class="mb-3">
                <i class="bi bi-people" style="font-size: 3.5rem; opacity: 0.45;"></i>
            </div>
            <h3 class="h5 mb-2">Belum ada data karyawan</h3>
            <p class="text-muted mb-4">Silakan tambah data karyawan dulu supaya bisa dikelola dari halaman ini.</p>
            <a href="/hr/employee-create.php" class="btn btn-primary px-4">
                <i class="bi bi-plus-circle me-2"></i>Tambah Karyawan
            </a>
        </div>
    </div>
<?php endif; ?>

<?php if ($query_error === null && count($employee_list) > 0): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom bg-light">
            <p class="mb-1 fw-semibold">Alur yang disarankan:</p>
            <p class="mb-0 text-muted">Pilih <strong>Detail</strong> untuk review satu karyawan lebih lengkap. Dari halaman detail, HR bisa lanjut edit, hapus, atau buat akun login bila masih diperlukan.</p>
        </div>
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
                        <?php foreach ($employee_list as $row): ?>
                            <?php
                            $id = (int) $row['id'];
                            $tanggal_bergabung = '-';
                            $akun_login_status = !empty($row['user_id']) ? 'Sudah ada' : 'Belum dibuat';

                            if (!empty($row['tanggal_bergabung'])) {
                                $tanggal_bergabung = date('d M Y', strtotime($row['tanggal_bergabung']));
                            }
                            ?>
                            <tr>
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
                                        <a href="/hr/employee-detail.php?id=<?php echo $id; ?>" class="btn btn-sm btn-secondary">Detail</a>
                                        <a href="/hr/employee-edit.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <?php if ($akun_login_status === 'Belum dibuat'): ?>
                                            <form method="post" action="/hr/employee-provision.php" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Buat Akun Login</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" action="/hr/employee-delete.php" onsubmit="return confirm('Data karyawan akan dihapus permanen dan akun login yang terhubung ikut terhapus. Lanjutkan?');" class="d-inline">
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
