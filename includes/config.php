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
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
// Obtener la ruta base correctamente
$script_name = $_SERVER['SCRIPT_NAME'];
$request_uri = $_SERVER['REQUEST_URI'];
//If para poner la ruta correctamente
if (strpos($script_name, '/adoptaweb') !== false) {
    $base_path = '/adoptaweb';
} else {
    // Buscar el directorio del script
    $dir = dirname($script_name);
    $base_path = ($dir == '/') ? '' : $dir;
}
define('BASE_URL', $protocol . $host . $base_path);
//Datos base de datos
define('DB_HOST', 'mysql');
define('DB_NAME', 'adoptaweb');
define('DB_USER', 'root');
define('DB_PASS', 'root');
//Directorios (relativos al sistema de archivos)
define('ROOT_PATH', dirname(__DIR__));
define('UPLOADS_DIR', ROOT_PATH . '/assets/uploads/');
define('IMG_DIR', ROOT_PATH . '/assets/img/');
//URLs públicas (relativas al navegador)
define('CSS_URL', BASE_URL . '/css/');
define('JS_URL', BASE_URL . '/js/');
define('ASSETS_URL', BASE_URL . '/assets/');
//Require de conexión a database
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

//Verificar cookie de consentimiento
if (!isset($_SESSION['cookie_consent']) && !isset($_COOKIE['cookie_consent'])) {
    $_SESSION['show_cookie_banner'] = true;
}
?>