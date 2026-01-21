<?php
require_once '../includes/config.php';

//Verificar que el usuario esté logueado
if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

// Consultar todas las adopciones del usuario
$stmt = $pdo->prepare("
    SELECT 
        a.*, 
        an.nombre AS animal_nombre, 
        an.especie, 
        c.nombre AS centro_nombre,
        CASE 
            WHEN ap.estado = 'Pagado' THEN 'Pagado'
            WHEN ap.estado = 'Pendiente' THEN 'Pendiente de pago'
            ELSE 'Sin pago'
        END AS estado_pago
    FROM adopciones a
    JOIN animales an ON a.id_animal = an.id_animal  -- Unir información del animal
    JOIN centros c ON an.id_centro = c.id_centro     -- Unir información del centro
    LEFT JOIN adopciones_pagos ap ON a.id_adopcion = ap.id_adopcion  -- Unir pagos (si existen)
    WHERE a.id_usuario = ?                           -- Solo adopciones del usuario actual
    ORDER BY a.fecha_solicitud DESC                  -- Ordenar de más reciente a más antigua
");
$stmt->execute([$_SESSION['user_id']]);
$adopciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Adopciones - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!--Menú lateral del perfil -->
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>
            
            <!--Contenido principal-->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-light">
                        <h4>Mis Adopciones</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($adopciones) > 0): ?>
                            <!--Historial de adopciones-->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Animal</th>
                                            <th>Centro</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($adopciones as $a): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($a['animal_nombre']) ?></strong>
                                                <br>
                                                <small><?= $a['especie'] ?></small>
                                            </td>
                                            
                                            <td><?= htmlspecialchars($a['centro_nombre']) ?></td>                                            
                                            <td><?= date('d/m/Y', strtotime($a['fecha_solicitud'])) ?></td>                                            
                                            <td>
                                                <?php
                                                //Determinar color del badge según el estado de la adopción
                                                $estado_color = match($a['estado']) {
                                                    'Aprobada' => 'success',
                                                    'Pendiente' => 'warning', 
                                                    default => 'danger'       
                                                };
                                                ?>
                                                <span class="badge bg-<?= $estado_color ?>"><?= $a['estado'] ?></span>
                                            </td>
                                            <!--Estado del pago-->
                                            <td>
                                                <span class="badge bg-info"><?= $a['estado_pago'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No tienes adopciones todavía</h5>
                                <a href="<?= BASE_URL ?>/animales/" class="btn btn-success">
                                    Ver animales disponibles
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