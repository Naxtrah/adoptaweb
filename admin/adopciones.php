<?php
require_once '../includes/config.php';

if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}


if (isset($_POST['accion']) && isset($_POST['id_adopcion'])) {
    $id = (int)$_POST['id_adopcion'];
    $accion = $_POST['accion'];

    if ($accion === 'aprobar') {
       
        $stmt = $pdo->prepare("SELECT id_animal FROM adopciones WHERE id_adopcion = ?");
        $stmt->execute([$id]);
        $id_animal = $stmt->fetchColumn();
        
      
        $pdo->beginTransaction();
        try {
            $pdo->prepare("UPDATE adopciones SET estado='Aprobada', fecha_adopcion = CURDATE() WHERE id_adopcion=?")->execute([$id]);
            $pdo->prepare("UPDATE animales SET estado='Adoptado' WHERE id_animal=?")->execute([$id_animal]);

            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM adopciones_pagos WHERE id_adopcion = ?");
            $stmt->execute([$id]);
            $existe_pago = $stmt->fetchColumn();

            if (!$existe_pago) {
               
                $stmt = $pdo->prepare("SELECT id_usuario FROM adopciones WHERE id_adopcion = ?");
                $stmt->execute([$id]);
                $adopcion = $stmt->fetch();

                if ($adopcion) {
                    $token_pago = hash('sha256', uniqid('', true));
                    $sql_pago = "INSERT INTO adopciones_pagos (id_adopcion, id_usuario, monto, estado, token_pago) 
                                 VALUES (?, ?, 60.00, 'Pendiente', ?)";
                    $stmt = $pdo->prepare($sql_pago);
                    $stmt->execute([$id, $adopcion['id_usuario'], $token_pago]);
                }
            }
            $pdo->commit();
            $_SESSION['exito'] = "Adopción aprobada. Se ha generado un pago pendiente para el usuario.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        header('Location: adopciones.php');
        exit();
    }

    if ($accion === 'rechazar') {
        
        $stmt = $pdo->prepare("SELECT id_animal FROM adopciones WHERE id_adopcion=?");
        $stmt->execute([$id]);
        $id_animal = $stmt->fetchColumn();
        
        
        $pdo->prepare("UPDATE adopciones SET estado='Rechazada' WHERE id_adopcion=?")->execute([$id]);

        
        $pdo->prepare("UPDATE animales SET estado='Disponible' WHERE id_animal=?")->execute([$id_animal]);

        
        $pdo->prepare("DELETE FROM adopciones_pagos WHERE id_adopcion = ?")->execute([$id]);

        $_SESSION['exito'] = "Adopción rechazada. El animal vuelve a estar disponible.";
        header('Location: adopciones.php');
        exit();
    }
}


$stmt = $pdo->query("
    SELECT a.*, u.nombre as usuario_nombre, u.email, 
           an.nombre as animal_nombre, an.imagen_url, c.nombre as centro_nombre
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
            <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['exito'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <h2><i class="fas fa-heart me-2"></i>Gestionar Adopciones</h2>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Animal</th>
                        <th>Centro</th>
                        <th>Fecha Solicitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adopciones as $a): ?>
                    <tr>
                        <td><?= $a['id_adopcion'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($a['usuario_nombre']) ?></strong><br>
                            <small><?= htmlspecialchars($a['email']) ?></small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($a['imagen_url']): ?>
                                    <img src="../<?= ltrim($a['imagen_url'], './') ?>" 
                                         alt="<?= htmlspecialchars($a['animal_nombre']) ?>" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                                <?php endif; ?>
                                <?= htmlspecialchars($a['animal_nombre']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($a['centro_nombre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($a['fecha_solicitud'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $a['estado'] == 'Pendiente' ? 'warning' : ($a['estado'] == 'Aprobada' ? 'success' : 'danger') ?>">
                                <?= $a['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="adopciones_ver.php?id=<?= $a['id_adopcion'] ?>" 
                               class="btn btn-sm btn-info mb-1" 
                               title="Ver detalles">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <?php if ($a['estado'] === 'Pendiente'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_adopcion" value="<?= $a['id_adopcion'] ?>">
                                    <button name="accion" value="aprobar" class="btn btn-sm btn-success mb-1">
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                    <button name="accion" value="rechazar" class="btn btn-sm btn-danger mb-1" 
                                            onclick="return confirm('¿Seguro que deseas rechazar esta adopción?')">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Finalizada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>