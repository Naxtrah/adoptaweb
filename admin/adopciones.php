<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$stmt = $pdo->query("
    SELECT a.*, u.nombre as usuario_nombre, an.nombre as animal_nombre, c.nombre as centro_nombre
    FROM adopciones a
    JOIN usuarios u ON a.id_usuario = u.id_usuario
    JOIN animales an ON a.id_animal = an.id_animal
    JOIN centros c ON an.id_centro = c.id_centro
    ORDER BY a.fecha_solicitud DESC
");
$adopciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Adopciones - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-heart me-2"></i>Gestionar Adopciones</h2>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr><th>ID</th><th>Usuario</th><th>Animal</th><th>Centro</th><th>Fecha Solicitud</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($adopciones as $a): ?>
                    <tr>
                        <td><?= $a['id_adopcion'] ?></td>
                        <td><?= htmlspecialchars($a['usuario_nombre']) ?></td>
                        <td><?= htmlspecialchars($a['animal_nombre']) ?></td>
                        <td><?= htmlspecialchars($a['centro_nombre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($a['fecha_solicitud'])) ?></td>
                        <td><span class="badge bg-<?= $a['estado'] == 'Pendiente' ? 'warning' : ($a['estado'] == 'Aprobada' ? 'success' : 'danger') ?>"><?= $a['estado'] ?></span></td>
                        <td><a href="?accion=editar&id=<?= $a['id_adopcion'] ?>" class="btn btn-sm btn-warning">Editar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>