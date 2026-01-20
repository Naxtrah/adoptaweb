<?php
require_once '../includes/config.php';
if (!estaLogueado()) redirect(BASE_URL.'/auth/login.php');

$token = $_GET['token'] ?? '';
$error = $_GET['error'] ?? '';


$mensajes = [
    'simulado' => 'Error simulado del sistema de pago. Esta es una prueba controlada.',
    'fondos' => 'Fondos insuficientes en la cuenta.',
    'tarjeta' => 'La tarjeta ha sido rechazada.',
    'timeout' => 'Tiempo de espera agotado. Intenta de nuevo.',
    'default' => 'El pago no se ha podido procesar. Por favor, intenta de nuevo.'
];

$mensaje = $mensajes[$error] ?? $mensajes['default'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Cancelado - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Pago No Procesado</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="rounded-circle bg-danger d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-times fa-3x text-white"></i>
                            </div>
                            <h3 class="text-danger">¡Ups!</h3>
                            <p class="lead">Ha ocurrido un problema con tu pago.</p>
                        </div>
                        
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-info-circle me-2"></i>Detalles del error:</h5>
                            <p class="mb-0"><?= htmlspecialchars($mensaje) ?></p>
                            <?php if ($error === 'simulado'): ?>
                                <div class="mt-2 small">
                                    <i class="fas fa-code me-1"></i> Esta es una simulación controlada del sistema.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="border rounded p-3 mb-4">
                            <h6>¿Qué puedes hacer ahora?</h6>
                            <ul class="text-start">
                                <li>Verificar los datos de pago introducidos</li>
                                <li>Comprobar que tu método de pago está activo</li>
                                <li>Intentar con otro método de pago</li>
                                <li>Contactar con tu banco si el problema persiste</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-3">
                            <?php if ($token): ?>
                                <a href="<?= BASE_URL ?>/pagos/realizar.php?token=<?= $token ?>" class="btn btn-warning btn-lg">
                                    <i class="fas fa-redo me-2"></i>Intentar de nuevo
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?= BASE_URL ?>/pagos/vacunas.php?token=<?= $token ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al resumen
                            </a>
                            
                            <a href="<?= BASE_URL ?>/perfil/pagos-pendientes.php" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Ver pagos pendientes
                            </a>
                            
                            <a href="<?= BASE_URL ?>/contacto.php" class="btn btn-outline-danger">
                                <i class="fas fa-headset me-2"></i>Contactar con soporte
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>Si el problema persiste, contacta con nuestro equipo de soporte.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>