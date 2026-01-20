<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();

//Obtener pagos del usuario
$stmt = $pdo->prepare("
    SELECT p.*, f.numero_factura, c.nombre as centro_nombre
    FROM pagos p
    LEFT JOIN facturas f ON p.id_pago = f.id_pago
    LEFT JOIN centros c ON p.id_centro = c.id_centro
    WHERE p.id_usuario = ?
    ORDER BY p.fecha_pago DESC
");
$stmt->execute([$_SESSION['user_id']]);
$pagos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pagos - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        <h4 class="mb-0">Mis Pagos</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($pagos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nº Factura</th>
                                            <th>Concepto</th>
                                            <th>Centro</th>
                                            <th>Importe</th>
                                            <th>Fecha</th>
                                            <th>Método</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?= $pago['numero_factura'] ?? 'Pendiente' ?></td>
                                            <td><?= htmlspecialchars($pago['concepto']) ?></td>
                                            <td><?= htmlspecialchars($pago['centro_nombre'] ?? 'General') ?></td>
                                            <td class="text-success fw-bold"><?= number_format($pago['monto'], 2) ?> €</td>
                                            <td><?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= $pago['metodo_pago'] ?></span>
                                            </td>
                                            <td>
                                                <?php if ($pago['numero_factura']): ?>
                                                <button class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-download"></i> Factura
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No tienes pagos registrados</h5>
                                <p class="mb-4">Cuando realices un pago, aparecerá aquí.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>