<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM contactos WHERE id_contacto = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) {
    header('Location: contactos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Mensaje - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2>Mensaje de <?= htmlspecialchars($m['nombre']) ?></h2>
            <p><strong>Asunto:</strong> <?= htmlspecialchars($m['asunto']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($m['email']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($m['telefono'] ?? 'No indicado') ?></p>
            <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($m['fecha_contacto'])) ?></p>
            <hr>
            <p><?= nl2br(htmlspecialchars($m['mensaje'])) ?></p>
            <a href="contactos.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
</body>
</html>