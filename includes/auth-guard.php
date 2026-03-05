<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cekLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function cekRole($required_role)
{
    cekLogin();

    if ($_SESSION['role'] !== $required_role) {
        if ($_SESSION['role'] === 'hr') {
            header('Location: /hr/dashboard.php');
        } else {
            header('Location: /employee/dashboard.php');
        }
        exit;
    }
}
