<?php

session_set_cookie_params([
    'lifetime' => 86400 * 30,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
<<<<<<< HEAD
=======

define('MODO_DESARROLLO', true);
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = '/adoptaweb';
define('BASE_URL', $protocol . $host . $base_path);
define('DB_HOST', 'localhost');
define('DB_NAME', 'adoptaweb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ROOT_PATH', dirname(__DIR__));
define('UPLOADS_DIR', ROOT_PATH . '/uploads/');
define('IMG_DIR', ROOT_PATH . '/img/');
define('CSS_URL', BASE_URL . '/css/');
define('JS_URL', BASE_URL . '/js/');
define('ASSETS_URL', BASE_URL . '/assets/');

require_once ROOT_PATH . '/includes/conexion.php';

function estaLogueado() {
    return isset($_SESSION['user_id']);
}

function esAdmin() {
    return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}

function obtenerUsuario() {
    global $pdo;
    if (estaLogueado()) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

function sanitizar($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

<<<<<<< HEAD
//Verificar cookie de consentimiento
=======
// Verificar cookie de consentimiento
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
if (!isset($_SESSION['cookie_consent']) && !isset($_COOKIE['cookie_consent'])) {
    $_SESSION['show_cookie_banner'] = true;
}
?>