<?php
$page_title = 'Sicuti HRD - Kelola Cuti Karyawan dengan Mudah';
$page_class = 'page-landing';
include 'includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title accent-bar">
                    Kelola Hak Cuti <br>
                    <span class="text-accent">Karyawan Anda</span>
                </h1>
                <p class="hero-subtitle">
                    Sistem manajemen cuti yang transparan, akurat, dan mudah digunakan. 
                    Hitung hak cuti secara otomatis berdasarkan tanggal bergabung.
                </p>
                <div class="d-flex gap-3">
                    <a href="login.php" class="btn btn-primary btn-lg px-4 py-3 fw-semibold">
                        Coba Demo <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <!-- Decorative space / abstract element -->
                <div class="p-5 bg-white rounded-4 shadow-lg opacity-75" style="transform: rotate(-3deg);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Hak Cuti 2024</h6>
                            <small class="text-muted">Diperbarui Otomatis</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Benefits -->
<section class="benefits-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="h3 fw-bold text-center">Mengapa Sicuti HRD?</h2>
            </div>
        </div>
        <div class="row g-4">
            <!-- Benefit 1 -->
            <div class="col-md-4">
                <div class="card benefit-card accent-bar h-100">
                    <div class="card-body">
                        <i class="bi bi-calculator benefit-icon"></i>
                        <h3 class="h4 card-title mb-3">Hitung Otomatis</h3>
                        <p class="card-text text-muted">
                            Tidak perlu menghitung manual. Sistem otomatis mengkalkulasi hak cuti tahunan, cuti besar, dan sisa cuti berdasarkan masa kerja.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Benefit 2 -->
            <div class="col-md-4">
                <div class="card benefit-card accent-bar h-100">
                    <div class="card-body">
                        <i class="bi bi-table benefit-icon"></i>
                        <h3 class="h4 card-title mb-3">Laporan Jelas</h3>
                        <p class="card-text text-muted">
                            Tabel hak cuti 8 tahun yang mudah dibaca. Transparansi penuh untuk HR dan karyawan mengenai jatah cuti mereka.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Benefit 3 -->
            <div class="col-md-4">
                <div class="card benefit-card accent-bar h-100">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-excel benefit-icon"></i>
                        <h3 class="h4 card-title mb-3">Ekspor Excel</h3>
                        <p class="card-text text-muted">
                            Unduh laporan lengkap dalam format Excel untuk keperluan arsip, audit, atau pemrosesan lebih lanjut dengan satu klik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Demo Info -->
<section class="demo-info-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card demo-info-card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 accent-bar mb-3">Tentang Demo Ini</h2>
                        <p class="mb-0 text-muted">
                            Ini adalah prototipe v1 dari Sicuti HRD. Aplikasi ini berjalan dalam mode simulasi tanpa database persisten. 
                            Anda dapat mencoba semua fitur layaknya di lingkungan produksi, namun data akan di-reset setiap sesi. 
                            Gunakan ini untuk mengevaluasi alur kerja dan kenyamanan antarmuka.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Bottom CTA -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-6 fw-bold mb-3">Mulai Sekarang</h2>
                <p class="lead text-muted mb-4">
                    Pilih peran Anda untuk masuk ke simulasi demo.
                </p>
                <a href="login.php" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-lg">
                    Masuk ke Demo
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
