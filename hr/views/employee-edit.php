<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="h3 mb-0 text-gray-800">Edit Karyawan</h2>
    <a href="/hr/employees.php" class="btn btn-outline-secondary">
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
        <form method="post" action="/hr/employee-edit.php?id=<?php echo $id; ?>" novalidate>
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
                <a href="/hr/employees.php" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
