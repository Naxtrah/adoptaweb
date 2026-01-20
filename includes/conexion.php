<?php
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false
        ]
    );
} catch (PDOException $e) {
    if (defined('MODO_DESARROLLO') && MODO_DESARROLLO) {
        die("Error de conexión a la base de datos: " . $e->getMessage());
    } else {
        error_log("[" . date('Y-m-d H:i:s') . "] Error de conexión BD: " . $e->getMessage());
        header('Location: /error-500.html');
        exit();
    }
}
?>