<?php
require_once '../includes/config.php';
//Manejo errores
$error = '';
$success = '';
//Verificación en post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar datos
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $apellido = sanitizar($_POST['apellido'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $direccion = sanitizar($_POST['direccion'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    //Validaciones de todos los datos del formulario
    if (empty($nombre)) {
        $error = 'El nombre es obligatorio';
    } elseif (empty($email)) {
        $error = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido';
    } elseif (empty($password)) {
        $error = 'La contraseña es obligatoria';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        try {
            //Verificar si el email ya existe con una query y manejar si el correo ya existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Este email ya está registrado';
            } else {
                //Seguridad contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                //Insertar nuevo usuario
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nombre, apellido, email, password_hash, telefono, direccion, id_rol, fecha_registro) 
                    VALUES (?, ?, ?, ?, ?, ?, 2, NOW())
                ");
                $stmt->execute([$nombre, $apellido, $email, $password_hash, $telefono, $direccion]);
                //Obtener id del nuevo usuario
                $user_id = $pdo->lastInsertId();
                //Inicio de sesión automáticamente
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_rol'] = 2;
                $_SESSION['usuario_id'] = $user_id;
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_rol'] = 2;
                //Mensaje bienvenida
                $success = '¡Registro exitoso! Bienvenido/a a AdoptaWeb.';
                //Redirigir después de 3 segundos
                header("refresh:3;url=" . BASE_URL . "/perfil/");
            }
        } catch (PDOException $e) {
            $error = 'Error en el registro: ' . $e->getMessage();
        }
    }
}
//Si ya está logueado, redirigir
if (estaLogueado()) {
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <style>
        .register-card {
            margin-top: 2rem;
            margin-bottom: 3rem;
        }
        .form-icon {
            color: #28a745;
        }
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .progress-bar {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>  
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow register-card">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($success) ?>
                                <p class="mb-0 mt-2">Serás redirigido a tu perfil en 3 segundos...</p>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="" id="registerForm">
                            <div class="row">
                                <!--Información personal-->
                                <div class="col-md-6">
                                    <h5 class="mb-4 text-success">
                                        <i class="fas fa-user-circle me-2"></i>Información Personal
                                    </h5>
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">
                                            <i class="fas fa-user form-icon me-1"></i>Nombre *
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nombre" 
                                               name="nombre"
                                               value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>"
                                               required
                                               placeholder="Tu nombre">
                                    </div>
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">
                                            <i class="fas fa-user form-icon me-1"></i>Primer apellido
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="apellido" 
                                               name="apellido"
                                               value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>"
                                               placeholder="Tu apellido">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope form-icon me-1"></i>Email *
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email"
                                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                               required
                                               placeholder="ejemplo@email.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">
                                            <i class="fas fa-phone form-icon me-1"></i>Teléfono
                                        </label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="telefono" 
                                               name="telefono"
                                               value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>"
                                               placeholder="+34 600 123 456">
                                    </div>
                                </div>
                                <!--Dirección y contraseña-->
                                <div class="col-md-6">
                                    <h5 class="mb-4 text-success">
                                        <i class="fas fa-home me-2"></i>Dirección & Seguridad
                                    </h5>
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label">
                                            <i class="fas fa-map-marker-alt form-icon me-1"></i>Dirección
                                        </label>
                                        <textarea class="form-control" 
                                                  id="direccion" 
                                                  name="direccion"
                                                  rows="2"
                                                  placeholder="Calle, número, ciudad, código postal"><?= isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : '' ?></textarea>
                                        <small class="text-muted">Necesaria para procesos de adopción</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock form-icon me-1"></i>Contraseña *
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password"
                                               required
                                               placeholder="Mínimo 6 caracteres"
                                               minlength="6">
                                        <div class="password-requirements mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Mínimo 6 caracteres
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label">
                                            <i class="fas fa-lock form-icon me-1"></i>Confirmar Contraseña *
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="confirm_password"
                                               required
                                               placeholder="Repite tu contraseña">
                                    </div>
                                </div>
                            </div>
                            <!--Términos y condiciones-->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terminos" required>
                                    <label class="form-check-label" for="terminos">
                                        Acepto los 
                                        <a href="<?= BASE_URL ?>/terminos" target="_blank" class="text-success">términos y condiciones</a> 
                                        y la 
                                        <a href="<?= BASE_URL ?>/privacidad" target="_blank" class="text-success">política de privacidad</a>
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="newsletter">
                                    <label class="form-check-label" for="newsletter">
                                        Deseo recibir noticias sobre adopciones y consejos para mascotas
                                    </label>
                                </div>
                            </div>
                            <!--Botones-->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg py-3">
                                    <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                                </button>
                                <div class="text-center mt-3">
                                    <p class="mb-2">¿Ya tienes cuenta?</p>
                                    <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-outline-success">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    //Validación de contraseña en tiempo real
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const requirements = document.querySelector('.password-requirements');  
        if (password.length < 6) {
            requirements.innerHTML = '<i class="fas fa-times text-danger me-1"></i> Mínimo 6 caracteres (actual: ' + password.length + ')';
            requirements.classList.remove('text-success');
            requirements.classList.add('text-danger');
        } else {
            requirements.innerHTML = '<i class="fas fa-check text-success me-1"></i> Contraseña válida (' + password.length + ' caracteres)';
            requirements.classList.remove('text-danger');
            requirements.classList.add('text-success');
        }
    });
    //Validación de confirmación de contraseña
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;  
        if (confirmPassword !== '' && password !== confirmPassword) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (confirmPassword !== '') {
            this.classList.add('is-valid');
            this.classList.remove('is-invalid');
        }
    });
    //Validación del formulario antes de enviar
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const terminos = document.getElementById('terminos').checked;  
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            document.getElementById('confirm_password').focus();
            return false;
        }
        if (!terminos) {
            e.preventDefault();
            alert('Debes aceptar los términos y condiciones');
            document.getElementById('terminos').focus();
            return false;
        }
        return true;
    });
    </script>
</body>
</html>