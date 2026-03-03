<?php
$page_title = 'Sicuti HRD - Kelola Cuti Karyawan dengan Mudah';
$page_class = 'page-landing';
include 'includes/header.php';
?>

<!-- Navbar -->
<nav class="navbar navbar-landing py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php" style="color: var(--color-primary-dark); font-family: var(--font-display); font-size: 1.5rem;">
            <svg viewBox="0 0 40 40" width="36" height="36" aria-hidden="true">
                <rect x="4" y="4" width="32" height="32" rx="8" fill="var(--color-primary)" />
                <path d="M12,24 L20,12 L28,24" stroke="var(--color-accent)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                <circle cx="20" cy="28" r="2" fill="var(--color-accent)" />
            </svg>
            Sicuti HRD
        </a>
        <div class="d-flex">
            <a href="login.php" class="btn btn-outline-primary fw-bold px-4" style="border-radius: var(--radius-lg);">Masuk</a>
        </div>
    </div>
</nav>

<!-- Section 1: Hero -->
<section class="hero-section">


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
                    <rect x="40" y="40" width="420" height="320" rx="8" fill="var(--color-surface)"
                        filter="drop-shadow(0 10px 15px rgba(15, 76, 92, 0.1))" />

                    <!-- Header of dashboard -->
                    <rect x="40" y="40" width="420" height="60" rx="8" fill="var(--color-primary)" />
                    <rect x="40" y="90" width="420" height="10" fill="var(--color-primary)" />
                    <!-- cover bottom radius -->
                    <circle cx="75" cy="70" r="15" fill="var(--color-accent)" />
                    <rect x="105" y="65" width="120" height="10" rx="5" fill="var(--color-surface)" opacity="0.8" />

                    <!-- Table rows -->
                    <g transform="translate(60, 120)">
                        <!-- Row 1 -->
                        <rect x="0" y="0" width="380" height="40" rx="6" fill="var(--color-primary-subtle)" />
                        <rect x="15" y="15" width="80" height="10" rx="5" fill="var(--color-primary-light)"
                            opacity="0.7" />
                        <rect x="120" y="15" width="40" height="10" rx="5" fill="var(--color-primary)" />
                        <rect x="250" y="15" width="100" height="10" rx="5" fill="var(--color-text-muted)"
                            opacity="0.3" />

                        <!-- Row 2 -->
                        <rect x="0" y="50" width="380" height="40" rx="6" fill="var(--color-primary-subtle)"
                            opacity="0.5" />
                        <rect x="15" y="65" width="60" height="10" rx="5" fill="var(--color-primary-light)"
                            opacity="0.7" />
                        <rect x="120" y="65" width="40" height="10" rx="5" fill="var(--color-primary)" />

                        <!-- Row 3 -->
                        <rect x="0" y="100" width="380" height="40" rx="6" fill="var(--color-primary-subtle)"
                            opacity="0.5" />
                        <rect x="15" y="115" width="70" height="10" rx="5" fill="var(--color-primary-light)"
                            opacity="0.7" />
                        <rect x="120" y="115" width="40" height="10" rx="5" fill="var(--color-accent)" />

                        <!-- Row 4 -->
                        <rect x="0" y="150" width="380" height="40" rx="6" fill="var(--color-primary-subtle)"
                            opacity="0.5" />
                        <rect x="15" y="165" width="90" height="10" rx="5" fill="var(--color-primary-light)"
                            opacity="0.7" />
                        <rect x="120" y="165" width="40" height="10" rx="5" fill="var(--color-primary)" />
                        <rect x="250" y="165" width="80" height="10" rx="5" fill="var(--color-text-muted)"
                            opacity="0.3" />
                    </g>

                    <!-- Floating Calendar/Date element -->
                    <g transform="translate(340, 260)">
                        <rect x="0" y="0" width="120" height="110" rx="8" fill="var(--color-surface)"
                            filter="drop-shadow(0 4px 6px rgba(15, 76, 92, 0.1))" />
                        <rect x="0" y="0" width="120" height="35" rx="8" fill="var(--color-accent)" />
                        <rect x="0" y="25" width="120" height="10" fill="var(--color-accent)" />
                        <!-- cover bottom radius -->
                        <rect x="35" y="60" width="20" height="25" rx="4" fill="var(--color-primary-light)" />
                        <rect x="65" y="55" width="20" height="30" rx="4" fill="var(--color-primary)" />
                    </g>

                    <!-- Floating Export/Doc element -->
                    <g transform="translate(10, 240)">
                        <rect x="0" y="0" width="80" height="100" rx="6" fill="var(--color-primary-light)"
                            filter="drop-shadow(0 4px 6px rgba(15, 76, 92, 0.15))" />
                        <rect x="15" y="25" width="30" height="6" rx="3" fill="var(--color-surface)" />
                        <rect x="15" y="45" width="50" height="6" rx="3" fill="var(--color-surface)" opacity="0.7" />
                        <rect x="15" y="65" width="40" height="6" rx="3" fill="var(--color-surface)" opacity="0.7" />
                        <polygon points="55,0 80,0 80,25 55,25" fill="var(--color-surface)" opacity="0.3" />
                    </g>

                    <!-- Decorative accents -->
                    <circle cx="430" cy="50" r="8" fill="var(--color-accent)" opacity="0.6" />
                    <circle cx="80" cy="340" r="12" fill="var(--color-primary)" opacity="0.2" />
                    <rect x="200" y="330" width="40" height="40" rx="8" fill="var(--color-accent)" opacity="0.1"
                        transform="rotate(15 220 350)" />
                </svg>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Benefits -->
<section class="benefits-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-heading">Mengapa Sicuti HRD?</h2>
                <p class="text-muted mt-3" style="max-width: 600px; margin: 0 auto;">
                    Dirancang khusus untuk kebutuhan pengelolaan cuti di Indonesia.
                </p>
            </div>
        </div>
        <div class="row g-4">
            <!-- Benefit 1: Hitung Otomatis -->
            <div class="col-md-4">
                <div class="gradient-card h-100">
                    <div class="card-body">
                        <div class="benefit-icon-wrap mb-3">
                            <!-- Small inline SVG icon: calculator/abacus geometric shape, ~40x40 viewBox -->
                            <svg viewBox="0 0 40 40" width="48" height="48" aria-hidden="true">
                                <rect x="5" y="5" width="30" height="30" rx="4" fill="var(--color-primary-subtle)" />
                                <rect x="10" y="10" width="20" height="8" rx="2" fill="var(--color-primary)" />
                                <circle cx="13" cy="24" r="2" fill="var(--color-accent)" />
                                <circle cx="20" cy="24" r="2" fill="var(--color-accent)" />
                                <circle cx="27" cy="24" r="2" fill="var(--color-accent)" />
                                <circle cx="13" cy="30" r="2" fill="var(--color-primary-light)" />
                                <circle cx="20" cy="30" r="2" fill="var(--color-primary-light)" />
                                <circle cx="27" cy="30" r="2" fill="var(--color-primary-light)" />
                            </svg>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Hitung Otomatis</h3>
                        <p class="text-muted mb-0">
                            Tidak perlu menghitung manual. Sistem otomatis mengkalkulasi hak cuti tahunan dan cuti besar
                            berdasarkan masa kerja.
                        </p>
                    </div>
                </div>
            </div>
            <!-- Benefit 2: Laporan Jelas -->
            <div class="col-md-4">
                <div class="gradient-card h-100">
                    <div class="card-body">
                        <div class="benefit-icon-wrap mb-3">
                            <!-- Small inline SVG icon: table/chart geometric shape -->
                            <svg viewBox="0 0 40 40" width="48" height="48" aria-hidden="true">
                                <rect x="4" y="8" width="32" height="24" rx="2" fill="var(--color-primary-subtle)" />
                                <rect x="4" y="8" width="32" height="6" rx="2" fill="var(--color-primary)" />
                                <rect x="6" y="16" width="28" height="4" rx="1" fill="var(--color-accent)"
                                    opacity="0.8" />
                                <rect x="6" y="22" width="28" height="4" rx="1" fill="var(--color-primary-light)"
                                    opacity="0.5" />
                            </svg>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Laporan Jelas</h3>
                        <p class="text-muted mb-0">
                            Tabel hak cuti 8 tahun yang mudah dibaca. Transparansi penuh untuk HR dan karyawan.
                        </p>
                    </div>
                </div>
            </div>
            <!-- Benefit 3: Ekspor Excel -->
            <div class="col-md-4">
                <div class="gradient-card h-100">
                    <div class="card-body">
                        <div class="benefit-icon-wrap mb-3">
                            <!-- Small inline SVG icon: document/spreadsheet with download arrow -->
                            <svg viewBox="0 0 40 40" width="48" height="48" aria-hidden="true">
                                <rect x="8" y="4" width="24" height="32" rx="3" fill="var(--color-primary-subtle)" />
                                <polygon points="32,4 32,12 24,4" fill="var(--color-primary-light)" opacity="0.5" />
                                <rect x="14" y="14" width="12" height="3" rx="1" fill="var(--color-primary)" />
                                <rect x="14" y="20" width="12" height="3" rx="1" fill="var(--color-primary)" />
                                <path d="M20,26 L20,32 M17,29 L20,32 L23,29" stroke="var(--color-accent)"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                            </svg>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Ekspor Excel</h3>
                        <p class="text-muted mb-0">
                            Unduh laporan lengkap dalam format Excel untuk arsip, audit, atau pemrosesan lebih lanjut.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: How It Works (NEW) -->
<section class="how-it-works-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-heading">Cara Kerja</h2>
                <p class="text-muted mt-3" style="max-width: 600px; margin: 0 auto;">
                    Tiga langkah sederhana untuk mengelola hak cuti karyawan.
                </p>
            </div>
        </div>
        <div class="row g-4">
            <!-- Step 1 -->
            <div class="col-md-4">
                <div class="step-card text-center">
                    <div class="step-number">1</div>
                    <div class="step-illustration mb-3">
                        <svg viewBox="0 0 120 100" aria-hidden="true">
                            <rect x="20" y="20" width="80" height="60" rx="6" fill="var(--color-primary-subtle)" />
                            <rect x="20" y="20" width="80" height="15" rx="4" fill="var(--color-primary)" />
                            <rect x="30" y="45" width="60" height="8" rx="2" fill="var(--color-surface)" />
                            <circle cx="40" cy="49" r="2" fill="var(--color-text-muted)" />
                            <!-- cursor -->
                            <polygon points="65,55 75,70 70,72 75,80 72,82 67,73 60,78" fill="var(--color-accent)" />
                        </svg>
                    </div>
                    <h3 class="h5 fw-bold mb-2">Masukkan Tahun Bergabung</h3>
                    <p class="text-muted small mb-0">HR memasukkan tahun bergabung karyawan ke dalam kalkulator.</p>
                </div>
            </div>
            <!-- Step 2 -->
            <div class="col-md-4">
                <div class="step-card text-center">
                    <div class="step-number">2</div>
                    <div class="step-illustration mb-3">
                        <svg viewBox="0 0 120 100" aria-hidden="true">
                            <rect x="10" y="15" width="100" height="70" rx="4" fill="var(--color-primary-subtle)" />
                            <rect x="15" y="25" width="90" height="8" rx="2" fill="var(--color-primary-light)"
                                opacity="0.3" />
                            <rect x="15" y="38" width="90" height="8" rx="2" fill="var(--color-accent)" opacity="0.8" />
                            <rect x="15" y="51" width="90" height="8" rx="2" fill="var(--color-primary-light)"
                                opacity="0.3" />
                            <rect x="15" y="64" width="90" height="8" rx="2" fill="var(--color-primary-light)"
                                opacity="0.3" />
                        </svg>
                    </div>
                    <h3 class="h5 fw-bold mb-2">Lihat Tabel 8 Tahun</h3>
                    <p class="text-muted small mb-0">Sistem menampilkan tabel hak cuti lengkap dari tahun ke-1 hingga
                        ke-8.</p>
                </div>
            </div>
            <!-- Step 3 -->
            <div class="col-md-4">
                <div class="step-card text-center">
                    <div class="step-number">3</div>
                    <div class="step-illustration mb-3">
                        <svg viewBox="0 0 120 100" aria-hidden="true">
                            <rect x="35" y="15" width="50" height="70" rx="4" fill="var(--color-primary-subtle)" />
                            <path d="M45,45 L55,55 L75,35" stroke="var(--color-primary)" stroke-width="4" fill="none"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <rect x="45" y="65" width="30" height="4" rx="2" fill="var(--color-primary-light)"
                                opacity="0.5" />
                            <circle cx="85" cy="75" r="15" fill="var(--color-accent)" />
                            <path d="M85,68 L85,82 M80,77 L85,82 L90,77" stroke="white" stroke-width="2" fill="none"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="h5 fw-bold mb-2">Ekspor Laporan</h3>
                    <p class="text-muted small mb-0">Simpan dan ekspor laporan cuti ke format Excel dengan satu klik.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Stats / Trust Indicators (NEW) -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Stat 1 -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-block text-center">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Tahun Cakupan</div>
                    <p class="stat-desc text-muted small">Perhitungan lengkap dari tahun pertama hingga kedelapan</p>
                </div>
            </div>
            <!-- Stat 2 -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-block text-center">
                    <div class="stat-number">2</div>
                    <div class="stat-label">Akses Peran</div>
                    <p class="stat-desc text-muted small">Tampilan khusus untuk HR dan karyawan</p>
                </div>
            </div>
            <!-- Stat 3 -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-block text-center">
                    <div class="stat-number">.xlsx</div>
                    <div class="stat-label">Format Ekspor</div>
                    <p class="stat-desc text-muted small">Laporan siap pakai dalam format Excel standar</p>
                </div>
            </div>
            <!-- Stat 4 -->
            <div class="col-sm-6 col-lg-3">
                <div class="stat-block text-center">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Transparan</div>
                    <p class="stat-desc text-muted small">Karyawan dapat memverifikasi perhitungan cuti mereka sendiri
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Demo Info (redesigned) -->
<section class="demo-info-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="gradient-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <svg viewBox="0 0 40 40" width="48" height="48" aria-hidden="true">
                                <circle cx="20" cy="20" r="16" fill="var(--color-primary-subtle)" />
                                <circle cx="20" cy="20" r="10" fill="var(--color-accent)" opacity="0.2" />
                                <path d="M20,12 L20,16 M20,24 L20,28" stroke="var(--color-accent)" stroke-width="2"
                                    stroke-linecap="round" />
                                <path d="M12,20 L16,20 M24,20 L28,20" stroke="var(--color-accent)" stroke-width="2"
                                    stroke-linecap="round" />
                                <circle cx="20" cy="20" r="4" fill="var(--color-accent)" />
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold mb-3">Tentang Demo Ini</h2>
                        <p class="mb-0 text-muted" style="max-width: 540px; margin: 0 auto;">
                            Ini adalah prototipe v1 dari Sicuti HRD. Aplikasi berjalan dalam mode simulasi tanpa
                            database persisten.
                            Anda dapat mencoba semua fitur layaknya di lingkungan produksi — data akan di-reset setiap
                            sesi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 6: Bottom CTA (redesigned) -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="section-heading mb-3">Siap Mencoba?</h2>
                <p class="lead text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                    Pilih peran Anda dan mulai jelajahi simulasi demo Sicuti HRD.
                </p>
                <a href="login.php" class="btn-cta">
                    Masuk ke Demo <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>