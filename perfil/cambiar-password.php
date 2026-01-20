<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$user = obtenerUsuario();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    
    
    $errores = [];
    
    if (empty($actual)) {
        $errores[] = 'La contraseña actual es obligatoria';
    }
    
    if (empty($nueva)) {
        $errores[] = 'La nueva contraseña es obligatoria';
    } elseif (strlen($nueva) < 6) {
        $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres';
    } elseif (!preg_match('/[A-Z]/', $nueva)) {
        $errores[] = 'La nueva contraseña debe contener al menos una mayúscula';
    } elseif (!preg_match('/[0-9]/', $nueva)) {
        $errores[] = 'La nueva contraseña debe contener al menos un número';
    }
    
    if (empty($confirmar)) {
        $errores[] = 'La confirmación de contraseña es obligatoria';
    } elseif ($nueva !== $confirmar) {
        $errores[] = 'Las contraseñas no coinciden';
    }
    
    
    if (empty($errores)) {
       
        if (!password_verify($actual, $user['password_hash'])) {
            $errores[] = 'La contraseña actual es incorrecta';
        }
    }
    
    if (empty($errores)) {
        try {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?");
            
            if ($stmt->execute([$hash, $_SESSION['user_id']])) {
                $success = 'Contraseña actualizada correctamente.';
                
              
            } else {
                $error = 'Error al actualizar la contraseña. Por favor, inténtalo de nuevo.';
            }
        } catch (PDOException $e) {
            error_log("Error al cambiar contraseña: " . $e->getMessage());
            $error = 'Error interno del sistema. Por favor, inténtalo más tarde.';
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
    <title>Cambiar Contraseña - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
    <style>
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
        <div class="row">
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-key me-2"></i>Cambiar Contraseña</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="changePasswordForm" novalidate>
                            <div class="mb-3">
                                <label for="actual" class="form-label">
                                    <i class="fas fa-lock me-1 text-success"></i>Contraseña actual *
                                </label>
                                <input type="password" 
                                       name="actual" 
                                       id="actual" 
                                       class="form-control" 
                                       required
                                       placeholder="Introduce tu contraseña actual">
                                <div class="invalid-feedback">
                                    Por favor, introduce tu contraseña actual
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nueva" class="form-label">
                                    <i class="fas fa-lock me-1 text-success"></i>Nueva contraseña *
                                </label>
                                <input type="password" 
                                       name="nueva" 
                                       id="nueva" 
                                       class="form-control" 
                                       required
                                       minlength="6"
                                       placeholder="Mínimo 6 caracteres, una mayúscula y un número">
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Mínimo 6 caracteres, una mayúscula y un número
                                </small>
                                <div class="invalid-feedback">
                                    La contraseña debe tener al menos 6 caracteres, una mayúscula y un número
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmar" class="form-label">
                                    <i class="fas fa-lock me-1 text-success"></i>Confirmar nueva contraseña *
                                </label>
                                <input type="password" 
                                       name="confirmar" 
                                       id="confirmar" 
                                       class="form-control" 
                                       required
                                       minlength="6"
                                       placeholder="Repite la nueva contraseña">
                                <div class="invalid-feedback" id="passwordMatchFeedback">
                                    Las contraseñas no coinciden
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?= BASE_URL ?>/perfil/" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Guardar cambios
                                </button>
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
    
    document.getElementById('nueva').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');
        
        
        let strength = 0;
        if (password.length >= 6) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        if (/[^A-Za-z0-9]/.test(password)) strength += 25;
        
       
        strengthBar.style.width = strength + '%';
        
       
        if (strength < 50) {
            strengthBar.style.backgroundColor = '#dc3545';
        } else if (strength < 75) {
            strengthBar.style.backgroundColor = '#ffc107';
        } else {
            strengthBar.style.backgroundColor = '#28a745';
        }
        
      
        const confirmPassword = document.getElementById('confirmar').value;
        if (confirmPassword !== '') {
            validatePasswordMatch();
        }
    });
    
    
    document.getElementById('confirmar').addEventListener('input', validatePasswordMatch);
    
    function validatePasswordMatch() {
        const password = document.getElementById('nueva').value;
        const confirmPassword = document.getElementById('confirmar').value;
        
        if (confirmPassword !== '' && password !== confirmPassword) {
            document.getElementById('confirmar').classList.add('is-invalid');
            document.getElementById('confirmar').classList.remove('is-valid');
        } else if (confirmPassword !== '') {
            document.getElementById('confirmar').classList.add('is-valid');
            document.getElementById('confirmar').classList.remove('is-invalid');
        }
    }
    
    
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        const password = document.getElementById('nueva').value;
        const confirmPassword = document.getElementById('confirmar').value;
        
        
        if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
            e.preventDefault();
            alert('La nueva contraseña debe contener al menos una mayúscula y un número');
            document.getElementById('nueva').focus();
            return false;
        }
        
     
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            document.getElementById('confirmar').focus();
            return false;
        }
        
        return true;
    });
    
   
    (function() {
        'use strict'
        var forms = document.querySelectorAll('#changePasswordForm')
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