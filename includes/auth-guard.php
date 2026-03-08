<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('AUTH_GUARD_TEST_MODE')) {
    define('AUTH_GUARD_TEST_MODE', false);
}

function authGuardRedirect($location)
{
    if (AUTH_GUARD_TEST_MODE) {
        return [
            'allowed' => false,
            'redirect' => $location,
            'session_cleared' => false,
        ];
    }

    header('Location: ' . $location);
    exit;
}

function authGuardLogoutRedirect()
{
    $_SESSION = [];

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    if (AUTH_GUARD_TEST_MODE) {
        return [
            'allowed' => false,
            'redirect' => '/login.php',
            'session_cleared' => true,
        ];
    }

    header('Location: /login.php');
    exit;
}

function authGuardGetUserById($user_id)
{
    if (AUTH_GUARD_TEST_MODE) {
        $test_users = $GLOBALS['AUTH_GUARD_TEST_USERS'] ?? [];

        if (isset($test_users[$user_id])) {
            return $test_users[$user_id];
        }

        return null;
    }

    require __DIR__ . '/../koneksi.php';

    $sql = 'SELECT id, username, karyawan_id, is_active FROM users WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user) {
        return $user;
    }

    return null;
}

function authGuardEmployeeExists($karyawan_id)
{
    if (AUTH_GUARD_TEST_MODE) {
        $test_karyawan = $GLOBALS['AUTH_GUARD_TEST_KARYAWAN'] ?? [];

        return isset($test_karyawan[$karyawan_id]);
    }

    require __DIR__ . '/../koneksi.php';

    $sql = 'SELECT id FROM karyawan WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'i', $karyawan_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $karyawan = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($karyawan) {
        return true;
    }

    return false;
}

function cekLogin()
{
    if (!isset($_SESSION['user_id'])) {
        return authGuardRedirect('/login.php');
    }

    $user_id = (int) $_SESSION['user_id'];
    if ($user_id <= 0) {
        return authGuardLogoutRedirect();
    }

    $user = authGuardGetUserById($user_id);
    if ($user === false) {
        return authGuardLogoutRedirect();
    }

    if (!$user) {
        return authGuardLogoutRedirect();
    }

    if ((int) ($user['is_active'] ?? 0) !== 1) {
        return authGuardLogoutRedirect();
    }

    if (AUTH_GUARD_TEST_MODE) {
        return [
            'allowed' => true,
            'redirect' => null,
            'session_cleared' => false,
            'user' => $user,
        ];
    }

    return $user;
}

function cekRole($required_role)
{
    $login_check = cekLogin();

    if (AUTH_GUARD_TEST_MODE) {
        if (!$login_check['allowed']) {
            return $login_check;
        }

        $user = $login_check['user'];
    } else {
        $user = $login_check;
    }

    if ($_SESSION['role'] !== $required_role) {
        if ($_SESSION['role'] === 'hr') {
            return authGuardRedirect('/hr/dashboard.php');
        }

        return authGuardRedirect('/employee/dashboard.php');
    }

    if ($required_role === 'employee') {
        $karyawan_id = isset($user['karyawan_id']) ? (int) $user['karyawan_id'] : 0;

        if ($karyawan_id <= 0) {
            return authGuardLogoutRedirect();
        }

        if (!authGuardEmployeeExists($karyawan_id)) {
            return authGuardLogoutRedirect();
        }
    }

    if (AUTH_GUARD_TEST_MODE) {
        return [
            'allowed' => true,
            'redirect' => null,
            'session_cleared' => false,
            'user' => $user,
        ];
    }

    return true;
}
