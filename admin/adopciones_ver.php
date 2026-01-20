<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT a.*, u.nombre as usuario_nombre, u.email, u.telefono,
           an.nombre as animal_nombre, an.especie, an.raza, an.edad,
           c.nombre as centro_nombre
    FROM adopciones a
    JOIN usuarios u ON a.id_usuario = u.id_usuario
    JOIN animales an ON a.id_animal = an.id_animal
    JOIN centros c ON an.id_centro = c.id_centro
    WHERE a.id_adopcion = ?
");
$stmt->execute([$id]);
$adopcion = $stmt->fetch();

if (!$adopcion) {
    header('Location: adopciones.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Adopción - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-file-alt me-2"></i>Detalles de la Adopción #<?= $adopcion['id_adopcion'] ?></h2>
            <a href="adopciones.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Volver</a>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información de la Adopción</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Datos del Animal</h6>
                            <p><strong>Nombre:</strong> <?= htmlspecialchars($adopcion['animal_nombre']) ?></p>
                            <p><strong>Especie:</strong> <?= htmlspecialchars($adopcion['especie']) ?></p>
                            <p><strong>Raza:</strong> <?= htmlspecialchars($adopcion['raza']) ?></p>
                            <p><strong>Edad:</strong> <?= htmlspecialchars($adopcion['edad']) ?> años</p>
                            <p><strong>Centro:</strong> <?= htmlspecialchars($adopcion['centro_nombre']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Datos del Adoptante</h6>
                            <p><strong>Nombre:</strong> <?= htmlspecialchars($adopcion['usuario_nombre']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($adopcion['email']) ?></p>
                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($adopcion['telefono'] ?? 'No proporcionado') ?></p>
                            <p><strong>Fecha Solicitud:</strong> <?= date('d/m/Y', strtotime($adopcion['fecha_solicitud'])) ?></p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-<?= $adopcion['estado'] == 'Pendiente' ? 'warning' : ($adopcion['estado'] == 'Aprobada' ? 'success' : 'danger') ?>">
                                    <?= $adopcion['estado'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($adopcion['notas']): ?>
                    <hr>
                    <h6>Formulario de Adopción Completado por el Usuario</h6>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($adopcion['notas'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>