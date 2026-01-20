<?php
require_once '../includes/config.php';
session_start();
unset($_SESSION['pending_payment']);
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
                <div class="card shadow text-center">
                    <div class="card-header bg-warning text-white">
                        <h4><i class="fas fa-exclamation-triangle me-2"></i>Pago Cancelado</h4>
                    </div>
                    <div class="card-body">
                        <p>Has cancelado el pago. Puedes intentarlo de nuevo cuando quieras.</p>
                        <a href="<?= BASE_URL ?>/perfil/" class="btn btn-outline-secondary">Volver al perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>