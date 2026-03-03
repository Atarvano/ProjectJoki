<?php
$page_title = 'Sicuti HRD - Kelola Cuti Karyawan dengan Mudah';
$page_class = 'page-landing';
include 'includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero-section">
    <!-- Floating decorative SVG elements (positioned absolute, behind content) -->
    <svg viewBox="0 0 80 80" class="decorative-float float-1" style="top: 15%; left: 8%;" aria-hidden="true">
        <!-- Small geometric shape: circle + triangle composition in brand colors -->
        <circle cx="40" cy="40" r="20" fill="var(--color-primary-light)" opacity="0.6"/>
        <polygon points="40,10 70,70 10,70" fill="var(--color-primary)" opacity="0.8"/>
    </svg>
    <svg viewBox="0 0 60 60" class="decorative-float float-2" style="top: 60%; right: 5%;" aria-hidden="true">
        <!-- Small geometric shape: rotated square in accent color -->
        <rect x="15" y="15" width="30" height="30" fill="var(--color-accent)" opacity="0.7" transform="rotate(45 30 30)"/>
    </svg>
    <svg viewBox="0 0 50 50" class="decorative-float float-3" style="bottom: 10%; left: 20%;" aria-hidden="true">
        <!-- Small geometric shape: diamond in primary-subtle -->
        <polygon points="25,5 45,25 25,45 5,25" fill="var(--color-primary-subtle)" opacity="0.9"/>
    </svg>

    <div class="container" style="position: relative; z-index: 1;">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h1 class="hero-title">
                    Kelola Hak Cuti <br>
                    <span class="text-accent">Karyawan Anda</span>
                </h1>
                <p class="hero-subtitle">
                    Sistem manajemen cuti yang transparan, akurat, dan mudah digunakan. 
                    Hitung hak cuti secara otomatis berdasarkan tanggal bergabung.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="login.php" class="btn-cta">
                        Coba Demo <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <!-- HERO ILLUSTRATION: Large inline SVG, flat geometric style -->
                <svg viewBox="0 0 500 400" class="hero-illustration" aria-hidden="true">
                    <!-- Base dashboard screen -->
                    <rect x="25" y="25" width="450" height="350" rx="12" fill="var(--color-primary)" opacity="0.05" />
                    <rect x="40" y="40" width="420" height="320" rx="8" fill="var(--color-surface)" filter="drop-shadow(0 10px 15px rgba(15, 76, 92, 0.1))" />
                    
                    <!-- Header of dashboard -->
                    <rect x="40" y="40" width="420" height="60" rx="8" fill="var(--color-primary)" />
                    <rect x="40" y="90" width="420" height="10" fill="var(--color-primary)" /> <!-- cover bottom radius -->
                    <circle cx="75" cy="70" r="15" fill="var(--color-accent)" />
                    <rect x="105" y="65" width="120" height="10" rx="5" fill="var(--color-surface)" opacity="0.8" />
                    
                    <!-- Table rows -->
                    <g transform="translate(60, 120)">
                        <!-- Row 1 -->
                        <rect x="0" y="0" width="380" height="40" rx="6" fill="var(--color-primary-subtle)" />
                        <rect x="15" y="15" width="80" height="10" rx="5" fill="var(--color-primary-light)" opacity="0.7" />
                        <rect x="120" y="15" width="40" height="10" rx="5" fill="var(--color-primary)" />
                        <rect x="250" y="15" width="100" height="10" rx="5" fill="var(--color-text-muted)" opacity="0.3" />
                        
                        <!-- Row 2 -->
                        <rect x="0" y="50" width="380" height="40" rx="6" fill="var(--color-primary-subtle)" opacity="0.5" />
                        <rect x="15" y="65" width="60" height="10" rx="5" fill="var(--color-primary-light)" opacity="0.7" />
                        <rect x="120" y="65" width="40" height="10" rx="5" fill="var(--color-primary)" />
                        
                        <!-- Row 3 -->
                        <rect x="0" y="100" width="380" height="40" rx="6" fill="var(--color-primary-subtle)" opacity="0.5" />
                        <rect x="15" y="115" width="70" height="10" rx="5" fill="var(--color-primary-light)" opacity="0.7" />
                        <rect x="120" y="115" width="40" height="10" rx="5" fill="var(--color-accent)" />
                        
                        <!-- Row 4 -->
                        <rect x="0" y="150" width="380" height="40" rx="6" fill="var(--color-primary-subtle)" opacity="0.5" />
                        <rect x="15" y="165" width="90" height="10" rx="5" fill="var(--color-primary-light)" opacity="0.7" />
                        <rect x="120" y="165" width="40" height="10" rx="5" fill="var(--color-primary)" />
                        <rect x="250" y="165" width="80" height="10" rx="5" fill="var(--color-text-muted)" opacity="0.3" />
                    </g>
                    
                    <!-- Floating Calendar/Date element -->
                    <g transform="translate(340, 260)">
                        <rect x="0" y="0" width="120" height="110" rx="8" fill="var(--color-surface)" filter="drop-shadow(0 4px 6px rgba(15, 76, 92, 0.1))" />
                        <rect x="0" y="0" width="120" height="35" rx="8" fill="var(--color-accent)" />
                        <rect x="0" y="25" width="120" height="10" fill="var(--color-accent)" /> <!-- cover bottom radius -->
                        <rect x="35" y="60" width="20" height="25" rx="4" fill="var(--color-primary-light)" />
                        <rect x="65" y="55" width="20" height="30" rx="4" fill="var(--color-primary)" />
                    </g>
                    
                    <!-- Floating Export/Doc element -->
                    <g transform="translate(10, 240)">
                        <rect x="0" y="0" width="80" height="100" rx="6" fill="var(--color-primary-light)" filter="drop-shadow(0 4px 6px rgba(15, 76, 92, 0.15))" />
                        <rect x="15" y="25" width="30" height="6" rx="3" fill="var(--color-surface)" />
                        <rect x="15" y="45" width="50" height="6" rx="3" fill="var(--color-surface)" opacity="0.7" />
                        <rect x="15" y="65" width="40" height="6" rx="3" fill="var(--color-surface)" opacity="0.7" />
                        <polygon points="55,0 80,0 80,25 55,25" fill="var(--color-surface)" opacity="0.3" />
                    </g>
                    
                    <!-- Decorative accents -->
                    <circle cx="430" cy="50" r="8" fill="var(--color-accent)" opacity="0.6" />
                    <circle cx="80" cy="340" r="12" fill="var(--color-primary)" opacity="0.2" />
                    <rect x="200" y="330" width="40" height="40" rx="8" fill="var(--color-accent)" opacity="0.1" transform="rotate(15 220 350)" />
                </svg>
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
