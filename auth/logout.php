<?php
require_once '../includes/config.php';
//Verificación de cuando cerró sesión el usuario
if (isset($_SESSION['user_id'])) {
    error_log("Usuario " . $_SESSION['user_email'] . " cerró sesión el " . date('Y-m-d H:i:s'));
}

$_SESSION = array();
//Manejo de cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
//Destrucción de la sesión
session_destroy();

//Redigir al usuario al index principal
header('Location: ' . BASE_URL . '/index.php');
exit();
?>