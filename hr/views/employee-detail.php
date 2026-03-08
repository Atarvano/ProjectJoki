<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-1 text-gray-800">Detail Karyawan</h2>
        <p class="text-muted mb-0">Halaman ini dipakai HR untuk review profil dan hak cuti karyawan pada satu layar.</p>
    </div>
    <a href="<?php echo htmlspecialchars($back_url); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i><?php echo htmlspecialchars($back_label); ?>
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h3 class="h4 mb-1"><?php echo htmlspecialchars($employee['nama']); ?></h3>
                <p class="mb-1 text-muted">NIK: <?php echo htmlspecialchars(trim((string) ($employee['nik'] ?? '')) !== '' ? $employee['nik'] : '-'); ?></p>
                <p class="mb-0 text-muted">Status akun login: <strong><?php echo htmlspecialchars($account_status); ?></strong></p>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="/hr/employee-edit.php?id=<?php echo (int) $employee['id']; ?>" class="btn btn-primary">
                    <i class="bi bi-pencil-square me-2"></i>Edit Karyawan
                </a>

                <?php if ($account_status === 'Belum dibuat'): ?>
                    <form method="post" action="/hr/employee-provision.php" class="m-0">
                        <input type="hidden" name="id" value="<?php echo (int) $employee['id']; ?>">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-person-plus me-2"></i>Buat Akun Login
                        </button>
                    </form>
                <?php else: ?>
                    <span class="badge bg-success-subtle text-success-emphasis fs-6 align-self-center">Akun login sudah dibuat</span>
                <?php endif; ?>

                <form method="post" action="/hr/employee-delete.php" class="m-0" onsubmit="return confirm('Yakin ingin menghapus data karyawan ini?');">
                    <input type="hidden" name="id" value="<?php echo (int) $employee['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-2"></i>Hapus Karyawan
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="border rounded p-3 h-100 bg-light">
                    <p class="text-uppercase small text-muted mb-2">Profil singkat</p>
                    <p class="mb-1"><strong>Jabatan:</strong> <?php echo htmlspecialchars(trim((string) ($employee['jabatan'] ?? '')) !== '' ? $employee['jabatan'] : '-'); ?></p>
                    <p class="mb-1"><strong>Departemen:</strong> <?php echo htmlspecialchars(trim((string) ($employee['departemen'] ?? '')) !== '' ? $employee['departemen'] : '-'); ?></p>
                    <p class="mb-0"><strong>Tanggal bergabung:</strong>
                        <?php
                        $join_date_text = trim((string) ($employee['tanggal_bergabung'] ?? ''));
                        if ($join_date_text !== '' && strtotime($join_date_text) !== false) {
                            $join_date_text = date('d M Y', strtotime($join_date_text));
                        } elseif ($join_date_text === '') {
                            $join_date_text = '-';
                        }
                        echo htmlspecialchars($join_date_text);
                        ?>
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <p class="text-uppercase small text-muted mb-2">Ringkasan Hak Cuti</p>
                    <h4 class="h5 mb-1"><?php echo htmlspecialchars($leave_snapshot['title']); ?></h4>
                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($leave_snapshot['text']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
        <h3 class="h5 mb-1">Profil Karyawan</h3>
        <p class="text-muted mb-0">Data utama karyawan tetap ditampilkan penuh agar HR bisa review sebelum edit, buat akun, atau hapus data.</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <tbody>
                    <?php foreach ($fields as $field_key => $field_label): ?>
                        <?php
                        $value = trim((string) ($employee[$field_key] ?? ''));
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

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
        <h3 class="h5 mb-1">Ringkasan Hak Cuti</h3>
        <p class="text-muted mb-3">Hak cuti di bawah ini dihitung dari tanggal bergabung dan engine cuti yang sama dengan halaman lain.</p>

        <?php if ($leave_error !== ''): ?>
            <div class="alert alert-warning mb-3">
                <strong>Perhatian:</strong> <?php echo htmlspecialchars($leave_error); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body pt-0 px-4 pb-4">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tahap</th>
                        <th>Kalender</th>
                        <th>Hak Cuti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $labels = [
                        6 => 'Tahun ke-6',
                        7 => 'Tahun ke-7',
                        8 => 'Tahun ke-8',
                    ];

                    foreach ($labels as $tahun_ke => $label):
                        $current_row = null;

                        foreach ($leave_rows as $row) {
                            if ((int) ($row['tahun_ke'] ?? 0) === $tahun_ke) {
                                $current_row = $row;
                                break;
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($label); ?></td>
                            <td><?php echo htmlspecialchars($current_row ? (string) $current_row['tahun_kalender'] : '-'); ?></td>
                            <td><?php echo htmlspecialchars($current_row ? (string) $current_row['hari_cuti'] . ' hari' : '-'); ?></td>
                            <td>
                                <?php if ($current_row): ?>
                                    <span class="badge <?php echo htmlspecialchars((string) ($current_row['status_class'] ?? 'bg-secondary')); ?>">
                                        <?php echo htmlspecialchars((string) ($current_row['status'] ?? '-')); ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
