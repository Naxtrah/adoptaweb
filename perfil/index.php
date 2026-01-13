<?php
require_once '../includes/config.php';

$user = obtenerUsuario();
//Query para sacar datos de animales que haya adoptado el user o estén pendientes
$adopciones = $pdo->prepare("
    SELECT COUNT(*) as total,
           SUM(CASE WHEN estado = 'Aprobada' THEN 1 ELSE 0 END) as aprobadas,
           SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes
    FROM adopciones 
    WHERE id_usuario = ?
");
$adopciones->execute([$_SESSION['user_id']]);
$stats = $adopciones->fetch();
//Datos animal de la database
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Mi Cuenta</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= BASE_URL ?>/perfil/" class="list-group-item list-group-item-action active">
                            <i class="fas fa-user-circle me-2"></i>Resumen
                        </a>
                        <a href="<?= BASE_URL ?>/perfil/mis-datos.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-edit me-2"></i>Mis Datos
                        </a>
                        <a href="<?= BASE_URL ?>/perfil/mis-adopciones.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-paw me-2"></i>Mis Adopciones
                        </a>
                        <a href="<?= BASE_URL ?>/auth/cambiar-password.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-user text-white fa-2x"></i>
                            </div>
                        </div>
                        <h5><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>
                        <span class="badge bg-success">Usuario</span>
                    </div>
                </div>
            </div>
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
                                            <th>Acciones</th>
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
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= $adopcion['estado'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/animales/detalle.php?id=<?= $adopcion['id_animal'] ?>" 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?= BASE_URL ?>/perfil/mis-adopciones.php" class="btn btn-success">
                                    Ver todas mis adopciones
                                </a>
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