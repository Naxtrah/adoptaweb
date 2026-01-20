<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$f = basename($_GET['f'] ?? '');
if (!$f || !preg_match('/^factura_\d+_\d+\.pdf$/i', $f)) {
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

$file = __DIR__ . '/' . $f;
if (!file_exists($file)) {
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

$stmt = $pdo->prepare("
    SELECT 1
    FROM facturas f
    JOIN pagos p ON f.id_pago = p.id_pago
    JOIN adopciones_pagos ap ON p.id_pago = ap.id
    WHERE f.pdf_dirr = ? AND ap.id_usuario = ?
");
$stmt->execute([$f, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $f . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
?>