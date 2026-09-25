<?php

function start_admin_session() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function require_admin() {
    start_admin_session();

    if (empty($_SESSION['admin_authenticated'])) {
        header("Location: index.php");
        exit();
    }
}

function admin_value_matches($expected, $actual) {
    if (function_exists('hash_equals')) {
        return hash_equals($expected, $actual);
    }

    return $expected === $actual;
}

function admin_login($email, $password) {
    if (
        admin_value_matches("safecenter@cpsu.edu.ph", $email) &&
        admin_value_matches("safecenter0123", $password)
    ) {
        start_admin_session();
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        return true;
    }

    return false;
}

?>
