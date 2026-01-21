<?php
require_once '../includes/config.php';

//Redirigir usuarios ya logueados
if (estaLogueado()) {
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

//Variables para mensajes de formulario
$error = '';
$success = '';

//Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Obtener y limpiar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    //Array para acumular errores
    $errores = [];
    //Validaciones de todos los datos del formulario
    if (empty($nombre)) {
        $errores[] = 'El nombre es obligatorio';
    } elseif (strlen($nombre) < 2 || strlen($nombre) > 100) {
        $errores[] = 'El nombre debe tener entre 2 y 100 caracteres';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
        $errores[] = 'El nombre solo puede contener letras y espacios';
    }
    
    if (!empty($apellido) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido)) {
        $errores[] = 'El apellido solo puede contener letras y espacios';
    }
    
    if (empty($email)) {
        $errores[] = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no tiene un formato válido';
    } elseif (strlen($email) > 150) {
        $errores[] = 'El email no puede tener más de 150 caracteres';
    }
    
    if (!empty($telefono) && !preg_match('/^[0-9\s\+\-\(\)]{9,20}$/', $telefono)) {
        $errores[] = 'El teléfono no tiene un formato válido';
    }
    
    if (!empty($direccion) && strlen($direccion) > 255) {
        $errores[] = 'La dirección no puede tener más de 255 caracteres';
    }
    
    if (empty($password)) {
        $errores[] = 'La contraseña es obligatoria';
    } elseif (strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errores[] = 'La contraseña debe contener al menos una mayúscula';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errores[] = 'La contraseña debe contener al menos un número';
    } elseif ($password !== $confirm_password) {
        $errores[] = 'Las contraseñas no coinciden';
    }
    
    //Validación de términos y condiciones
    if (!isset($_POST['terminos'])) {
        $errores[] = 'Debes aceptar los términos y condiciones';
    }
    
    //Procesar registro si no hay errores
    if (empty($errores)) {
        try {
            //Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Este email ya está registrado';
            } else {
                //Hashear contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                //Insertar nuevo usuario
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (nombre, apellido, email, password_hash, telefono, direccion, id_rol, fecha_registro) 
                    VALUES (?, ?, ?, ?, ?, ?, 2, NOW())
                ");
                $stmt->execute([$nombre, $apellido, $email, $password_hash, $telefono, $direccion]);
                
                //Obtener ID del nuevo usuario
                $user_id = $pdo->lastInsertId();
                
                //Iniciar sesión automáticamente
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $nombre;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 2;
                
                //Suscripción opcional al newsletter
                if (isset($_POST['newsletter'])) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO newsletter (email, fecha_registro, activo) VALUES (?, NOW(), 1)");
                        $stmt->execute([$email]);
                    } catch (PDOException $e) {
                        //Log de error sin interrumpir el registro
                        error_log("Error al suscribir a newsletter: " . $e->getMessage());
                    }
                }
                
                $success = '¡Registro exitoso! Bienvenido/a a AdoptaWeb. Serás redirigido a tu perfil...';
                
                //Redirigir después de 3 segundos
                header("refresh:3;url=" . BASE_URL . "/perfil/");
            }
        } catch (PDOException $e) {
            error_log("Error en registro: " . $e->getMessage());
            $error = 'Error en el registro. Por favor, inténtalo de nuevo más tarde.';
        }
    } else {
        $error = implode('<br>', $errores);
    }
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
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
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
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
                        <!--Mostrar mensajes de error-->
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!--Mostrar mensaje de éxito-->
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <p class="mb-0 mt-2">Serás redirigido a tu perfil en 3 segundos...</p>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="registerForm" novalidate>
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
                                               pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}"
                                               placeholder="Tu nombre">
                                        <div class="invalid-feedback">
                                            El nombre debe tener entre 2 y 100 letras
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">
                                            <i class="fas fa-user form-icon me-1"></i>Apellido
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="apellido" 
                                               name="apellido"
                                               value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>"
                                               pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*"
                                               placeholder="Tu apellido">
                                        <div class="invalid-feedback">
                                            Solo se permiten letras y espacios
                                        </div>
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
                                               maxlength="150"
                                               placeholder="ejemplo@email.com">
                                        <div class="invalid-feedback">
                                            Por favor, introduce un email válido
                                        </div>
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
                                               pattern="[0-9\s\+\-\(\)]{9,20}"
                                               placeholder="+34 600 123 456">
                                        <div class="invalid-feedback">
                                            Formato de teléfono no válido
                                        </div>
                                    </div>
                                </div>
                                
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
                                                  maxlength="255"
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
                                               minlength="6"
                                               placeholder="Mínimo 6 caracteres con mayúscula y número">
                                        <div class="password-strength" id="passwordStrength"></div>
                                        <div class="password-requirements mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Mínimo 6 caracteres, una mayúscula y un número
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
                                        <div class="invalid-feedback" id="passwordMatchFeedback">
                                            Las contraseñas no coinciden
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!--Checkboxes de términos y newsletter-->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
                                    <label class="form-check-label" for="terminos">
                                        Acepto los 
                                        <a href="<?= BASE_URL ?>/terminos.php" target="_blank" class="text-success">términos y condiciones</a> 
                                        y la 
                                        <a href="<?= BASE_URL ?>/privacidad.php" target="_blank" class="text-success">política de privacidad</a>
                                    </label>
                                    <div class="invalid-feedback">
                                        Debes aceptar los términos y condiciones
                                    </div>
                                </div>
                                
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                    <label class="form-check-label" for="newsletter">
                                        Deseo recibir noticias sobre adopciones y consejos para mascotas
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg py-3">
                                    <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                                </button>
                                
                                <!--Enlace a login para usuarios existentes-->
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
    <!--Scripts de Bootstrap y validación personalizada-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //Indicador visual de fortaleza de contraseña
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const requirements = document.querySelector('.password-requirements');
            
            let strength = 0;
            if (password.length >= 6) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            //Cambiar color según fortaleza
            if (strength < 50) {
                strengthBar.style.backgroundColor = '#dc3545';
                requirements.innerHTML = '<i class="fas fa-times text-danger me-1"></i> Contraseña débil';
            } else if (strength < 75) {
                strengthBar.style.backgroundColor = '#ffc107';
                requirements.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i> Contraseña media';
            } else {
                strengthBar.style.backgroundColor = '#28a745';
                requirements.innerHTML = '<i class="fas fa-check text-success me-1"></i> Contraseña fuerte';
            }
            
            //Validar coincidencia si se ha ingresado confirmación
            const confirmPassword = document.getElementById('confirm_password').value;
            if (confirmPassword !== '') {
                validatePasswordMatch();
            }
        });
        
        //Validar coincidencia de contraseñas en tiempo real
        document.getElementById('confirm_password').addEventListener('input', validatePasswordMatch);
        
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const feedback = document.getElementById('passwordMatchFeedback');
            
            if (confirmPassword !== '' && password !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('is-invalid');
                document.getElementById('confirm_password').classList.remove('is-valid');
            } else if (confirmPassword !== '') {
                document.getElementById('confirm_password').classList.add('is-valid');
                document.getElementById('confirm_password').classList.remove('is-invalid');
            }
        }
        
        //Validación adicional antes de enviar formulario
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            //Verificar requisitos de contraseña
            if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                e.preventDefault();
                alert('La contraseña debe contener al menos una mayúscula y un número');
                document.getElementById('password').focus();
                return false;
            }
            
            //Verificar coincidencia de contraseñas
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                document.getElementById('confirm_password').focus();
                return false;
            }
            
            return true;
        });
        
        //Validación nativa de Bootstrap
        (function() {
            'use strict'
            var forms = document.querySelectorAll('#registerForm')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>