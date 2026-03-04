<?php
$role = isset($_GET['role']) ? $_GET['role'] : 'hr';
$valid_roles = ['hr', 'employee'];
if (!in_array($role, $valid_roles)) {
    $role = 'hr';
}

$is_hr = ($role === 'hr');
$role_label = $is_hr ? 'HR' : 'Karyawan';
$role_action = $is_hr ? 'Masuk sebagai HR' : 'Masuk sebagai Karyawan';
$role_dashboard = $is_hr ? 'hr/dashboard.php' : 'employee/dashboard.php';
$switch_role = $is_hr ? 'employee' : 'hr';
$switch_label = $is_hr ? 'Masuk sebagai Karyawan' : 'Masuk sebagai HR';
$role_icon = $is_hr ? 'bi-shield-check' : 'bi-person';

$page_title = "Login $role_label - Sicuti HRD";
$page_class = "page-login role-$role";
include 'includes/header.php';
?>

<div class="login-split">
    <div class="login-form-panel">
        <div class="login-form-inner">
            <div class="login-brand-block">
                <div class="login-brand-logo d-flex align-items-center gap-2">
                    <svg viewBox="0 0 40 40" width="36" height="36" aria-hidden="true">
                        <rect x="4" y="4" width="32" height="32" rx="4" fill="var(--color-primary)" />
                        <polygon points="12,12 28,12 20,28" fill="var(--color-accent)" />
                    </svg>
                    <span class="text-muted small fw-bold">PT X</span>
                </div>
                <div class="login-brand-divider"></div>
                <div class="login-brand-logo d-flex align-items-center gap-2">
                    <img src="/assets/icons/sicuti-logo.svg" class="sicuti-logo" alt="Logo Sicuti HRD">
                    <span class="text-muted small fw-bold">Sicuti HRD</span>
                </div>
            </div>

            <div class="text-center mb-4">
                <span class="role-badge">
                    <i class="bi <?php echo $role_icon; ?> me-2"></i>
                    <?php echo htmlspecialchars($role_label); ?>
                </span>
            </div>

            <h1 class="login-heading">
                Selamat Datang, <?php echo htmlspecialchars($role_label); ?>
            </h1>

            <div class="demo-notice">
                <i class="bi bi-info-circle me-1"></i>
                Akses demo — tidak memerlukan autentikasi nyata.
            </div>

            <div class="login-fields mb-3">
                <div class="mb-3 text-start">
                    <label class="form-label text-muted small">Email (Demo)</label>
                    <input type="email" class="form-control bg-light" value="demo@perusahaan.com" readonly>
                </div>
                <div class="mb-4 text-start">
                    <label class="form-label text-muted small">Password</label>
                    <input type="password" class="form-control bg-light" value="********" readonly>
                </div>
            </div>

            <a href="<?php echo htmlspecialchars($role_dashboard); ?>" class="btn-login mb-3">
                <?php echo htmlspecialchars($role_action); ?>
            </a>

            <ul class="login-value-list">
                <li><i class="bi bi-check-circle-fill"></i> Perhitungan cuti otomatis & akurat</li>
                <li><i class="bi bi-check-circle-fill"></i> Laporan transparan untuk semua karyawan</li>
                <li><i class="bi bi-check-circle-fill"></i> Ekspor data ke Excel dalam satu klik</li>
            </ul>

            <div class="role-switch text-center pt-3 mt-4 border-top">
                <span class="text-muted small d-block mb-2">Atau</span>
                <a href="login.php?role=<?php echo htmlspecialchars($switch_role); ?>" class="text-decoration-none fw-medium">
                    <?php echo htmlspecialchars($switch_label); ?> <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="login-illustration-panel">
        <!-- Floating decorative elements -->
        <svg class="login-float float-1 decorative-float" style="top: 15%; left: 15%;" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="var(--color-primary)" opacity="0.1" />
        </svg>
        <svg class="login-float float-2 decorative-float" style="bottom: 20%; right: 15%;" viewBox="0 0 100 100">
            <rect x="20" y="20" width="60" height="60" rx="15" fill="var(--color-accent)" opacity="0.15" />
        </svg>
        
        <?php if ($is_hr): ?>
        <!-- Main Character Scene HR -->
        <svg class="login-illustration" viewBox="0 0 600 500" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <!-- Background element (screen/dashboard) -->
            <rect x="150" y="100" width="300" height="200" rx="10" fill="var(--color-surface-alt)" stroke="var(--color-border)" stroke-width="2"/>
            <rect x="150" y="300" width="300" height="20" rx="5" fill="var(--color-primary-subtle)"/>
            <rect x="270" y="320" width="60" height="30" fill="var(--color-primary-subtle)"/>
            <rect x="220" y="350" width="160" height="10" rx="5" fill="var(--color-primary-subtle)"/>
            
            <!-- Screen content -->
            <rect x="170" y="120" width="80" height="20" rx="4" fill="var(--color-primary-light)" opacity="0.5"/>
            <rect x="170" y="160" width="260" height="100" rx="5" fill="var(--color-surface)"/>
            <path d="M180 240 L220 180 L280 220 L350 170 L410 210" fill="none" stroke="var(--color-accent)" stroke-width="4" stroke-linecap="round"/>
            <circle cx="220" cy="180" r="5" fill="var(--color-primary)"/>
            <circle cx="280" cy="220" r="5" fill="var(--color-primary)"/>
            <circle cx="350" cy="170" r="5" fill="var(--color-primary)"/>
            
            <!-- Character body -->
            <path d="M220 450 C220 380 260 360 300 360 C340 360 380 380 380 450" fill="var(--color-primary)"/>
            <!-- Head -->
            <circle cx="300" cy="310" r="40" fill="var(--color-accent-light)"/>
            <!-- Desk -->
            <rect x="100" y="450" width="400" height="50" rx="10" fill="var(--color-primary-dark)"/>
            
            <!-- Document prop on desk -->
            <rect x="180" y="440" width="60" height="10" rx="2" fill="var(--color-surface)"/>
            <rect x="190" y="435" width="40" height="5" rx="2" fill="var(--color-primary-subtle)"/>
        </svg>
        <?php else: ?>
        <!-- Main Character Scene Employee -->
        <svg class="login-illustration" viewBox="0 0 600 500" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <!-- Background element (Large Calendar) -->
            <rect x="180" y="120" width="240" height="240" rx="15" fill="var(--color-surface-alt)" stroke="var(--color-border)" stroke-width="2"/>
            <rect x="180" y="120" width="240" height="60" rx="15" fill="var(--color-accent)"/>
            <!-- Square off bottom corners of header -->
            <rect x="180" y="160" width="240" height="20" fill="var(--color-accent)"/>
            
            <!-- Calendar Binder Rings -->
            <rect x="220" y="100" width="10" height="40" rx="5" fill="var(--color-primary-dark)"/>
            <rect x="370" y="100" width="10" height="40" rx="5" fill="var(--color-primary-dark)"/>
            
            <!-- Calendar Grid -->
            <rect x="200" y="200" width="40" height="40" rx="4" fill="var(--color-surface)"/>
            <rect x="250" y="200" width="40" height="40" rx="4" fill="var(--color-primary-subtle)"/>
            <rect x="300" y="200" width="40" height="40" rx="4" fill="var(--color-surface)"/>
            <rect x="350" y="200" width="40" height="40" rx="4" fill="var(--color-surface)"/>
            
            <rect x="200" y="250" width="40" height="40" rx="4" fill="var(--color-surface)"/>
            <rect x="250" y="250" width="40" height="40" rx="4" fill="var(--color-surface)"/>
            <rect x="300" y="250" width="40" height="40" rx="4" fill="var(--color-accent-light)"/>
            <rect x="350" y="250" width="40" height="40" rx="4" fill="var(--color-surface)"/>
            
            <!-- Character body (Standing/Looking) -->
            <path d="M120 480 C120 390 160 350 200 350 C240 350 280 390 280 480" fill="var(--color-primary)"/>
            <!-- Head -->
            <circle cx="200" cy="300" r="35" fill="var(--color-primary-light)"/>
            
            <!-- Hand pointing/holding -->
            <path d="M250 380 L320 310" stroke="var(--color-primary)" stroke-width="15" stroke-linecap="round"/>
            
            <!-- Floor line -->
            <line x1="80" y1="480" x2="520" y2="480" stroke="var(--color-primary-dark)" stroke-width="4" stroke-linecap="round"/>
        </svg>
        <?php endif; ?>
    </div>
</div>
