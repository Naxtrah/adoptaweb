<?php
require_once '../includes/config.php';
if (!estaLogueado()) redirect(BASE_URL.'/auth/login.php');

$user_id = $_SESSION['user_id'] ?? 0;

$pendientes   = [];
$errorMensaje = '';

try {
    $sql = "SELECT 
                ap.id,
                ap.monto,
                ap.token_pago,
                a.estado AS estado_adopcion,
                COALESCE(an.nombre,'Sin nombre') AS animal_nombre,
                COALESCE(c.nombre,'Centro no especificado') AS centro_nombre,
                an.id_animal
            FROM adopciones_pagos ap
            LEFT JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
            LEFT JOIN animales an ON a.id_animal = an.id_animal
            LEFT JOIN centros c ON an.id_centro = c.id_centro
            WHERE ap.id_usuario = ? AND ap.estado = 'Pendiente'
            ORDER BY ap.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

   
    foreach ($pendientes as &$pago) {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(v.precio), 0) as total_vacunas
            FROM animal_vacunas av 
            JOIN vacunas v ON av.id_vacuna = v.id_vacuna 
            WHERE av.id_animal = ?
        ");
        $stmt->execute([$pago['id_animal']]);
        $total_vacunas = $stmt->fetchColumn();
        
        
        if ($pago['monto'] != $total_vacunas) {
            $updateStmt = $pdo->prepare("UPDATE adopciones_pagos SET monto = ? WHERE id = ?");
            $updateStmt->execute([$total_vacunas, $pago['id']]);
            $pago['monto'] = $total_vacunas;
        }
    }
    unset($pago);

} catch (Exception $e) {
    $pendientes = [];
    $errorMensaje = $e->getMessage();
}

$numPendientes = count($pendientes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos Pendientes - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3"><?php include 'menu-lateral.php'; ?></div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Pagos pendientes de adopción</h4>
                </div>
                <div class="card-body">
                    <?php if ($errorMensaje): ?>
                        <div class="alert alert-danger"><strong>Error:</strong> <?= htmlspecialchars($errorMensaje) ?></div>
                    <?php endif; ?>

                    <?php if ($numPendientes > 0): ?>
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-info-circle me-2"></i>Tienes <strong><?= $numPendientes ?></strong> pago(s) pendiente(s)
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr><th>#</th><th>Animal</th><th>Centro</th><th>Monto</th><th>Estado adopción</th><th>Acciones</th></tr>
                                </thead>
                                <tbody>
<?php
$stmt = $pdo->prepare("
    SELECT 
        ap.id,
        ap.monto,
        ap.token_pago,
        a.estado AS estado_adopcion,
        COALESCE(an.nombre,'Sin nombre') AS animal_nombre,
        COALESCE(c.nombre,'Centro no especificado') AS centro_nombre,
        an.id_animal
    FROM adopciones_pagos ap
    JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
    LEFT JOIN animales an ON a.id_animal = an.id_animal
    LEFT JOIN centros c ON an.id_centro = c.id_centro
    WHERE ap.id_usuario = ? 
      AND ap.estado = 'Pendiente' 
      AND a.estado = 'Aprobada'
    ORDER BY ap.id DESC
");
$stmt->execute([$_SESSION['user_id']]);
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!is_array($filas)) $filas = [];

$html = '';
foreach ($filas as $i => $p) {
 
    $stmtVacunas = $pdo->prepare("
        SELECT COALESCE(SUM(v.precio), 0) as total_vacunas
        FROM animal_vacunas av 
        JOIN vacunas v ON av.id_vacuna = v.id_vacuna 
        WHERE av.id_animal = ?
    ");
    $stmtVacunas->execute([$p['id_animal']]);
    $monto_real = $stmtVacunas->fetchColumn();
    
    
    if ($p['monto'] != $monto_real) {
        $updateStmt = $pdo->prepare("UPDATE adopciones_pagos SET monto = ? WHERE id = ?");
        $updateStmt->execute([$monto_real, $p['id']]);
        $p['monto'] = $monto_real;
    }
    
    $html .= '
    <tr>
        <td>' . ($i + 1) . '</td>
        <td><strong>' . htmlspecialchars($p['animal_nombre']) . '</strong></td>
        <td>' . htmlspecialchars($p['centro_nombre']) . '</td>
        <td><span class="badge bg-success">' . number_format($p['monto'], 2) . ' €</span></td>
        <td><span class="badge bg-warning">' . htmlspecialchars($p['estado_adopcion']) . '</span></td>
        <td>
            <a href="' . BASE_URL . '/pagos/vacunas.php?token=' . $p['token_pago'] . '" class="btn btn-sm btn-primary">
                Pagar ahora
            </a>
        </td>
    </tr>';
}
?>
<?= $html ?>
                                </tbody>
                            </table>
                        </div>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">No tienes pagos pendientes</h5>
                            <p class="text-muted mb-4">Todas tus adopciones están al día.</p>
                            <a href="<?= BASE_URL ?>/animales/" class="btn btn-success">
                                <i class="fas fa-paw me-2"></i>Ver animales disponibles
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer bg-light">
                    <details>
                        <summary class="text-primary cursor-pointer"><i class="fas fa-code me-2"></i>Información técnica</summary>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6"><p class="mb-1 small"><strong>Usuario ID:</strong> <?= $user_id ?></p></div>
                                <div class="col-md-6"><p class="mb-1 small"><strong>Mostrados:</strong> <?= $numPendientes ?></p></div>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>