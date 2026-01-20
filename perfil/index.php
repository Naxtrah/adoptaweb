<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$user = obtenerUsuario();
if (!$user) {
    header('Location: ' . BASE_URL . '/auth/logout.php');
    exit();
}

<<<<<<< HEAD
//Estadísticas de adopciones
=======
// Estadísticas de adopciones
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
$adopciones = $pdo->prepare("
    SELECT COUNT(*) as total,
           SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
           SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes
    FROM adopciones 
    WHERE id_usuario = ?
");
$adopciones->execute([$_SESSION['user_id']]);
$stats = $adopciones->fetch();

<<<<<<< HEAD
//Últimas adopciones
=======
// Últimas adopciones
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
$ultimas = $pdo->prepare("
    SELECT a.*, an.nombre as animal_nombre, an.especie, c.nombre as centro_nombre
    FROM adopciones a
    JOIN animales an ON a.id_animal = an.id_animal
    JOIN centros c ON an.id_centro = c.id_centro
    WHERE a.id_usuario = ?
    ORDER BY a.fecha_solicitud DESC
    LIMIT 5
");
$ultimas->execute([$_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row">
            <!-- Menú lateral externo -->
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>

<<<<<<< HEAD
            <!--Contenido principal-->
=======
            <!-- Contenido principal -->
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Resumen de mi actividad</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-center border-success">
                                    <div class="card-body">
                                        <h3 class="text-success"><?= $stats['total'] ?: 0 ?></h3>
                                        <p class="text-muted mb-0">Total adopciones</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center border-success">
                                    <div class="card-body">
                                        <h3 class="text-success"><?= $stats['aprobadas'] ?: 0 ?></h3>
                                        <p class="text-muted mb-0">Adopciones aprobadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center border-warning">
                                    <div class="card-body">
                                        <h3 class="text-warning"><?= $stats['pendientes'] ?: 0 ?></h3>
                                        <p class="text-muted mb-0">Pendientes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD
                        <!--Ultimas solicitudes usuario-->
=======

>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
                        <h5 class="mb-3">Mis últimas solicitudes</h5>
                        <?php if ($ultimas->rowCount() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Animal</th>
                                            <th>Centro</th>
                                            <th>Fecha solicitud</th>
                                            <th>Estado</th>
<<<<<<< HEAD
=======
                                    
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($adopcion = $ultimas->fetch()): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($adopcion['animal_nombre']) ?></strong><br>
                                                <small class="text-muted"><?= $adopcion['especie'] ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($adopcion['centro_nombre']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($adopcion['fecha_solicitud'])) ?></td>
                                            <td>
                                                <?php 
                                                $badgeClass = [
                                                    'Pendiente' => 'warning',
                                                    'Aprobada' => 'success',
                                                    'Rechazada' => 'danger'
                                                ][$adopcion['estado']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>"><?= $adopcion['estado'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?= BASE_URL ?>/perfil/mis-adopciones.php" class="btn btn-success">Ver todas mis adopciones</a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No tienes adopciones todavía</h5>
                                <p class="mb-4">¡Encuentra a tu nuevo compañero!</p>
                                <a href="<?= BASE_URL ?>/animales/" class="btn btn-success">
                                    <i class="fas fa-search me-2"></i>Buscar animales
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>