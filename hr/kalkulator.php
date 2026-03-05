<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../includes/cuti-calculator.php';

$hasil = null;
$error = null;
$tahun_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_report') {
    // PRG pattern for saving reports
    $tahun_input = trim($_POST['tahun_bergabung']);
    $nama = trim($_POST['nama_karyawan'] ?? '');
    $simpan = isset($_POST['simpan_laporan']);

    $validasi = validasiTahunBergabung($tahun_input);

    if ($validasi['valid']) {
        if ($simpan) {
            require_once __DIR__ . '/../includes/reports-data.php';
            if (strlen($nama) < 3) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Nama karyawan minimal 3 karakter.'];
            } else {
                $result = saveReport($nama, (int) $tahun_input);
                if ($result === 'created') {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => "Laporan disimpan untuk $nama ($tahun_input)."];
                } else {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => "Laporan diperbarui untuk $nama ($tahun_input)."];
                }
            }
        }
        header('Location: kalkulator.php?tahun_bergabung=' . urlencode($tahun_input));
        exit;
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $validasi['pesan']];
        header('Location: kalkulator.php');
        exit;
    }
}

if (isset($_GET['tahun_bergabung'])) {
    $tahun_input = trim($_GET['tahun_bergabung']);
    $validasi = validasiTahunBergabung($tahun_input);
    if ($validasi['valid']) {
        $hasil = hitungHakCuti((int) $tahun_input);
    } else {
        $error = $validasi['pesan'];
    }
}

$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$dashboard_context = [
    'role' => 'hr',
    'active_nav' => 'kalkulator',
    'page_title' => 'Kalkulator Hak Cuti - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => 'dashboard.php'],
        ['label' => 'Kalkulator Hak Cuti', 'url' => '#']
    ],
    'profile_label' => 'Admin HR',
    'profile_initials' => 'HR',
];

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Kalkulator Hak Cuti</h2>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show mb-4">
        <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($hasil === null): ?>
    <!-- State A: Empty State or Error State -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <i class="bi bi-calculator text-primary opacity-50" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="mb-3">Hitung Hak Cuti Karyawan</h3>
                    <p class="text-muted mb-4 lead">
                        Masukkan tahun karyawan bergabung untuk melihat tabel simulasi hak cuti selama 8 tahun ke depan. Anda juga dapat langsung menyimpan hasilnya ke daftar laporan.
                    </p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger d-inline-block mb-4 text-start">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="calculator-form mx-auto text-start" style="max-width: 500px;">
                        <input type="hidden" name="action" value="save_report">
                        
                        <div class="mb-3">
                            <label for="tahun_bergabung" class="form-label fw-medium">Tahun Bergabung</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar"></i></span>
                                <input type="number" id="tahun_bergabung" name="tahun_bergabung" class="form-control border-start-0"
                                    placeholder="Contoh: 2020" min="1990" max="<?php echo date('Y'); ?>"
                                    value="<?php echo htmlspecialchars($tahun_input); ?>" required>
                                <button type="submit" class="btn btn-primary px-4">
                                    Hitung
                                </button>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mt-4">
                            <div class="card-body">
                                <div class="form-check mb-0">
                                    <input class="form-check-input simpan-checkbox fs-5" type="checkbox" id="simpan_laporan_empty"
                                        name="simpan_laporan" value="1">
                                    <label class="form-check-label pt-1" for="simpan_laporan_empty">
                                        <i class="bi bi-bookmark-plus me-1"></i>Simpan hasil sebagai laporan
                                    </label>
                                </div>
                                <div class="save-name-field mt-3" style="display: none;">
                                    <label for="nama_karyawan" class="form-label small text-muted">Nama Karyawan</label>
                                    <input type="text" id="nama_karyawan" name="nama_karyawan" class="form-control"
                                        placeholder="Masukkan nama lengkap" minlength="3">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- State C: Result State -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h4 class="mb-4">Pengaturan Perhitungan</h4>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="save_report">
                        <div class="mb-4">
                            <label for="tahun_bergabung_result" class="form-label fw-medium">Tahun Bergabung</label>
                            <input type="number" id="tahun_bergabung_result" name="tahun_bergabung" class="form-control form-control-lg bg-light"
                                placeholder="Contoh: 2020" min="1990" max="<?php echo date('Y'); ?>"
                                value="<?php echo htmlspecialchars($tahun_input); ?>" required>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body py-3">
                                <div class="form-check">
                                    <input class="form-check-input simpan-checkbox" type="checkbox" id="simpan_laporan_result"
                                        name="simpan_laporan" value="1">
                                    <label class="form-check-label" for="simpan_laporan_result">
                                        <i class="bi bi-bookmark-plus me-1 text-primary"></i>Simpan ke laporan
                                    </label>
                                </div>
                                <div class="save-name-field mt-3" style="display: none;">
                                    <input type="text" name="nama_karyawan" class="form-control"
                                        placeholder="Nama karyawan (min. 3 karakter)" minlength="3">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Hitung Ulang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded">
                                <i class="bi bi-calendar2-check fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Tabel Hak Cuti Karyawan</h5>
                                <div class="text-muted small">Tahun bergabung: <strong class="text-dark"><?php echo htmlspecialchars($hasil['tahun_bergabung']); ?></strong></div>
                            </div>
                        </div>
                        <div class="text-end d-none d-sm-block">
                            <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                                Periode: <?php echo $hasil['tahun_mulai']; ?> - <?php echo $hasil['tahun_selesai']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table entitlement-table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center py-3" style="width: 80px;">Tahun</th>
                                    <th class="py-3">Tahun Kalender</th>
                                    <th class="text-center py-3">Jumlah Hari</th>
                                    <th class="text-center py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil['data'] as $row): ?>
                                    <tr class="<?php echo ($row['tahun_kalender'] == date('Y')) ? 'table-primary bg-opacity-10' : ''; ?>">
                                        <td class="text-center text-muted fw-medium">Ke-<?php echo $row['tahun_ke']; ?></td>
                                        <td class="fw-bold"><?php echo $row['tahun_kalender']; ?></td>
                                        <td class="text-center">
                                            <span class="fs-5 text-primary fw-bold"><?php echo $row['hari_cuti']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?php echo $row['status_class']; ?> px-3 py-2">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.querySelectorAll('.simpan-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var nameField = this.closest('form').querySelector('.save-name-field');
            if (this.checked) {
                nameField.style.display = 'block';
                // Use a short timeout to allow the display:block to render before focusing
                setTimeout(function() {
                    var input = nameField.querySelector('input');
                    if (input) input.focus();
                }, 10);
            } else {
                nameField.style.display = 'none';
            }
        });
    });
</script>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>
