<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$stmt = $pdo->query("
    SELECT p.*, u.nombre as usuario_nombre, c.nombre as centro_nombre
    FROM pagos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    JOIN centros c ON p.id_centro = c.id_centro
    ORDER BY p.fecha_pago DESC
");
$pagos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-credit-card me-2"></i>Gestión de Pagos</h2>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr><th>ID</th><th>Usuario</th><th>Centro</th><th>Concepto</th><th>Monto</th><th>Método</th><th>Fecha</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $p): ?>
                    <tr>
                        <td><?= $p['id_pago'] ?></td>
                        <td><?= htmlspecialchars($p['usuario_nombre']) ?></td>
                        <td><?= htmlspecialchars($p['centro_nombre']) ?></td>
                        <td><?= htmlspecialchars($p['concepto']) ?></td>
                        <td><?= number_format($p['monto'], 2) ?> €</td>
                        <td><?= $p['metodo_pago'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>