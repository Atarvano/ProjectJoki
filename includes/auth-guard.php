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
        ];
    }

    header('Location: ' . $location);
    exit;
}

function cekLogin()
{
    if (!isset($_SESSION['user_id'])) {
        return authGuardRedirect('/login.php');
    }

    return [
        'allowed' => true,
        'redirect' => null,
    ];
}

function cekRole($required_role)
{
    $login_check = cekLogin();
    if (is_array($login_check) && isset($login_check['allowed']) && $login_check['allowed'] === false) {
        return $login_check;
    }

    if ($_SESSION['role'] !== $required_role) {
        if ($_SESSION['role'] === 'hr') {
            return authGuardRedirect('/hr/dashboard.php');
        } else {
            return authGuardRedirect('/employee/dashboard.php');
        }
    }

    return [
        'allowed' => true,
        'redirect' => null,
    ];
}
