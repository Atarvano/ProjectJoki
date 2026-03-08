<?php
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="h3 mb-1 text-gray-800">Laporan Cuti Karyawan</h2>
        <p class="text-muted mb-0">Rekap ini dihitung langsung dari data karyawan aktif yang tersimpan saat ini.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <form method="GET" class="d-inline-block">
            <select name="filter_status" class="form-select border-secondary text-secondary fw-medium" onchange="this.form.submit()" style="width: auto;">
                <option value="">Semua Status</option>
                <option value="Tersedia" <?php echo $filter_status === 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                <option value="Menunggu" <?php echo $filter_status === 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                <option value="Rencana" <?php echo $filter_status === 'Rencana' ? 'selected' : ''; ?>>Rencana</option>
            </select>
        </form>

        <?php if (count($report_rows) > 0): ?>
            <a href="export.php<?php echo $filter_status !== '' ? '?filter_status=' . urlencode($filter_status) : ''; ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
        <?php else: ?>
            <a class="btn btn-success disabled" style="pointer-events: none;" aria-disabled="true">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show mb-4 shadow-sm border-0">
        <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle-fill text-success' : ($flash['type'] === 'danger' ? 'exclamation-circle-fill text-danger' : 'info-circle-fill text-info'); ?> me-2 fs-5 align-middle"></i>
        <span class="align-middle"><?php echo htmlspecialchars($flash['message']); ?></span>
        <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex align-items-center gap-2 text-muted flex-wrap">
            <i class="bi bi-info-circle text-primary"></i>
            <small>Gunakan filter status untuk melihat posisi hak cuti tahun berjalan, lalu buka detail karyawan untuk meninjau data sumbernya.</small>
        </div>
        <?php if ($invalid_join_date_count > 0): ?>
            <div class="alert alert-warning mt-3 mb-0 py-2 px-3 small">
                Ada <?php echo $invalid_join_date_count; ?> karyawan yang belum bisa dihitung karena tanggal bergabung belum valid.
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <?php if (count($report_rows) === 0): ?>
            <div class="p-5 text-center text-muted bg-light border-bottom">
                <div class="mb-3">
                    <i class="bi bi-people text-secondary opacity-50" style="font-size: 4rem;"></i>
                </div>
                <?php if ($filter_status !== ''): ?>
                    <h4 class="h5 mb-2">Tidak Ada Data untuk Filter Ini</h4>
                    <p class="mb-4">Belum ada karyawan dengan status <?php echo htmlspecialchars($filter_status); ?> pada tahun berjalan.</p>
                    <a href="reports.php" class="btn btn-outline-secondary px-4">Hapus Filter</a>
                <?php else: ?>
                    <h4 class="h5 mb-2">Belum Ada Rekap yang Bisa Ditampilkan</h4>
                    <p class="mb-4">Tambahkan atau lengkapi data karyawan terlebih dahulu agar laporan hak cuti dapat dihitung otomatis.</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="employees.php" class="btn btn-primary px-4">
                            <i class="bi bi-people me-2"></i>Kelola Karyawan
                        </a>
                        <a href="dashboard.php" class="btn btn-outline-secondary px-4">Kembali ke Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="text-center py-3 ps-4" style="width: 60px;">#</th>
                            <th class="py-3">Karyawan</th>
                            <th class="text-center py-3">Bergabung</th>
                            <th class="text-center py-3">Hak Cuti 8 Tahun</th>
                            <th class="text-center py-3">Status Tahun Ini</th>
                            <th class="text-center py-3 pe-4" style="width: 160px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_rows as $index => $report): ?>
                            <tr>
                                <td class="text-center text-muted fw-medium ps-4"><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3 py-1">
                                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <span class="fw-bold fs-6"><?php echo strtoupper(substr(htmlspecialchars($report['nama']), 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($report['nama']); ?></div>
                                            <div class="text-muted small">NIK: <?php echo htmlspecialchars($report['nik']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-medium"><?php echo $report['tahun_bergabung']; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-bold">
                                        <?php echo $report['total_cuti']; ?> Hari
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo htmlspecialchars($report['status_class']); ?> px-3 py-2">
                                        <?php echo htmlspecialchars($report['status_tahun_ini']); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="employee-detail.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-2"></i>Detail Karyawan
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
