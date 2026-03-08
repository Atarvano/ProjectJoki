<?php
?>
<div class="row mb-4">
    <div class="col-12">
        <div class="gradient-card border-0 mb-4 overflow-hidden" style="background-color: var(--color-surface);">
            <div class="card-body p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div>
                    <h1 class="hero-title mb-2 text-primary-dark" style="font-size: clamp(2rem, 4vw, 3rem); font-family: var(--font-display); font-weight: 800;">
                        Hak Cuti <span class="text-accent">Saya</span>
                    </h1>
                    <p class="hero-subtitle mb-0 text-secondary" style="font-size: 1.125rem; max-width: 550px;">
                        Ringkasan hak cuti Anda ditampilkan otomatis berdasarkan data kepegawaian akun yang sedang login.
                    </p>
                </div>

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
    <?php if ($load_error !== null): ?>
        <div class="alert alert-warning shadow-sm border-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($load_error); ?>
        </div>
    <?php else: ?>
        <div class="dashboard-stat-card mb-4" style="background-color: var(--color-primary-subtle); border-color: var(--color-info);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-primary-dark fw-bold" style="font-family: var(--font-display);"><?php echo htmlspecialchars($employee_name); ?></h4>
                        <div class="text-secondary">Bergabung: <strong><?php echo htmlspecialchars((string) $tahun_bergabung); ?></strong></div>
                    </div>
                </div>
                <div class="d-flex gap-4 align-items-center">
                    <div class="text-end">
                        <div class="small text-secondary fw-semibold mb-1 text-uppercase">Status Tahun Ini</div>
                        <span class="badge rounded-pill <?php echo htmlspecialchars($status_class_sekarang); ?> fs-6"><?php echo htmlspecialchars($status_sekarang); ?></span>
                    </div>
                    <div class="border-start border-secondary border-opacity-25 ps-4 text-end">
                        <div class="small text-secondary fw-semibold mb-1 text-uppercase">Total Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?></div>
                        <h3 class="mb-0 text-primary fw-bold" style="font-family: var(--font-display);"><?php echo (int) $focus_total_hari; ?> <small class="text-secondary fs-6 fw-normal">hari</small></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info mb-4 border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i>
            <?php echo htmlspecialchars($periode_message); ?>
        </div>

        <div class="dashboard-table-shell mb-4">
            <div class="dashboard-table-header">
                <div>
                    <h5 class="mb-1 text-primary-dark fw-bold" style="font-family: var(--font-display);">Fokus Hak Cuti Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?></h5>
                    <p class="mb-0 text-secondary small">Tabel ini menampilkan masa yang paling relevan untuk ringkasan hak cuti Anda saat ini.</p>
                </div>
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
                        <?php if ($focus_rows === []): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary">
                                    Data fokus tahun 6 sampai 8 belum tersedia untuk ditampilkan dari riwayat kerja Anda saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($focus_rows as $row): ?>
                            <tr>
                                <td class="text-center text-muted"><?php echo (int) $row['tahun_ke']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars((string) $row['tahun_kalender']); ?></td>
                                <td class="text-center">
                                    <span class="fs-5 text-primary fw-bold"><?php echo (int) $row['hari_cuti']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo htmlspecialchars((string) $row['status_class']); ?> fw-semibold px-3 py-2">
                                        <?php echo htmlspecialchars((string) $row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-center border-top bg-light">
                <p class="employee-support-hint text-muted small mb-0 fw-medium">
                    <i class="bi bi-question-circle-fill me-1 text-primary"></i>Ringkasan ini otomatis mengikuti data kepegawaian akun Anda. Jika ada data yang perlu diperbarui, silakan hubungi tim HR.
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
