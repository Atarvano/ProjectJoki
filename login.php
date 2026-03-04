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
        <svg class="login-float float-1 decorative-float" style="top: 15%; left: 15%;" viewBox="0 0 100 100" aria-hidden="true">
            <circle cx="50" cy="50" r="40" fill="var(--color-primary)" opacity="0.1" />
        </svg>
        <svg class="login-float float-2 decorative-float" style="bottom: 20%; right: 15%;" viewBox="0 0 100 100" aria-hidden="true">
            <rect x="20" y="20" width="60" height="60" rx="15" fill="var(--color-accent)" opacity="0.15" />
        </svg>
        <svg class="login-float float-3 decorative-float" style="top: 30%; right: 10%;" viewBox="0 0 100 100" aria-hidden="true">
            <polygon points="50,10 90,90 10,90" fill="var(--color-primary-light)" opacity="0.1" />
        </svg>
        
        <?php if ($is_hr): ?>
        <!-- Main Character Scene HR -->
        <svg class="login-illustration" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <!-- Background Elements Shared -->
            <circle cx="400" cy="300" r="250" fill="var(--color-surface)" opacity="0.5" />
            <circle cx="400" cy="300" r="180" fill="var(--color-surface-alt)" />
            
            <!-- HR Props: Screens and Charts -->
            <!-- Large Monitor -->
            <rect x="250" y="150" width="220" height="150" rx="8" fill="var(--color-surface)" stroke="var(--color-primary)" stroke-width="4" />
            <rect x="340" y="300" width="40" height="30" fill="var(--color-primary)" />
            <rect x="290" y="330" width="140" height="8" rx="4" fill="var(--color-primary)" />
            
            <!-- Screen Content: Chart -->
            <rect x="270" y="170" width="180" height="110" rx="4" fill="var(--color-primary-subtle)" />
            <path d="M 280 260 L 320 200 L 370 230 L 410 180 L 440 210" fill="none" stroke="var(--color-accent)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="320" cy="200" r="6" fill="var(--color-accent)" />
            <circle cx="370" cy="230" r="6" fill="var(--color-accent)" />
            <circle cx="410" cy="180" r="6" fill="var(--color-accent)" />
            
            <!-- Document / Report -->
            <rect x="520" y="240" width="100" height="140" rx="4" fill="var(--color-surface)" stroke="var(--color-primary-light)" stroke-width="4" transform="rotate(15 520 240)" />
            <rect x="540" y="270" width="60" height="6" rx="3" fill="var(--color-primary-light)" transform="rotate(15 520 240)" />
            <rect x="540" y="290" width="40" height="6" rx="3" fill="var(--color-primary-light)" transform="rotate(15 520 240)" />
            <rect x="540" y="310" width="50" height="6" rx="3" fill="var(--color-primary-light)" transform="rotate(15 520 240)" />
            
            <!-- Checkmark / Success Cue -->
            <circle cx="630" cy="220" r="20" fill="var(--color-success)" />
            <path d="M 620 220 L 626 226 L 640 212" fill="none" stroke="var(--color-surface)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            
            <!-- Character: HR (Analytical Pose, mid-shot) -->
            <!-- Body -->
            <path d="M 240 550 C 240 400 300 350 400 350 C 500 350 560 400 560 550" fill="var(--color-primary)" />
            <!-- Head -->
            <circle cx="400" cy="280" r="60" fill="var(--color-accent-light)" />
            <!-- Glasses -->
            <rect x="360" y="260" width="35" height="20" rx="4" fill="none" stroke="var(--color-primary-dark)" stroke-width="4" />
            <rect x="405" y="260" width="35" height="20" rx="4" fill="none" stroke="var(--color-primary-dark)" stroke-width="4" />
            <path d="M 395 270 L 405 270" stroke="var(--color-primary-dark)" stroke-width="4" />
            
            <!-- Arm pointing at screen -->
            <path d="M 460 400 Q 520 380 440 250" fill="none" stroke="var(--color-primary-dark)" stroke-width="24" stroke-linecap="round" />
            <!-- Desk Foreground -->
            <rect x="100" y="500" width="600" height="100" rx="8" fill="var(--color-primary-dark)" />
            <!-- Laptop on desk -->
            <rect x="250" y="480" width="160" height="20" rx="4" fill="var(--color-surface-alt)" />
            <polygon points="270,480 390,480 370,430 290,430" fill="var(--color-surface)" />
        </svg>
        <?php else: ?>
        <!-- Main Character Scene Employee -->
        <svg class="login-illustration" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
            <!-- Background Elements Shared -->
            <circle cx="400" cy="300" r="250" fill="var(--color-surface)" opacity="0.5" />
            <circle cx="400" cy="300" r="180" fill="var(--color-surface-alt)" />
            
            <!-- Employee Props: Calendar and Planning -->
            <!-- Large Calendar -->
            <rect x="450" y="120" width="200" height="220" rx="12" fill="var(--color-surface)" stroke="var(--color-primary-light)" stroke-width="4" />
            <rect x="450" y="120" width="200" height="60" fill="var(--color-accent)" rx="8" />
            <!-- Calendar Binder -->
            <rect x="480" y="100" width="12" height="40" rx="6" fill="var(--color-primary)" />
            <rect x="600" y="100" width="12" height="40" rx="6" fill="var(--color-primary)" />
            
            <!-- Calendar Grid -->
            <rect x="470" y="200" width="30" height="30" rx="4" fill="var(--color-primary-subtle)" />
            <rect x="510" y="200" width="30" height="30" rx="4" fill="var(--color-primary-subtle)" />
            <rect x="550" y="200" width="30" height="30" rx="4" fill="var(--color-accent-light)" />
            <rect x="590" y="200" width="30" height="30" rx="4" fill="var(--color-primary-subtle)" />
            
            <rect x="470" y="240" width="30" height="30" rx="4" fill="var(--color-primary-subtle)" />
            <rect x="510" y="240" width="30" height="30" rx="4" fill="var(--color-surface-alt)" />
            <rect x="550" y="240" width="30" height="30" rx="4" fill="var(--color-surface-alt)" />
            <rect x="590" y="240" width="30" height="30" rx="4" fill="var(--color-surface-alt)" />
            
            <!-- Airplane / Trip Cue -->
            <path d="M 680 180 L 730 160 L 710 200 L 700 190 L 680 200 Z" fill="var(--color-primary)" transform="rotate(15 680 180)" />
            
            <!-- Checklist Prop -->
            <rect x="180" y="220" width="120" height="160" rx="8" fill="var(--color-surface)" stroke="var(--color-primary-subtle)" stroke-width="4" transform="rotate(-10 180 220)" />
            <circle cx="210" cy="260" r="8" fill="var(--color-success)" transform="rotate(-10 180 220)" />
            <rect x="235" y="256" width="50" height="8" rx="4" fill="var(--color-primary-subtle)" transform="rotate(-10 180 220)" />
            <circle cx="210" cy="290" r="8" fill="var(--color-surface-alt)" stroke="var(--color-primary-subtle)" stroke-width="2" transform="rotate(-10 180 220)" />
            <rect x="235" y="286" width="40" height="8" rx="4" fill="var(--color-primary-subtle)" transform="rotate(-10 180 220)" />
            
            <!-- Character: Employee (Optimistic Pose, mid-shot) -->
            <!-- Body -->
            <path d="M 280 550 C 280 420 330 380 420 380 C 510 380 560 420 560 550" fill="var(--color-primary-dark)" />
            <!-- Head -->
            <circle cx="420" cy="300" r="60" fill="var(--color-primary-light)" />
            <!-- Backpack strap -->
            <path d="M 330 400 C 330 450 290 500 290 550" fill="none" stroke="var(--color-accent)" stroke-width="16" />
            
            <!-- Arm celebrating -->
            <path d="M 480 430 Q 560 400 530 280" fill="none" stroke="var(--color-primary-dark)" stroke-width="24" stroke-linecap="round" />
            
            <!-- Coffee cup -->
            <rect x="620" y="450" width="40" height="60" rx="4" fill="var(--color-surface)" stroke="var(--color-primary-subtle)" stroke-width="2" />
            <path d="M 660 460 C 680 460 680 490 660 490" fill="none" stroke="var(--color-surface)" stroke-width="6" />
            
            <!-- Table Foreground -->
            <rect x="100" y="520" width="600" height="80" rx="8" fill="var(--color-primary)" />
        </svg>
        <?php endif; ?>
    </div>
</div>
