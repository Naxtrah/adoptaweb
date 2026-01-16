<?php
require_once '../includes/config.php';
require_once '../includes/paypal.php';

if (!estaLogueado() || !isset($_SESSION['pending_payment'])) {
    header('Location: ' . BASE_URL . '/');
    exit();
}

$paypal = new PayPalPayment(true);
$payment_info = $_SESSION['pending_payment'];
$success = false;
$mensaje = 'Error desconocido al procesar el pago';
$numero_factura = null;

if (isset($_GET['token'])) {
    $capture = $paypal->capturePayment($_GET['token']);

    if (isset($capture['status']) && $capture['status'] === 'COMPLETED') {
        $id_usuario = $_SESSION['user_id'];
        $monto = $payment_info['amount'];
        $concepto = $payment_info['description'];
        $id_centro = null;

        if ($payment_info['tipo'] === 'adopcion' && !empty($payment_info['id_adopcion'])) {
            $stmt = $pdo->prepare("
                SELECT an.id_centro 
                FROM adopciones a 
                JOIN animales an ON a.id_animal = an.id_animal 
                WHERE a.id_adopcion = ?
            ");
            $stmt->execute([$payment_info['id_adopcion']]);
            $adopcion = $stmt->fetch();
            $id_centro = $adopcion['id_centro'] ?? null;
        }

        $stmt = $pdo->prepare("
            INSERT INTO pagos (id_usuario, id_centro, monto, concepto, metodo_pago, fecha_pago) 
            VALUES (?, ?, ?, ?, 'PayPal', NOW())
        ");
        $stmt->execute([$id_usuario, $id_centro, $monto, $concepto]);
        $id_pago = $pdo->lastInsertId();

        $numero_factura = 'FAC-' . date('Y') . '-' . str_pad($id_pago, 5, '0', STR_PAD_LEFT');
        $stmt = $pdo->prepare("INSERT INTO facturas (id_pago, numero_factura, fecha_emision) VALUES (?, ?, NOW())");
        $stmt->execute([$id_pago, $numero_factura]);

        $success = true;
        $mensaje = 'Pago completado correctamente';
        unset($_SESSION['pending_payment']);
    } else {
        $mensaje = 'Error al procesar el pago con PayPal';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Completado - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-<?= $success ? 'success' : 'danger' ?> text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-<?= $success ? 'check-circle' : 'times-circle' ?> me-2"></i>
                            <?= $success ? 'Pago Completado' : 'Error en el Pago' ?>
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="rounded-circle bg-<?= $success ? 'success' : 'danger' ?> d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-<?= $success ? 'check' : 'times' ?> fa-2x text-white"></i>
                            </div>
                            <h5 class="text-<?= $success ? 'success' : 'danger' ?>"><?= $success ? '¡Pago realizado con éxito!' : 'Error en el pago' ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($mensaje) ?></p>
                        </div>

                        <?php if ($success): ?>
                            <div class="border rounded p-3 mb-4">
                                <h6>Resumen del pago</h6>
                                <p class="mb-1"><strong>Número de factura:</strong> <?= htmlspecialchars($numero_factura) ?></p>
                                <p class="mb-1"><strong>Importe:</strong> <?= number_format($payment_info['amount'], 2) ?> €</p>
                                <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y H:i') ?></p>
                                <p class="mb-0"><strong>Método:</strong> PayPal</p>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>/perfil/mis-pagos.php" class="btn btn-success">Ver mis pagos</a>
                                <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary">Volver al inicio</a>
                            </div>
                        <?php else: ?>
                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>/pagos/realizar.php" class="btn btn-warning">Intentar de nuevo</a>
                                <a href="<?= BASE_URL ?>/perfil/" class="btn btn-outline-secondary">Volver al perfil</a>
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