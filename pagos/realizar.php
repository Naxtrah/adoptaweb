<<<<<<< HEAD
<?php
require_once '../includes/config.php';
require_once '../includes/paypal.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$id_adopcion = isset($_GET['id_adopcion']) ? (int)$_GET['id_adopcion'] : 0;
$tipo = $_GET['tipo'] ?? 'adopcion'; //Puede ser adopción o donación

//Obtener información del pago
if ($tipo == 'adopcion' && $id_adopcion > 0) {
    $stmt = $pdo->prepare("
        SELECT a.*, an.nombre as animal_nombre, an.id_centro, c.nombre as centro_nombre
        FROM adopciones a
        JOIN animales an ON a.id_animal = an.id_animal
        JOIN centros c ON an.id_centro = c.id_centro
        WHERE a.id_adopcion = ? AND a.id_usuario = ?
    ");
    $stmt->execute([$id_adopcion, $_SESSION['user_id']]);
    $adopcion = $stmt->fetch();
    
    if (!$adopcion) {
        header('Location: ' . BASE_URL . '/perfil/');
        exit();
    }
    
    $amount = 150.00; //Tasa estándar de adopción
    $description = "Tasa de adopción de " . htmlspecialchars($adopcion['animal_nombre']);
} else {
    $amount = $_POST['amount'] ?? 0;
    $description = "Donación a AdoptaWeb";
}

// Procesar pago con PayPal
$paypal = new PayPalPayment(true); // true para sandbox

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['paypal'])) {
    $return_url = BASE_URL . '/pagos/completado.php';
    $cancel_url = BASE_URL . '/pagos/cancelado.php';
    
    $payment = $paypal->createPayment($amount, $description, $return_url, $cancel_url);
    
    if (isset($payment['id'])) {
        // Guardar información temporal del pago
        $_SESSION['pending_payment'] = [
            'payment_id' => $payment['id'],
            'amount' => $amount,
            'description' => $description,
            'id_adopcion' => $id_adopcion,
            'tipo' => $tipo
        ];
        
        // Redirigir a PayPal
        foreach ($payment['links'] as $link) {
            if ($link['rel'] == 'approve') {
                header('Location: ' . $link['href']);
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Realizar Pago
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-3x text-success mb-3"></i>
                            <h5>Pago Seguro</h5>
                            <p class="text-muted">Tu información de pago está protegida</p>
                        </div>
                        
                        <div class="payment-summary mb-4">
                            <h6>Resumen del pago:</h6>
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Concepto:</span>
                                    <strong><?= $description ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Importe:</span>
                                    <strong class="text-success"><?= number_format($amount, 2) ?> €</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>IVA:</span>
                                    <span>Incluido</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-methods">
                            <h6 class="mb-3">Métodos de pago:</h6>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="paypal" value="paypal" checked>
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-2 text-primary"></i>
                                            PayPal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" disabled>
                                        <label class="form-check-label text-muted" for="tarjeta">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Tarjeta de crédito (próximamente)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Serás redirigido a PayPal para completar el pago de forma segura.
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="paypal" class="btn btn-primary btn-lg">
                                        <i class="fab fa-paypal me-2"></i>
                                        Pagar con PayPal
                                    </button>
                                    <a href="<?= BASE_URL ?>/perfil/" class="btn btn-outline-secondary">
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
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
require_once '../includes/paypal.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$id_adopcion = isset($_GET['id_adopcion']) ? (int)$_GET['id_adopcion'] : 0;
$tipo = $_GET['tipo'] ?? 'adopcion'; // 'adopcion' o 'donacion'

// Obtener información del pago
if ($tipo == 'adopcion' && $id_adopcion > 0) {
    $stmt = $pdo->prepare("
        SELECT a.*, an.nombre as animal_nombre, an.id_centro, c.nombre as centro_nombre
        FROM adopciones a
        JOIN animales an ON a.id_animal = an.id_animal
        JOIN centros c ON an.id_centro = c.id_centro
        WHERE a.id_adopcion = ? AND a.id_usuario = ?
    ");
    $stmt->execute([$id_adopcion, $_SESSION['user_id']]);
    $adopcion = $stmt->fetch();
    
    if (!$adopcion) {
        header('Location: ' . BASE_URL . '/perfil/');
        exit();
    }
    
    $amount = 150.00; // Tasa estándar de adopción
    $description = "Tasa de adopción de " . htmlspecialchars($adopcion['animal_nombre']);
} else {
    $amount = $_POST['amount'] ?? 0;
    $description = "Donación a AdoptaWeb";
}

// Procesar pago con PayPal
$paypal = new PayPalPayment(true); // true para sandbox

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['paypal'])) {
    $return_url = BASE_URL . '/pagos/completado.php';
    $cancel_url = BASE_URL . '/pagos/cancelado.php';
    
    $payment = $paypal->createPayment($amount, $description, $return_url, $cancel_url);
    
    if (isset($payment['id'])) {
        // Guardar información temporal del pago
        $_SESSION['pending_payment'] = [
            'payment_id' => $payment['id'],
            'amount' => $amount,
            'description' => $description,
            'id_adopcion' => $id_adopcion,
            'tipo' => $tipo
        ];
        
        // Redirigir a PayPal
        foreach ($payment['links'] as $link) {
            if ($link['rel'] == 'approve') {
                header('Location: ' . $link['href']);
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pago - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Realizar Pago
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-3x text-success mb-3"></i>
                            <h5>Pago Seguro</h5>
                            <p class="text-muted">Tu información de pago está protegida</p>
                        </div>
                        
                        <div class="payment-summary mb-4">
                            <h6>Resumen del pago:</h6>
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Concepto:</span>
                                    <strong><?= $description ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Importe:</span>
                                    <strong class="text-success"><?= number_format($amount, 2) ?> €</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>IVA:</span>
                                    <span>Incluido</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-methods">
                            <h6 class="mb-3">Métodos de pago:</h6>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="paypal" value="paypal" checked>
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-2 text-primary"></i>
                                            PayPal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" disabled>
                                        <label class="form-check-label text-muted" for="tarjeta">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Tarjeta de crédito (próximamente)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Serás redirigido a PayPal para completar el pago de forma segura.
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="paypal" class="btn btn-primary btn-lg">
                                        <i class="fab fa-paypal me-2"></i>
                                        Pagar con PayPal
                                    </button>
                                    <a href="<?= BASE_URL ?>/perfil/" class="btn btn-outline-secondary">
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
</html>