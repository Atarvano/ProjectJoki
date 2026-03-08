<?php
?>
<div class="container-fluid px-0">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-3">
                <div>
                    <p class="text-uppercase text-secondary small fw-semibold mb-1">Self-view Hak Cuti</p>
                    <h1 class="mb-2 text-primary-dark fw-bold" style="font-family: var(--font-display);">Hak Cuti <span class="text-accent">Saya</span></h1>
                    <p class="text-secondary mb-0">Ringkasan ini langsung mengambil data cuti dari akun karyawan yang sedang login.</p>
                </div>
                <div class="text-secondary small">
                    <div><strong>Nama:</strong> <?php echo htmlspecialchars($employee_name); ?></div>
                    <div><strong>Tanggal bergabung:</strong> <?php echo htmlspecialchars($employee_join_date_label); ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($load_error !== null): ?>
        <div class="alert alert-warning shadow-sm border-0 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($load_error); ?>
        </div>
    <?php else: ?>
        <div class="dashboard-table-shell mb-4">
            <div class="dashboard-table-header d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                <div>
                    <h5 class="mb-1 text-primary-dark fw-bold" style="font-family: var(--font-display);">Fokus Hak Cuti Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?></h5>
                    <p class="mb-0 text-secondary small">Tabel utama ini menampilkan tahun kerja 6, 7, dan 8 sebagai ringkasan hak cuti yang paling relevan.</p>
                </div>
                <div class="d-flex flex-wrap gap-3 text-lg-end">
                    <div>
                        <div class="small text-secondary fw-semibold mb-1 text-uppercase">Status Tahun Ini</div>
                        <span class="badge rounded-pill <?php echo htmlspecialchars($status_class_sekarang); ?>"><?php echo htmlspecialchars($status_sekarang); ?></span>
                    </div>
                    <div>
                        <div class="small text-secondary fw-semibold mb-1 text-uppercase">Total Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?></div>
                        <div class="fw-bold text-primary fs-4"><?php echo (int) $focus_total_hari; ?> <span class="text-secondary fs-6 fw-normal">hari</span></div>
                    </div>
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
        </div>

        <div class="alert alert-info mb-4 border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i>
            <?php echo htmlspecialchars($periode_message); ?>
        </div>
    <?php endif; ?>

    <div class="p-3 text-center border rounded bg-light">
        <p class="employee-support-hint text-muted small mb-0 fw-medium">
            <i class="bi bi-question-circle-fill me-1 text-primary"></i>Ringkasan ini otomatis mengikuti data kepegawaian akun Anda. Jika ada data yang perlu diperbarui, silakan hubungi tim HR.
        </p>
    </div>
</div>
