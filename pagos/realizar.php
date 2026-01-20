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
    SELECT ap.*, a.id_adopcion, an.id_animal, an.nombre AS animal_nombre
    FROM adopciones_pagos ap
    JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
    JOIN animales an ON a.id_animal = an.id_animal
    WHERE ap.token_pago = ? AND ap.estado = 'Pendiente'
");
$stmt->execute([$token]);
$pago = $stmt->fetch();

if (!$pago) {
    $_SESSION['error'] = "Pago no encontrado o ya procesado";
    header('Location: ' . BASE_URL . '/perfil/pagos-pendientes.php');
    exit();
}

if ($pago['id_usuario'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "No tienes permiso para acceder a este pago";
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

$vacunas = $pdo->prepare("
    SELECT v.id_vacuna, v.nombre, v.precio 
    FROM animal_vacunas av 
    JOIN vacunas v ON av.id_vacuna = v.id_vacuna 
    WHERE av.id_animal = ?
");
$vacunas->execute([$pago['id_animal']]);
$vacunas_lista = $vacunas->fetchAll();
$total = array_sum(array_column($vacunas_lista, 'precio'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_pago'])) {
    $metodo = $_POST['metodo_pago'] ?? '';
    $valido = true;
    $errores = [];

    switch ($metodo) {
        case 'tarjeta':
            $numero = str_replace(' ', '', $_POST['tarjeta_numero'] ?? '');
            $fecha = $_POST['tarjeta_fecha'] ?? '';
            $cvv = $_POST['tarjeta_cvv'] ?? '';
            $titular = $_POST['tarjeta_titular'] ?? '';
            
            if (empty($numero) || !preg_match('/^\d{16}$/', $numero)) {
                $errores[] = 'Número de tarjeta inválido (debe tener 16 dígitos)';
                $valido = false;
            }
            
            if (empty($fecha) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $fecha)) {
                $errores[] = 'Formato de fecha inválido (MM/YY)';
                $valido = false;
            } else {
                list($mes, $ano) = explode('/', $fecha);
                $ano_completo = 2000 + intval($ano);
                $fecha_actual = new DateTime();
                $fecha_expiracion = DateTime::createFromFormat('Y-m-d', $ano_completo . '-' . $mes . '-01');
                $fecha_expiracion->modify('last day of this month');
                
                if ($fecha_expiracion < $fecha_actual) {
                    $errores[] = 'La tarjeta está caducada';
                    $valido = false;
                }
            }
            
            if (empty($cvv) || !preg_match('/^\d{3,4}$/', $cvv)) {
                $errores[] = 'CVV inválido (debe tener 3-4 dígitos)';
                $valido = false;
            }
            
            if (empty($titular)) {
                $errores[] = 'Nombre del titular es requerido';
                $valido = false;
            }
            break;
            
        case 'paypal':
            $email = $_POST['paypal_email'] ?? '';
            $password = $_POST['paypal_password'] ?? '';
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'Email de PayPal inválido';
                $valido = false;
            }
            
            if (empty($password) || strlen($password) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres';
                $valido = false;
            }
            break;
            
        case 'transferencia':
            $cuenta = str_replace(' ', '', $_POST['transferencia_cuenta'] ?? '');
            $titular_cuenta = $_POST['transferencia_titular'] ?? '';
            
            if (empty($cuenta)) {
                $errores[] = 'Número de cuenta es requerido';
                $valido = false;
            } elseif (!preg_match('/^[A-Z]{2}\d{22}$/', $cuenta)) {
                $errores[] = 'Número de cuenta inválido (formato: ESXX XXXX XXXX XXXX XXXX XXXX)';
                $valido = false;
            }
            
            if (empty($titular_cuenta)) {
                $errores[] = 'Titular de la cuenta es requerido';
                $valido = false;
            }
            break;
            
        default:
            $errores[] = 'Método de pago no válido';
            $valido = false;
    }

    if ($valido) {
        $exito = mt_rand(1, 100) <= 80;
        
        if ($exito) {
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("UPDATE adopciones_pagos SET estado = 'Pagado', fecha_pago = NOW(), monto = ? WHERE token_pago = ?");
                $stmt->execute([$total, $token]);

                $concepto = 'Vacunas de ' . $pago['animal_nombre'];

                $stmt = $pdo->prepare("
                    INSERT INTO pagos (id_usuario, id_centro, monto, concepto, metodo_pago, fecha_pago)
                    SELECT 
                        a.id_usuario,
                        an.id_centro,
                        ?,
                        ?,
                        ?,
                        NOW()
                    FROM adopciones_pagos ap
                    JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
                    JOIN animales an ON a.id_animal = an.id_animal
                    WHERE ap.token_pago = ?
                ");
                $stmt->execute([$total, $concepto, ucfirst($metodo), $token]);
                $id_pago = $pdo->lastInsertId();

                foreach ($vacunas_lista as $v) {
                    $stmt = $pdo->prepare("INSERT INTO pagos_detalle (id_pago, id_animal, id_vacuna, cantidad, precio_unitario) VALUES (?, ?, ?, 1, ?)");
                    $stmt->execute([$id_pago, $pago['id_animal'], $v['id_vacuna'], $v['precio']]);
                }

                $stmt = $pdo->prepare("UPDATE animales SET estado = 'Adoptado' WHERE id_animal = ?");
                $stmt->execute([$pago['id_animal']]);

                $numero_factura = 'FAC-' . date('Y') . '-' . str_pad($id_pago, 5, '0', STR_PAD_LEFT);
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
                $stmt->execute([$id_pago, $numero_factura, $numero_factura . '.pdf']);

                $pdo->commit();

                header('Location: ' . BASE_URL . '/pagos/completado.php?token=' . $token);
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                $errores[] = 'Error al procesar el pago: ' . $e->getMessage();
                $valido = false;
            }
        } else {
            header('Location: ' . BASE_URL . '/pagos/cancelado.php?token=' . $token . '&error=simulado');
            exit();
        }
    }
    
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
    }
}

$metodo_actual = $_POST['metodo_pago'] ?? 'tarjeta';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Realizar Pago - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
    <script>
        function formatCardNumber(input) {
            var value = input.value.replace(/\D/g, '');
            var formatted = '';
            for (var i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            input.value = formatted.substring(0, 19);
        }
        
        function formatExpiryDate(input) {
            var value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                input.value = value.substring(0,2) + '/' + value.substring(2,4);
            } else {
                input.value = value;
            }
        }
        
        function formatAccountNumber(input) {
            var value = input.value.replace(/\D/g, '');
            var formatted = '';
            for (var i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            input.value = formatted.substring(0, 27);
        }
        
        function showPaymentMethod(method) {
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
                form.querySelectorAll('[required]').forEach(field => {
                    field.removeAttribute('required');
                });
            });
            
            const formToShow = document.getElementById(method + '-form');
            if (formToShow) {
                formToShow.style.display = 'block';
                formToShow.querySelectorAll('input').forEach(field => {
                    field.setAttribute('required', 'required');
                });
            }
        }
        
        function validateForm() {
            const metodo = document.querySelector('input[name="metodo_pago"]:checked').value;
            let valido = true;
            const errores = [];
            
            switch(metodo) {
                case 'tarjeta':
                    const tarjetaNumero = document.getElementById('tarjeta_numero').value.replace(/\s/g, '');
                    const tarjetaFecha = document.getElementById('tarjeta_fecha').value;
                    const tarjetaCvv = document.getElementById('tarjeta_cvv').value;
                    const tarjetaTitular = document.getElementById('tarjeta_titular').value;
                    
                    if (!/^\d{16}$/.test(tarjetaNumero)) {
                        errores.push('Número de tarjeta debe tener 16 dígitos');
                        valido = false;
                    }
                    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(tarjetaFecha)) {
                        errores.push('Fecha debe tener formato MM/YY');
                        valido = false;
                    }
                    if (!/^\d{3,4}$/.test(tarjetaCvv)) {
                        errores.push('CVV debe tener 3-4 dígitos');
                        valido = false;
                    }
                    if (!tarjetaTitular.trim()) {
                        errores.push('Nombre del titular es requerido');
                        valido = false;
                    }
                    break;
                    
                case 'paypal':
                    const paypalEmail = document.getElementById('paypal_email').value;
                    const paypalPassword = document.getElementById('paypal_password').value;
                    
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(paypalEmail)) {
                        errores.push('Email de PayPal inválido');
                        valido = false;
                    }
                    if (paypalPassword.length < 6) {
                        errores.push('La contraseña debe tener al menos 6 caracteres');
                        valido = false;
                    }
                    break;
                    
                case 'transferencia':
                    const cuenta = document.getElementById('transferencia_cuenta').value.replace(/\s/g, '');
                    const titularCuenta = document.getElementById('transferencia_titular').value;
                    
                    if (!/^[A-Z]{2}\d{22}$/.test(cuenta)) {
                        errores.push('Número de cuenta inválido (formato: ESXX XXXX XXXX XXXX XXXX XXXX)');
                        valido = false;
                    }
                    if (!titularCuenta.trim()) {
                        errores.push('Titular de la cuenta es requerido');
                        valido = false;
                    }
                    break;
            }
            
            if (!valido && errores.length > 0) {
                alert('Errores de validación:\n' + errores.join('\n'));
                return false;
            }
            
            return true;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            showPaymentMethod('<?= $metodo_actual ?>');
            
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });
            
            document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    showPaymentMethod(this.value);
                });
            });
        });
    </script>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-credit-card me-2"></i>Realizar Pago</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h5>Resumen del pago</h5>
                        <p class="mb-1"><strong>Animal:</strong> <?= htmlspecialchars($pago['animal_nombre']) ?></p>
                        <p class="mb-1"><strong>Concepto:</strong> Vacunas de adopción</p>
                        <p class="mb-0"><strong>Total:</strong> <span class="fw-bold text-success"><?= number_format($total, 2) ?> €</span></p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" novalidate>
                        <div class="mb-4">
                            <h5 class="mb-3">Selecciona método de pago:</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check card border p-3 h-100 text-center">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="tarjeta" value="tarjeta" 
                                               <?= $metodo_actual === 'tarjeta' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tarjeta">
                                            <i class="fas fa-credit-card fa-2x mb-2 text-primary"></i><br>
                                            Tarjeta de crédito
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card border p-3 h-100 text-center">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="paypal" value="paypal"
                                               <?= $metodo_actual === 'paypal' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal fa-2x mb-2 text-primary"></i><br>
                                            PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card border p-3 h-100 text-center">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="transferencia" value="transferencia"
                                               <?= $metodo_actual === 'transferencia' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="transferencia">
                                            <i class="fas fa-university fa-2x mb-2 text-primary"></i><br>
                                            Transferencia
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tarjeta-form" class="payment-form border rounded p-4 mb-4">
                            <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Datos de la tarjeta</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="tarjeta_titular" class="form-label">Nombre del titular *</label>
                                    <input type="text" class="form-control" id="tarjeta_titular" name="tarjeta_titular" 
                                           value="<?= htmlspecialchars($_POST['tarjeta_titular'] ?? '') ?>">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="tarjeta_numero" class="form-label">Número de tarjeta *</label>
                                    <input type="text" class="form-control" id="tarjeta_numero" name="tarjeta_numero" 
                                           maxlength="19" placeholder="1234 5678 9012 3456" 
                                           oninput="formatCardNumber(this)" 
                                           value="<?= htmlspecialchars($_POST['tarjeta_numero'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tarjeta_fecha" class="form-label">Fecha de expiración (MM/YY) *</label>
                                    <input type="text" class="form-control" id="tarjeta_fecha" name="tarjeta_fecha" 
                                           maxlength="5" placeholder="12/25" 
                                           oninput="formatExpiryDate(this)"
                                           value="<?= htmlspecialchars($_POST['tarjeta_fecha'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tarjeta_cvv" class="form-label">CVV *</label>
                                    <input type="text" class="form-control" id="tarjeta_cvv" name="tarjeta_cvv" 
                                           maxlength="4" placeholder="123" 
                                           value="<?= htmlspecialchars($_POST['tarjeta_cvv'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div id="paypal-form" class="payment-form border rounded p-4 mb-4" style="display: none;">
                            <h5 class="mb-3"><i class="fab fa-paypal me-2"></i>Iniciar sesión en PayPal</h5>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Esta es una simulación. No se realizará ningún cargo real.
                            </div>
                            <div class="mb-3">
                                <label for="paypal_email" class="form-label">Email de PayPal *</label>
                                <input type="email" class="form-control" id="paypal_email" name="paypal_email" 
                                       value="<?= htmlspecialchars($_POST['paypal_email'] ?? '') ?>" 
                                       placeholder="usuario@ejemplo.com">
                            </div>
                            <div class="mb-3">
                                <label for="paypal_password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="paypal_password" name="paypal_password">
                            </div>
                        </div>

                        <div id="transferencia-form" class="payment-form border rounded p-4 mb-4" style="display: none;">
                            <h5 class="mb-3"><i class="fas fa-university me-2"></i>Datos de transferencia</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Simulación: solo se valida el formato. No se realiza transferencia real.
                            </div>
                            <div class="mb-3">
                                <label for="transferencia_titular" class="form-label">Titular de la cuenta *</label>
                                    <input type="text" class="form-control" id="transferencia_titular" name="transferencia_titular" 
                                           value="<?= htmlspecialchars($_POST['transferencia_titular'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="transferencia_cuenta" class="form-label">Número de cuenta (IBAN) *</label>
                                <input type="text" class="form-control" id="transferencia_cuenta" name="transferencia_cuenta" 
                                       maxlength="27" placeholder="ESXX XXXX XXXX XXXX XXXX XXXX"
                                       oninput="formatAccountNumber(this)"
                                       value="<?= htmlspecialchars($_POST['transferencia_cuenta'] ?? '') ?>">
                                <small class="text-muted">Formato: ES00 0000 0000 0000 0000 0000</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="procesar_pago" class="btn btn-success btn-lg">
                                <i class="fas fa-lock me-2"></i>Pagar <?= number_format($total, 2) ?> €
                            </button>
                            <a href="<?= BASE_URL ?>/pagos/vacunas.php?token=<?= $token ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al resumen
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-shield-alt me-1"></i> Pago seguro SSL · 
                    <i class="fas fa-lock me-1"></i> Datos encriptados · 
                    <i class="fas fa-sim-card me-1"></i> Simulación de pago
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>