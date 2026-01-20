<<<<<<< HEAD
<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();
/*Visualizar últimas adopciones de dicho usuario*/
$stmt = $pdo->prepare("
    SELECT a.*, an.nombre as animal_nombre, an.especie, c.nombre as centro_nombre
    FROM adopciones a
    JOIN animales an ON a.id_animal = an.id_animal
    JOIN centros c ON an.id_centro = c.id_centro
    WHERE a.id_usuario = ?
    ORDER BY a.fecha_solicitud DESC
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
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-light">
                        <h4>Mis Adopciones</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($adopciones) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Animal</th>
                                            <th>Centro</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($adopciones as $a): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($a['animal_nombre']) ?></strong><br><small><?= $a['especie'] ?></small></td>
                                            <td><?= htmlspecialchars($a['centro_nombre']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($a['fecha_solicitud'])) ?></td>
                                            <td><span class="badge bg-<?= $a['estado'] == 'Aprobada' ? 'success' : ($a['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>"><?= $a['estado'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No tienes adopciones todavía</h5>
                                <a href="<?= BASE_URL ?>/animales/" class="btn btn-success">Ver animales disponibles</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
=======
<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();

$stmt = $pdo->prepare("
    SELECT a.*, an.nombre as animal_nombre, an.especie, c.nombre as centro_nombre
    FROM adopciones a
    JOIN animales an ON a.id_animal = an.id_animal
    JOIN centros c ON an.id_centro = c.id_centro
    WHERE a.id_usuario = ?
    ORDER BY a.fecha_solicitud DESC
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
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-light">
                        <h4>Mis Adopciones</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($adopciones) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Animal</th>
                                            <th>Centro</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($adopciones as $a): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($a['animal_nombre']) ?></strong><br><small><?= $a['especie'] ?></small></td>
                                            <td><?= htmlspecialchars($a['centro_nombre']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($a['fecha_solicitud'])) ?></td>
                                            <td><span class="badge bg-<?= $a['estado'] == 'Aprobada' ? 'success' : ($a['estado'] == 'Pendiente' ? 'warning' : 'danger') ?>"><?= $a['estado'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No tienes adopciones todavía</h5>
                                <a href="<?= BASE_URL ?>/animales/" class="btn btn-success">Ver animales disponibles</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
</html>