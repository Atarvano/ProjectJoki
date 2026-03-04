<?php
require_once __DIR__ . '/../includes/cuti-calculator.php';

// Setup dashboard context
$page_class = 'page-dashboard role-employee';
$dashboard_context = [
    'role' => 'employee',
    'active_nav' => 'hak-cuti',
    'page_title' => 'Hak Cuti Saya',
    'breadcrumb' => [
        ['label' => 'Dashboard'],
        ['label' => 'Hak Cuti Saya'],
    ],
    'profile_label' => 'Karyawan',
    'profile_initials' => 'KR',
    'demo_badge' => 'Demo v1',
];

// Preset demo employees
$preset_karyawan = [
    ['nama' => 'Budi Santoso',    'tahun_bergabung' => 2018],
    ['nama' => 'Siti Rahayu',     'tahun_bergabung' => 2020],
    ['nama' => 'Ahmad Fauzi',     'tahun_bergabung' => 2022],
    ['nama' => 'Dewi Lestari',    'tahun_bergabung' => 2024],
    ['nama' => 'Riko Pratama',    'tahun_bergabung' => (int)date('Y')],
];

// Handle form input
$hasil = null;
$error = null;
$selected_name = '';
$tahun_input = '';
$is_custom = false;

if (isset($_GET['preset']) && $_GET['preset'] !== '') {
    if ($_GET['preset'] === 'custom' && isset($_GET['tahun_bergabung'])) {
        $tahun_input = trim($_GET['tahun_bergabung']);
        $selected_name = 'Kustom';
        $is_custom = true;
    } elseif (is_numeric($_GET['preset'])) {
        $tahun_input = trim($_GET['preset']);
        // Find name from preset
        foreach ($preset_karyawan as $preset) {
            if ($preset['tahun_bergabung'] == $tahun_input) {
                $selected_name = $preset['nama'];
                break;
            }
        }
    }
    
    if ($tahun_input !== '') {
        $validasi = validasiTahunBergabung($tahun_input);
        if ($validasi['valid']) {
            $hasil = hitungHakCuti((int)$tahun_input);
        } else {
            $error = $validasi['pesan'];
        }
    }
}

// Compute summary values if result available
$total_sisa = 0;
$status_sekarang = 'Rencana';
$status_class_sekarang = 'bg-secondary';
$tahun_sekarang = (int)date('Y');

if ($hasil) {
    foreach ($hasil['data'] as $row) {
        if ($row['tahun_kalender'] >= $tahun_sekarang) {
            $total_sisa += $row['hari_cuti'];
        }
        if ($row['tahun_kalender'] == $tahun_sekarang) {
            $status_sekarang = $row['status'];
            $status_class_sekarang = $row['status_class'];
        }
    }
}
?>

<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="gradient-card border-0 mb-4 overflow-hidden" style="background-color: var(--color-surface);">
            <div class="card-body p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div>
                    <h1 class="hero-title mb-2 text-primary-dark" style="font-size: clamp(2rem, 4vw, 3rem); font-family: var(--font-display); font-weight: 800;">
                        Hak Cuti <span class="text-accent">Saya</span>
                    </h1>
                    <p class="hero-subtitle mb-0 text-secondary" style="font-size: 1.125rem; max-width: 500px;">
                        Periksa hak cuti tahunan Anda berdasarkan tanggal bergabung.
                    </p>
                </div>
                
                <!-- SVG Icon matching landing page style -->
                <div class="d-none d-md-block mt-4 mt-md-0" style="opacity: 0.9;">
                    <svg viewBox="0 0 120 100" width="120" height="100" aria-hidden="true">
                        <rect x="20" y="20" width="80" height="60" rx="6" fill="var(--color-primary-subtle)" />
                        <rect x="20" y="20" width="80" height="15" rx="4" fill="var(--color-primary)" />
                        <rect x="30" y="45" width="60" height="8" rx="2" fill="var(--color-surface)" />
                        <circle cx="40" cy="49" r="2" fill="var(--color-text-muted)" />
                        <polygon points="65,55 75,70 70,72 75,80 72,82 67,73 60,78" fill="var(--color-accent)" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-0">

    <?php if ($hasil === null && $error === null): ?>
        <!-- State 1: Empty state -->
        <div class="dashboard-form-card mb-5 text-center p-5">
            <h2 class="mb-3 text-primary-dark" style="font-family: var(--font-display); font-weight: 800; font-size: clamp(1.75rem, 3vw, 2.5rem);">Lihat Hak Cuti Anda</h2>
            <p class="text-secondary mb-4" style="max-width: 600px; margin: 0 auto; font-size: 1.125rem;">Silakan pilih profil Anda untuk melihat ringkasan hak cuti berdasarkan tahun bergabung. Anda juga dapat menggunakan tahun kustom untuk simulasi.</p>
            
            <form action="" method="GET" class="mt-4 mx-auto" style="max-width: 500px;">
                <div class="mb-4 text-start">
                    <label for="preset-select" class="form-label text-muted fw-bold small text-uppercase tracking-wider">Pilih Profil Demo</label>
                    <select name="preset" id="preset-select" class="form-select form-select-lg" style="border-radius: var(--radius-md);" required>
                        <option value="">-- Pilih Profil Karyawan --</option>
                        <?php foreach ($preset_karyawan as $preset): ?>
                            <option value="<?php echo $preset['tahun_bergabung']; ?>">
                                <?php echo htmlspecialchars($preset['nama'] . ' (Bergabung ' . $preset['tahun_bergabung'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="custom">Tahun Kustom...</option>
                    </select>
                </div>
                
                <div id="custom-year-container" class="mb-4 text-start" style="display: none;">
                    <label for="tahun_bergabung" class="form-label text-muted fw-bold small text-uppercase tracking-wider">Masukkan Tahun Bergabung:</label>
                    <input type="number" 
                           id="tahun_bergabung"
                           name="tahun_bergabung" 
                           class="form-control form-control-lg" 
                           style="border-radius: var(--radius-md);"
                           placeholder="Contoh: 2020" 
                           min="1990" 
                           max="<?php echo date('Y'); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg w-100" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light)); border: none; box-shadow: 0 4px 15px rgba(15, 76, 92, 0.3); font-family: var(--font-display); font-weight: 700;">
                    <i class="bi bi-eye me-2"></i>Lihat Hak Cuti
                </button>
            </form>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="dashboard-stat-card border-0 shadow-sm h-100 p-4 bg-white rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-calendar-event fs-5"></i>
                        </div>
                        <div class="text-muted fw-medium">Tahun Bergabung</div>
                    </div>
                    <div class="fs-2 fw-bold text-dark">--</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-stat-card border-0 shadow-sm h-100 p-4 bg-white rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-activity fs-5"></i>
                        </div>
                        <div class="text-muted fw-medium">Status Hak Cuti</div>
                    </div>
                    <div class="fs-2 fw-bold text-dark">--</div>
                </div>
            </div>
        </div>
        
    <?php elseif ($error !== null): ?>
        <!-- State 2: Error state -->
        <div class="dashboard-form-card mb-5 text-center">
            <h2 class="mb-3 text-primary" style="font-family: var(--font-display); font-weight: 700;">Lihat Hak Cuti Anda</h2>
            <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">Silakan pilih profil Anda untuk melihat ringkasan hak cuti berdasarkan tahun bergabung. Anda juga dapat menggunakan tahun kustom untuk simulasi.</p>
            
            <div class="alert alert-danger d-inline-block mt-2 mb-4 text-start">
                <i class="bi bi-exclamation-circle me-2"></i>
                Mohon periksa kembali tahun yang dimasukkan. <?php echo htmlspecialchars($error); ?>
            </div>
            
            <form action="" method="GET" class="mx-auto" style="max-width: 500px;">
                <div class="mb-3">
                    <select name="preset" id="preset-select" class="form-select form-select-lg" required>
                        <option value="">-- Pilih Profil Karyawan --</option>
                        <?php foreach ($preset_karyawan as $preset): ?>
                            <option value="<?php echo $preset['tahun_bergabung']; ?>" <?php echo (isset($_GET['preset']) && $_GET['preset'] == $preset['tahun_bergabung']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($preset['nama'] . ' (Bergabung ' . $preset['tahun_bergabung'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="custom" <?php echo (isset($_GET['preset']) && $_GET['preset'] === 'custom') ? 'selected' : ''; ?>>Tahun Kustom...</option>
                    </select>
                </div>
                
                <div id="custom-year-container" class="mb-3" style="<?php echo (isset($_GET['preset']) && $_GET['preset'] === 'custom') ? 'display: block;' : 'display: none;'; ?>">
                    <label for="tahun_bergabung" class="form-label">Masukkan Tahun Bergabung:</label>
                    <input type="number" 
                           id="tahun_bergabung"
                           name="tahun_bergabung" 
                           class="form-control form-control-lg" 
                           placeholder="Contoh: 2020" 
                           min="1990" 
                           max="<?php echo date('Y'); ?>"
                           value="<?php echo htmlspecialchars($tahun_input); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-eye me-2"></i>Lihat Hak Cuti
                </button>
            </form>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="dashboard-stat-card border-0 shadow-sm h-100 p-4 bg-white rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-calendar-event fs-5"></i>
                        </div>
                        <div class="text-muted fw-medium">Tahun Bergabung</div>
                    </div>
                    <div class="fs-2 fw-bold text-dark">--</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-stat-card border-0 shadow-sm h-100 p-4 bg-white rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-activity fs-5"></i>
                        </div>
                        <div class="text-muted fw-medium">Status Hak Cuti</div>
                    </div>
                    <div class="fs-2 fw-bold text-dark">--</div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- State 3: Result state -->
        <div class="dashboard-stat-card mb-4" style="background-color: var(--color-primary-subtle); border-color: var(--color-info);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-primary-dark fw-bold" style="font-family: var(--font-display);"><?php echo htmlspecialchars($selected_name); ?></h4>
                        <div class="text-secondary">Bergabung: <strong><?php echo $hasil['tahun_bergabung']; ?></strong></div>
                    </div>
                </div>
                <div class="d-flex gap-4 align-items-center">
                    <div class="text-end">
                        <div class="small text-secondary fw-semibold mb-1 text-uppercase">Status Tahun Ini</div>
                        <span class="badge rounded-pill <?php echo $status_class_sekarang; ?> fs-6"><?php echo $status_sekarang; ?></span>
                    </div>
                    <div class="border-start border-secondary border-opacity-25 ps-4 text-end">
                        <div class="small text-secondary fw-semibold mb-1 text-uppercase">Sisa Cuti (8 thn)</div>
                        <h3 class="mb-0 text-primary fw-bold" style="font-family: var(--font-display);"><?php echo $total_sisa; ?> <small class="text-secondary fs-6 fw-normal">hari</small></h3>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($hasil['tahun_bergabung'] == $tahun_sekarang): ?>
            <!-- Anticipation State -->
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                Selamat bergabung! Hak cuti Anda mulai berlaku tahun <strong><?php echo $hasil['tahun_mulai']; ?></strong>. Berikut rencana hak cuti Anda selama 8 tahun ke depan.
            </div>
        <?php endif; ?>

        <div class="dashboard-table-shell mb-4">
            <div class="dashboard-table-header">
                <h5 class="mb-0 text-primary-dark fw-bold" style="font-family: var(--font-display);">Rincian Hak Cuti 8 Tahun</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">No.</th>
                            <th>Tahun Kalender</th>
                            <th class="text-center">Hari Cuti</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasil['data'] as $row): ?>
                            <tr>
                                <td class="text-center text-muted"><?php echo $row['tahun_ke']; ?></td>
                                <td class="fw-bold"><?php echo $row['tahun_kalender']; ?></td>
                                <td class="text-center">
                                    <span class="fs-5 text-primary fw-bold"><?php echo $row['hari_cuti']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo $row['status_class']; ?> fw-semibold px-3 py-2">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-center border-top bg-light">
                <p class="employee-support-hint text-muted small mb-0 fw-medium">
                    <i class="bi bi-question-circle-fill me-1 text-primary"></i>Butuh klarifikasi? Hubungi tim HR untuk informasi lebih lanjut.
                </p>
            </div>
        </div>
        
        <div class="dashboard-form-card border-0 bg-white">
            <div class="card-body">
                <form action="" method="GET" class="d-flex flex-column flex-md-row gap-3 align-items-md-end">
                    <div class="flex-grow-1">
                        <label for="preset-select" class="form-label text-muted small">Pilih profil lain:</label>
                        <select name="preset" id="preset-select" class="form-select" required>
                            <option value="">-- Pilih Profil Karyawan --</option>
                            <?php foreach ($preset_karyawan as $preset): ?>
                                <option value="<?php echo $preset['tahun_bergabung']; ?>" <?php echo (!$is_custom && isset($_GET['preset']) && $_GET['preset'] == $preset['tahun_bergabung']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($preset['nama'] . ' (Bergabung ' . $preset['tahun_bergabung'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="custom" <?php echo ($is_custom) ? 'selected' : ''; ?>>Tahun Kustom...</option>
                        </select>
                    </div>
                    
                    <div id="custom-year-container" class="flex-grow-1" style="<?php echo ($is_custom) ? 'display: block;' : 'display: none;'; ?>">
                        <label for="tahun_bergabung" class="form-label text-muted small">Tahun Bergabung:</label>
                        <input type="number" 
                               id="tahun_bergabung"
                               name="tahun_bergabung" 
                               class="form-control" 
                               placeholder="Contoh: 2020" 
                               min="1990" 
                               max="<?php echo date('Y'); ?>"
                               value="<?php echo htmlspecialchars($tahun_input); ?>">
                    </div>
                    
                    <div>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Lihat
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const presetSelects = document.querySelectorAll('#preset-select');
    
    presetSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Find the closest form to scope the container lookup
            const form = this.closest('form');
            const customContainer = form.querySelector('#custom-year-container');
            const customInput = form.querySelector('#tahun_bergabung');
            
            if (this.value === 'custom') {
                customContainer.style.display = 'block';
                customInput.required = true;
                // Don't clear it, they might have typed something before
                customInput.focus();
            } else {
                customContainer.style.display = 'none';
                customInput.required = false;
            }
        });
    });
});
</script>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';

