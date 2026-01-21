<?php
require_once '../includes/config.php';
//Verificar autenticación del usuario
if (!estaLogueado()) redirect(BASE_URL.'/auth/login.php');

//Obtener token de pago desde URL
$token = $_GET['token'] ?? '';
if (!$token) {
    $_SESSION['error'] = "Token de pago no válido";
    header('Location: ' . BASE_URL . '/perfil/pagos-pendientes.php');
    exit();
}

//Consultar información del pago pendiente
$stmt = $pdo->prepare("
    SELECT ap.*, a.id_adopcion, an.id_animal, an.nombre AS animal_nombre
    FROM adopciones_pagos ap
    JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
    JOIN animales an ON a.id_animal = an.id_animal
    WHERE ap.token_pago = ? AND ap.estado = 'Pendiente'
");
$stmt->execute([$token]);
$pago = $stmt->fetch();

//Validar existencia del pago
if (!$pago) {
    $_SESSION['error'] = "Pago no encontrado o ya procesado";
    header('Location: ' . BASE_URL . '/perfil/pagos-pendientes.php');
    exit();
}

//Verificar permisos del usuario
if ($pago['id_usuario'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "No tienes permiso para acceder a este pago";
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

//Consultar vacunas del animal
$vacunas = $pdo->prepare("
    SELECT v.nombre, v.precio 
    FROM animal_vacunas av 
    JOIN vacunas v ON av.id_vacuna = v.id_vacuna 
    WHERE av.id_animal = ?
");
$vacunas->execute([$pago['id_animal']]);
$vacunas_lista = $vacunas->fetchAll();

//Calcular total de vacunas
$total_vacunas = array_sum(array_column($vacunas_lista, 'precio'));

$monto_a_pagar = $pago['monto'];

//Sincronizar monto si hay discrepancia
if ($monto_a_pagar != $total_vacunas) {
    $stmt = $pdo->prepare("UPDATE adopciones_pagos SET monto = ? WHERE token_pago = ?");
    $stmt->execute([$total_vacunas, $token]);
    $monto_a_pagar = $total_vacunas;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Pago - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4>Resumen de pago - <?= htmlspecialchars($pago['animal_nombre']) ?></h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Resumen de la adopción:</strong> Estás a punto de completar el pago de la adopción de <strong><?= htmlspecialchars($pago['animal_nombre']) ?></strong>.
                    </div>

                    <!--Detalles del pago-->
                    <div class="border rounded p-3 mb-4">
                        <h5 class="mb-3">Detalles del pago:</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Concepto:</span>
                            <strong>Vacunas de <?= htmlspecialchars($pago['animal_nombre']) ?></strong>
                        </div>
                        <!--Lista de vacunas-->
                        <div class="mb-3">
                            <span class="fw-bold">Vacunas incluidas:</span>
                            <ul class="list-unstyled ms-3 mt-2">
                                <?php foreach ($vacunas_lista as $v): ?>
                                    <li>• <?= htmlspecialchars($v['nombre']) ?> - <?= number_format($v['precio'], 2) ?> €</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <!--Total a pagar-->
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span class="fw-bold">Total a pagar:</span>
                            <strong class="text-success fs-5"><?= number_format($monto_a_pagar, 2) ?> €</strong>
                        </div>
                    </div>

                    <!--Botones de acción-->
                    <div class="d-grid">
                        <a href="<?= BASE_URL ?>/pagos/realizar.php?token=<?= $token ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Continuar al pago
                        </a>
                        <a href="<?= BASE_URL ?>/perfil/pagos-pendientes.php" class="btn btn-outline-secondary mt-2">
                            Cancelar
                        </a>
                    </div>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-info-circle me-1"></i> Serás redirigido a la plataforma de pago segura
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>