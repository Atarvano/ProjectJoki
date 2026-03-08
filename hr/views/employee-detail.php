<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="h3 mb-0 text-gray-800">Detail Karyawan</h2>
    <a href="/hr/employees.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <p class="mb-1"><strong>Akun login:</strong> <?php echo htmlspecialchars($account_status); ?></p>
                <p class="text-muted mb-0">Jika data karyawan ini dihapus permanen, akun login yang terhubung juga akan ikut terhapus otomatis.</p>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="/hr/kalkulator.php?karyawan_id=<?php echo (int) $employee['id']; ?>" class="btn btn-outline-primary">
                    <i class="bi bi-calculator me-2"></i>Lihat Hak Cuti
                </a>
                <?php if ($account_status === 'Belum dibuat'): ?>
                    <form method="post" action="/hr/employee-provision.php" class="m-0">
                        <input type="hidden" name="id" value="<?php echo (int) $employee['id']; ?>">
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
