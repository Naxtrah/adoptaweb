<?php
require_once '../includes/config.php';
if (!estaLogueado()) redirect(BASE_URL.'/auth/login.php');

$token = $_GET['token'] ?? '';
if (!$token) {
    $_SESSION['error'] = "Token de pago no válido";
    header('Location: ' . BASE_URL . '/perfil/pagos-pendientes.php');
    exit();
}


$stmt = $pdo->prepare("
    SELECT 
        ap.*,
        f.numero_factura,
        f.fecha_emision,
        f.pdf_dirr,
        an.nombre AS animal_nombre,
        a.id_adopcion,
        p.id_pago,
        p.monto,
        p.fecha_pago AS fecha_pago_real
    FROM adopciones_pagos ap
    JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
    JOIN animales an ON a.id_animal = an.id_animal
    LEFT JOIN pagos p ON p.id_usuario = ap.id_usuario 
        AND p.fecha_pago = (
            SELECT MAX(fecha_pago) 
            FROM pagos 
            WHERE id_usuario = ap.id_usuario 
            AND DATE(fecha_pago) = DATE(ap.fecha_pago)
        )
    LEFT JOIN facturas f ON f.id_pago = p.id_pago
    WHERE ap.token_pago = ? 
        AND ap.estado = 'Pagado' 
        AND ap.id_usuario = ?
    ORDER BY p.fecha_pago DESC
    LIMIT 1
");
$stmt->execute([$token, $_SESSION['user_id']]);
$pago = $stmt->fetch();

if (!$pago) {
    $_SESSION['error'] = "Pago no encontrado o no autorizado";
    header('Location: ' . BASE_URL . '/perfil/pagos-pendientes.php');
    exit();
}


$vacunas = $pdo->prepare("
    SELECT v.nombre, v.precio 
    FROM animal_vacunas av 
    JOIN vacunas v ON av.id_vacuna = v.id_vacuna 
    WHERE av.id_animal = ?
");
$vacunas->execute([$pago['id_animal']]);
$vacunas_lista = $vacunas->fetchAll();
$total = array_sum(array_column($vacunas_lista, 'precio'));


if (!$pago['numero_factura'] && $pago['id_pago']) {
    $concepto = 'Vacunas de ' . $pago['animal_nombre'];
    $numero_factura = 'FAC-' . date('Y') . '-' . str_pad($pago['id_pago'], 5, '0', STR_PAD_LEFT);
    $pdfDir = __DIR__ . '/../facturas';
    if (!is_dir($pdfDir)) mkdir($pdfDir, 0755, true);
    $pdfFile = $pdfDir . '/' . $numero_factura . '.pdf';

    require_once __DIR__ . '/../includes/pdf.php';
    $datos = [
        'numero' => $numero_factura,
        'fecha' => date('d/m/Y'),
        'cliente' => $_SESSION['user_name'] ?? 'Usuario',
        'concepto' => $concepto,
        'monto' => number_format($total, 2),
        'vacunas' => $vacunas_lista
    ];
    
    generarPDFFactura($datos, $pdfFile);

    $stmt = $pdo->prepare("INSERT INTO facturas (id_pago, numero_factura, fecha_emision, pdf_dirr) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$pago['id_pago'], $numero_factura, $numero_factura . '.pdf']);
    
   
    $pago['numero_factura'] = $numero_factura;
    $pago['pdf_dirr'] = $numero_factura . '.pdf';
    $pago['fecha_emision'] = date('Y-m-d H:i:s');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Completado - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>¡Pago Completado con Éxito!</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-5">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-check fa-3x text-white"></i>
                        </div>
                        <h3 class="text-success">¡Gracias por tu adopción!</h3>
                        <p class="lead">Tu pago ha sido procesado correctamente y la adopción está ahora completa.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-paw me-2"></i>Resumen de la adopción</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Animal adoptado:</strong> <?= htmlspecialchars($pago['animal_nombre']) ?></p>
                                    <p><strong>Fecha de pago:</strong> <?= date('d/m/Y H:i', strtotime($pago['fecha_pago_real'] ?? $pago['fecha_pago'])) ?></p>
                                    <p><strong>Número de adopción:</strong> ADOP-<?= str_pad($pago['id_adopcion'], 4, '0', STR_PAD_LEFT) ?></p>
                                    <hr>
                                    <h6>Vacunas incluidas:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($vacunas_lista as $v): ?>
                                            <li>• <?= htmlspecialchars($v['nombre']) ?> - <?= number_format($v['precio'], 2) ?> €</li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p class="fw-bold mt-3">Total pagado: <span class="text-success"><?= number_format($total, 2) ?> €</span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Documentación</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($pago['numero_factura']): ?>
                                        <p><strong>Factura:</strong> <?= htmlspecialchars($pago['numero_factura']) ?></p>
                                        <p><strong>Fecha de emisión:</strong> <?= date('d/m/Y', strtotime($pago['fecha_emision'])) ?></p>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Tu factura ha sido generada y está disponible para descarga.
                                        </div>
                                        
                                       <div class="d-grid gap-2">
                                        <?php if ($pago['pdf_dirr']): ?>
                                            <a href="<?= BASE_URL ?>/facturas/descargar.php?f=<?= urlencode($pago['pdf_dirr']) ?>" 
                                            class="btn btn-primary">
                                                <i class="fas fa-download me-2"></i>Descargar factura PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            La factura se está generando. Por favor, inténtalo de nuevo en unos minutos.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success mt-4">
                        <h5><i class="fas fa-star me-2"></i>¡Felicidades!</h5>
                        <p class="mb-0">Ahora eres el orgulloso dueño de <?= htmlspecialchars($pago['animal_nombre']) ?>. 
                        Recibirás un correo con todos los detalles de la adopción y los próximos pasos.</p>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="<?= BASE_URL ?>/perfil/mis-adopciones.php" class="btn btn-success btn-lg me-md-2">
                            <i class="fas fa-heart me-2"></i>Ver mis adopciones
                        </a>
                        <a href="<?= BASE_URL ?>/perfil/" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user me-2"></i>Ir a mi perfil
                        </a>
                        <a href="<?= BASE_URL ?>/animales/" class="btn btn-outline-success btn-lg ms-md-2">
                            <i class="fas fa-paw me-2"></i>Ver más animales
                        </a>
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    ¿Necesitas ayuda? <a href="<?= BASE_URL ?>/contacto.php">Contacta con nosotros</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>