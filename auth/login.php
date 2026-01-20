<?php
require_once '../includes/config.php';


if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: ' . BASE_URL . '/admin/index.php');
    } else {
        header('Location: ' . BASE_URL . '/perfil/');
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

   
    if (empty($email) || empty($password)) {
        $error = "Por favor, completa todos los campos";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del email no es válido";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                
                if (password_verify($password, $user['password_hash'])) {
                    
                    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $updateStmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?");
                        $updateStmt->execute([$newHash, $user['id_usuario']]);
                    }

                   
                    $_SESSION['user_id'] = $user['id_usuario'];
                    $_SESSION['user_name'] = $user['nombre'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['id_rol'];

                    
                    $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '';
                    
                    if ($user['id_rol'] == 1) { 
                        header('Location: ' . BASE_URL . '/admin/index.php');
                        exit();
                    } elseif (!empty($redirect)) {
                        header('Location: ' . $redirect);
                        exit();
                    } else {
                        header('Location: ' . BASE_URL . '/perfil/');
                        exit();
                    }
                } else {
                    $error = "Contraseña incorrecta";
                }
            } else {
                $error = "No existe una cuenta con ese email";
            }
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = "Error interno del sistema. Por favor, inténtalo más tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/favicon.png" type="image/x-icon">
    <style>
        .login-card {
            margin-top: 5rem;
            margin-bottom: 3rem;
        }
        .demo-credentials {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow login-card">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h4>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1 text-success"></i>Email
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                       required
                                       placeholder="ejemplo@email.com">
                                <div class="invalid-feedback">
                                    Por favor, introduce un email válido
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1 text-success"></i>Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required
                                       placeholder="Tu contraseña"
                                       minlength="6">
                                <div class="invalid-feedback">
                                    La contraseña debe tener al menos 6 caracteres
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                </button>
                            </div>
                            
                            <div class="text-center mb-3">
                                <a href="#" class="text-success small">
                                    <i class="fas fa-question-circle me-1"></i>¿Olvidaste tu contraseña?
                                </a>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <p class="mb-2">
                                    ¿No tienes cuenta? 
                                    <a href="<?= BASE_URL ?>/auth/registro.php" class="text-success fw-bold">
                                        Regístrate aquí
                                    </a>
                                </p>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="text-muted small mb-2">Credenciales de prueba:</p>
                            <div class="border rounded p-3 bg-light demo-credentials">
                                <p class="mb-1">
                                    <strong>Admin:</strong> administrador.adoptaweb@gmail.com / demo123
                                </p>
                                <p class="mb-0">
                                    <strong>Usuario:</strong> ana@gmail.com / demo123
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        (function() {
            'use strict'
            
           
            var forms = document.querySelectorAll('form')
            
          
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