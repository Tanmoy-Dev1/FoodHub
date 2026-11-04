<?php
session_start();

// Session gulo clear kore dewa
$_SESSION = array();

// Jodi session cookie use kora hoy, tahole setao delete kore dewa
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session destroy kora
session_destroy();

// Home page-e redirect kora
header("Location: index.php");
exit;
?>