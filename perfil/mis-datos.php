<?php
require_once '../includes/config.php';

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

//Obtener información del usuario actual
$user = obtenerUsuario();
$error = '';
$success = '';

// Validad datos + medidas de seguridad
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    
    //Array para acumular errores de validación
    $errores = [];
    //Validaciones
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
    
    if (!empty($telefono) && !preg_match('/^[0-9\s\+\-\(\)]{9,20}$/', $telefono)) {
        $errores[] = 'El teléfono no tiene un formato válido';
    }
    
    if (!empty($direccion) && strlen($direccion) > 255) {
        $errores[] = 'La dirección no puede tener más de 255 caracteres';
    }
    
    //proceder a actualizar en base de datos si no hay errores
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, direccion = ? WHERE id_usuario = ?");
            
            if ($stmt->execute([$nombre, $apellido, $telefono, $direccion, $_SESSION['user_id']])) {
                // Actualizar nombre en sesión para reflejar cambios inmediatamente
                $_SESSION['user_name'] = $nombre;
                $success = "Datos actualizados correctamente";
            } else {
                $error = "Error al actualizar los datos";
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar datos de usuario: " . $e->getMessage());
            $error = "Error interno del sistema. Por favor, inténtalo de nuevo más tarde.";
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
    <title>Mis Datos - AdoptaWeb</title>
    <!--Inclusión de librerías y estilos necesarios-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!--Menú lateral del perfil-->
            <div class="col-md-3">
                <?php include 'menu-lateral.php'; ?>
            </div>
            
            <!--Formulario de edición de datos-->
            <div class="col-md-9">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4><i class="fas fa-user me-2"></i>Mis Datos</h4>
                    </div>
                    <div class="card-body">
                        <!--Mostrar mensaje de éxito si existe-->
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!--Formulario principal para editar datos personales-->
                        <form method="POST" id="userDataForm" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user me-1 text-success"></i>Nombre *
                                    </label>
                                    <input type="text" 
                                           name="nombre" 
                                           id="nombre" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($user['nombre']) ?>" 
                                           required
                                           pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}"
                                           placeholder="Tu nombre">
                                    <div class="invalid-feedback">
                                        El nombre debe tener entre 2 y 100 letras y espacios
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">
                                        <i class="fas fa-user me-1 text-success"></i>Apellido
                                    </label>
                                    <input type="text" 
                                           name="apellido" 
                                           id="apellido" 
                                           class="form-control" 
                                           value="<?= htmlspecialchars($user['apellido']) ?>"
                                           pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*"
                                           placeholder="Tu apellido">
                                    <div class="invalid-feedback">
                                        Solo se permiten letras y espacios
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1 text-success"></i>Email
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" 
                                       readonly
                                       disabled>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    El email no se puede modificar
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-1 text-success"></i>Teléfono
                                </label>
                                <input type="tel" 
                                       name="telefono" 
                                       id="telefono" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($user['telefono']) ?>"
                                       pattern="[0-9\s\+\-\(\)]{9,20}"
                                       placeholder="+34 600 123 456">
                                <div class="invalid-feedback">
                                    Formato de teléfono no válido (9-20 dígitos, espacios, +, -, ())
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="direccion" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1 text-success"></i>Dirección
                                </label>
                                <textarea name="direccion" 
                                          id="direccion" 
                                          class="form-control" 
                                          rows="3"
                                          maxlength="255"
                                          placeholder="Calle, número, ciudad, código postal"><?= htmlspecialchars($user['direccion']) ?></textarea>
                                <div class="invalid-feedback">
                                    La dirección no puede tener más de 255 caracteres
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1 text-success"></i>Fecha de registro
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       value="<?= date('d/m/Y', strtotime($user['fecha_registro'])) ?>" 
                                       readonly
                                       disabled>
                            </div>
                            
                            <!--Botones de acción-->
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
    <!--Scripts de Bootstrap y JavaScript personalizado-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        //Validación de Bootstrap para formularios
        (function() {
            'use strict'
            var forms = document.querySelectorAll('#userDataForm')
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
        
        //Validaciones en tiempo real
        document.getElementById('telefono').addEventListener('input', function() {
            const telefono = this.value;
            const regex = /^[0-9\s\+\-\(\)]{0,20}$/;
            
            if (telefono === '') {
                this.classList.remove('is-invalid', 'is-valid');
            } else if (!regex.test(telefono) || telefono.length < 9) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
        
        document.getElementById('nombre').addEventListener('input', function() {
            const nombre = this.value;
            const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/;
            
            if (nombre === '') {
                this.classList.remove('is-invalid', 'is-valid');
            } else if (!regex.test(nombre)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
        
        document.getElementById('apellido').addEventListener('input', function() {
            const apellido = this.value;
            const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
            
            if (apellido === '') {
                this.classList.remove('is-invalid', 'is-valid');
            } else if (!regex.test(apellido)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>