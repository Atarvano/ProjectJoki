<?php
require_once __DIR__ . '/../includes/auth/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/cuti-calculator.php';

$daftar_karyawan = [];
$selected_karyawan_id = isset($_GET['karyawan_id']) ? (int) $_GET['karyawan_id'] : 0;
$selected_karyawan = null;
$hasil = null;
$error = null;

$query_daftar = 'SELECT id, nama, nik, jabatan, tanggal_bergabung FROM karyawan ORDER BY nik ASC, nama ASC';
$result_daftar = mysqli_query($koneksi, $query_daftar);

if ($result_daftar) {
    while ($row = mysqli_fetch_assoc($result_daftar)) {
        $daftar_karyawan[] = $row;
    }
}

if ($selected_karyawan_id > 0) {
    $query_karyawan = 'SELECT id, nama, nik, jabatan, tanggal_bergabung, departemen FROM karyawan WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($koneksi, $query_karyawan);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $selected_karyawan_id);
        mysqli_stmt_execute($stmt);
        $result_karyawan = mysqli_stmt_get_result($stmt);

        if ($result_karyawan) {
            $selected_karyawan = mysqli_fetch_assoc($result_karyawan);
        }

        mysqli_stmt_close($stmt);
    }

    if (!$selected_karyawan) {
        $error = 'Karyawan yang dipilih tidak ditemukan. Silakan pilih data lain dari daftar.';
    } else {
        $tanggal_bergabung = trim((string) ($selected_karyawan['tanggal_bergabung'] ?? ''));

        if ($tanggal_bergabung === '') {
            $error = 'Tanggal bergabung karyawan belum tersedia, jadi hak cuti belum bisa dihitung.';
        } elseif (strtotime($tanggal_bergabung) === false) {
            $error = 'Tanggal bergabung karyawan tidak valid, jadi hak cuti belum bisa dihitung.';
        } else {
            $tahun_bergabung = (int) date('Y', strtotime($tanggal_bergabung));
            $validasi = validasiTahunBergabung($tahun_bergabung);

            if ($validasi['valid']) {
                $hasil = hitungHakCuti($tahun_bergabung);
            } else {
                $error = $validasi['pesan'];
            }
        }
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
    'active_nav' => 'kalkulator',
    'page_title' => 'Kalkulator Hak Cuti - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Kalkulator Hak Cuti', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-1 text-gray-800">Kalkulator Hak Cuti</h2>
        <p class="text-muted mb-0">Pilih karyawan untuk melihat hak cuti berdasarkan tanggal bergabung yang tersimpan di database.</p>
    </div>
    <a href="/hr/employees.php" class="btn btn-outline-secondary">
        <i class="bi bi-people me-2"></i>Kelola Karyawan
    </a>
</div>

<?php if (empty($daftar_karyawan)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5 text-center">
            <div class="mb-3">
                <i class="bi bi-person-x text-secondary opacity-50" style="font-size: 4rem;"></i>
            </div>
            <h3 class="h4 mb-3">Belum ada data karyawan</h3>
            <p class="text-muted mb-4">Tambahkan data karyawan terlebih dahulu agar HR bisa membuka hak cuti per karyawan.</p>
            <a href="/hr/employees.php" class="btn btn-primary">
                <i class="bi bi-arrow-right-circle me-2"></i>Buka Kelola Karyawan
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h3 class="h5 mb-3">Pilih Karyawan</h3>
                    <form method="get" action="" class="mb-4">
                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label fw-medium">Nama / NIK Karyawan</label>
                            <select name="karyawan_id" id="karyawan_id" class="form-select form-select-lg" required>
                                <option value="">Pilih karyawan</option>
                                <?php foreach ($daftar_karyawan as $karyawan_option): ?>
                                    <option value="<?php echo (int) $karyawan_option['id']; ?>" <?php echo $selected_karyawan_id === (int) $karyawan_option['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($karyawan_option['nik'] . ' - ' . $karyawan_option['nama']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Lihat Hak Cuti
                        </button>
                    </form>

                    <div class="rounded bg-light border p-3">
                        <p class="small text-muted mb-0">
                            Halaman ini memakai tanggal bergabung karyawan yang sudah tersimpan. HR tidak perlu mengetik tahun secara manual lagi.
                        </p>
                    </div>

                    <?php if ($selected_karyawan): ?>
                        <hr>
                        <div>
                            <h4 class="h6 mb-3">Data Karyawan</h4>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-muted">Nama</dt>
                                <dd class="col-7 mb-2"><?php echo htmlspecialchars($selected_karyawan['nama']); ?></dd>

                                <dt class="col-5 text-muted">NIK</dt>
                                <dd class="col-7 mb-2"><?php echo htmlspecialchars($selected_karyawan['nik']); ?></dd>

                                <dt class="col-5 text-muted">Jabatan</dt>
                                <dd class="col-7 mb-2"><?php echo htmlspecialchars($selected_karyawan['jabatan'] ?: '-'); ?></dd>

                                <dt class="col-5 text-muted">Departemen</dt>
                                <dd class="col-7 mb-2"><?php echo htmlspecialchars($selected_karyawan['departemen'] ?: '-'); ?></dd>

                                <dt class="col-5 text-muted">Tgl Bergabung</dt>
                                <dd class="col-7 mb-0">
                                    <?php
                                    $tanggal_bergabung_label = trim((string) ($selected_karyawan['tanggal_bergabung'] ?? ''));
                                    if ($tanggal_bergabung_label !== '' && strtotime($tanggal_bergabung_label) !== false) {
                                        $tanggal_bergabung_label = date('d M Y', strtotime($tanggal_bergabung_label));
                                    } elseif ($tanggal_bergabung_label === '') {
                                        $tanggal_bergabung_label = '-';
                                    }
                                    echo htmlspecialchars($tanggal_bergabung_label);
                                    ?>
                                </dd>
                            </dl>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if ($error): ?>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-circle text-danger opacity-75" style="font-size: 3.5rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Hak cuti belum bisa ditampilkan</h3>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            <?php elseif ($hasil && $selected_karyawan): ?>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded">
                                    <i class="bi bi-calendar2-check fs-4"></i>
                                </div>
                                <div>
                                    <h3 class="h5 mb-1">Hak Cuti <?php echo htmlspecialchars($selected_karyawan['nama']); ?></h3>
                                    <div class="text-muted small">
                                        NIK <strong class="text-dark"><?php echo htmlspecialchars($selected_karyawan['nik']); ?></strong>
                                        · Tahun bergabung <strong class="text-dark"><?php echo htmlspecialchars((string) $hasil['tahun_bergabung']); ?></strong>
                                    </div>
                                </div>
                            </div>
                            <a href="/hr/employee-detail.php?id=<?php echo (int) $selected_karyawan['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-person-lines-fill me-2"></i>Kembali ke Detail Karyawan
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="px-4 pt-4">
                            <div class="alert alert-light border mb-4">
                                Perhitungan ini memakai tanggal bergabung dari database dan engine cuti yang sama seperti sebelumnya.
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center py-3" style="width: 90px;">Tahun</th>
                                        <th class="py-3">Tahun Kalender</th>
                                        <th class="text-center py-3">Jumlah Hari</th>
                                        <th class="text-center py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hasil['data'] as $row): ?>
                                        <tr class="<?php echo (int) $row['tahun_kalender'] === (int) date('Y') ? 'table-primary bg-opacity-10' : ''; ?>">
                                            <td class="text-center text-muted fw-medium">Ke-<?php echo (int) $row['tahun_ke']; ?></td>
                                            <td class="fw-bold"><?php echo (int) $row['tahun_kalender']; ?></td>
                                            <td class="text-center"><span class="fs-5 text-primary fw-bold"><?php echo (int) $row['hari_cuti']; ?></span></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill <?php echo htmlspecialchars($row['status_class']); ?> px-3 py-2">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-3">
                            <i class="bi bi-arrow-left-right text-primary opacity-50" style="font-size: 3.5rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Pilih satu karyawan</h3>
                        <p class="text-muted mb-0">Setelah karyawan dipilih, tabel hak cuti akan langsung tampil berdasarkan data tanggal bergabung di database.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/layout/dashboard-layout.php';
?>
