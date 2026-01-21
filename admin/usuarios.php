<?php
require_once '../includes/config.php';
//Verificar autenticación y permisos de administrador
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

//Determinar acción solicitada
$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';
$error = '';

//Cargar datos de usuario para editar si se especifica ID
$usuario_editar = null;
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $usuario_editar = $stmt->fetch();
    $es_admin_actual = $usuario_editar && ($_SESSION['user_id'] == $usuario_editar['id_usuario']);
} else {
    $es_admin_actual = false;
}

//Procesar formularios POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guardar_usuario'])) {
        //Obtener y sanitizar datos del formulario
        $nombre = sanitizar($_POST['nombre']);
        $apellido = sanitizar($_POST['apellido'] ?? '');
        $email = sanitizar($_POST['email']);
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $direccion = sanitizar($_POST['direccion'] ?? '');
        $id_rol = (int)$_POST['id_rol'];
        $nueva_password = $_POST['password'] ?? '';
        $id_usuario = (int)$_POST['id_usuario'];

        //Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
        $stmt->execute([$email, $id_usuario]);
        $email_existente = $stmt->fetch();
        
        if ($email_existente) {
            $error = 'Este email ya está registrado por otro usuario.';
        } else {
            try {
                if ($id_usuario > 0) {
                    //Actualizar usuario existente
                    
                    //Mantener rol de administrador si es el usuario actual
                    if ($_SESSION['user_id'] == $id_usuario) {
                        $id_rol = 1; //Forzar rol de administrador
                    }
                    
                    //Actualizar con o sin nueva contraseña
                    if (!empty($nueva_password)) {
                        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, id_rol = ?, password_hash = ? WHERE id_usuario = ?");
                        $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $password_hash, $id_usuario]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, id_rol = ? WHERE id_usuario = ?");
                        $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $id_usuario]);
                    }
                    
                    //Cerrar sesión si el usuario editó sus propios datos
                    if ($_SESSION['user_id'] == $id_usuario) {
                        session_destroy();
                        $_SESSION['mensaje'] = 'Tus datos han sido actualizados. Por favor, inicia sesión nuevamente.';
                        header('Location: ' . BASE_URL . '/auth/login.php');
                        exit();
                    }
                    
                    $mensaje = 'Usuario actualizado correctamente';
                } else {
                    //Crear nuevo usuario
                    
                    //Generar contraseña automática si no se proporciona
                    if (empty($nueva_password)) {
                        $nueva_password = generarPassword();
                    }
                    $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, direccion, id_rol, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $password_hash]);
                    $mensaje = 'Usuario creado correctamente. Contraseña generada: ' . $nueva_password;
                }
                
                //Redirigir con mensaje de éxito
                header('Location: usuarios.php?mensaje=' . urlencode($mensaje));
                exit();
                
            } catch (PDOException $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['eliminar_usuario'])) {
        $id_eliminar = (int)$_POST['id_usuario'];
        
        //Validaciones para eliminación
        if ($id_eliminar == $_SESSION['user_id']) {
            $error = 'No puedes eliminar tu propio usuario.';
        } 
        
        else {
            //Verificar rol del usuario a eliminar
            $stmt = $pdo->prepare("SELECT id_rol FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id_eliminar]);
            $usuario_a_eliminar = $stmt->fetch();
            
            if (!$usuario_a_eliminar) {
                $error = 'Usuario no encontrado.';
            } else {
                //Validaciones especiales para administradores
                if ($usuario_a_eliminar['id_rol'] == 1) {
                    //Verificar que no sea el único administrador
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_rol = 1 AND id_usuario != ?");
                    $stmt->execute([$id_eliminar]);
                    $admins_restantes = $stmt->fetchColumn();
                    
                    if ($admins_restantes == 0) {
                        $error = 'No se puede eliminar al único administrador del sistema.';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
                        $stmt->execute([$id_eliminar]);
                        $mensaje = 'Usuario administrador eliminado correctamente.';
                    }
                } else {
                    //Eliminar usuario normal
                    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
                    $stmt->execute([$id_eliminar]);
                    $mensaje = 'Usuario eliminado correctamente.';
                }
                
                if ($mensaje) {
                    header('Location: usuarios.php?mensaje=' . urlencode($mensaje));
                    exit();
                }
            }
        }
    }
}

//Mostrar mensaje pasado por URL
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
}

//Configurar paginación para lista de usuarios
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 15;
$inicio = ($pagina - 1) * $por_pagina;

//Calcular total de páginas
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_paginas = ceil($total_usuarios / $por_pagina);

//Consultar usuarios con paginación
$stmt = $pdo->prepare("SELECT u.*, r.nombre_rol FROM usuarios u LEFT JOIN roles r ON u.id_rol = r.id_rol ORDER BY u.fecha_registro DESC LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();

//Consultar roles disponibles
$roles = $pdo->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();

//Función para generar contraseña segura
function generarPassword($longitud = 12) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    $max = strlen($caracteres) - 1;
    
    for ($i = 0; $i < $longitud; $i++) {
        $password .= $caracteres[random_int(0, $max)];
    }
    
    return $password;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Admin</title>
    <!--Enlaces a estilos externos-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
    <style>
        .badge-admin {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-user {
            background-color: #28a745;
            color: white;
        }
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            background: none;
            border: none;
            color: #6c757d;
        }
        .modal-editar {
            background-color: rgba(0,0,0,0.5);
        }
        #modalUsuario{
           display: block;
           background-color: rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <!--Encabezado con botón de nuevo usuario-->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
                <a href="usuarios.php?accion=nuevo" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </a>
            </div>
            
            <!--Mostrar mensajes de éxito-->
            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!--Mostrar mensajes de error-->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!--Tabla de usuarios-->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Registro</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): 
                                    $es_mi_usuario = ($usuario['id_usuario'] == $_SESSION['user_id']);
                                ?>
                                <tr>
                                    <td><?= $usuario['id_usuario'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
                                        <?php if (!empty($usuario['apellido'])): ?>
                                            <br><small><?= htmlspecialchars($usuario['apellido']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><?= htmlspecialchars($usuario['telefono'] ?? 'No registrado') ?></td>
                                    <td>
                                        <!--Indicador visual de rol-->
                                        <?php if ($usuario['id_rol'] == 1): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-crown me-1"></i><?= $usuario['nombre_rol'] ?>
                                                <?php if ($es_mi_usuario): ?>
                                                    <i class="fas fa-user ms-1" title="Eres tú"></i>
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $usuario['nombre_rol'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                    <td class="text-center">
                                        <!--Botón editar-->
                                        <a href="usuarios.php?accion=editar&id=<?= $usuario['id_usuario'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar usuario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!--Botón eliminar (no disponible para propio usuario)-->
                                        <?php if (!$es_mi_usuario): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar a <?= htmlspecialchars($usuario['nombre']) ?>? Esta acción no se puede deshacer.');">
                                                <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                                <button type="submit" name="eliminar_usuario" class="btn btn-sm btn-danger" title="Eliminar usuario">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted" title="No puedes eliminarte a ti mismo">
                                                <i class="fas fa-ban"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!--Paginación-->
                    <?php if ($total_paginas > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!--Botón anterior-->
                            <?php if ($pagina > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!--Números de página-->
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!--Botón siguiente-->
                            <?php if ($pagina < $total_paginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Modal para crear/editar usuario (se muestra en editar/nuevo)-->
<?php if ($accion === 'editar' || $accion === 'nuevo'): ?>
<div class="modal fade show" id="modalUsuario" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!--Encabezado del modal-->
            <div class="modal-header bg-<?= $accion === 'editar' ? 'warning' : 'success' ?> text-white">
                <h5 class="modal-title">
                    <?= ($accion === 'editar' ? 'Editar Usuario' : 'Nuevo Usuario') ?>
                    <?php if ($es_admin_actual && $usuario_editar): ?>
                        <small class="text-dark">(Administrador actual)</small>
                    <?php endif; ?>
                </h5>
                <!--Botón para cerrar (redirige a lista)-->
                <a href="usuarios.php" class="btn-close btn-close-white"></a>
            </div>
            <!--Formulario de usuario-->
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" value="<?= $usuario_editar['id_usuario'] ?? 0 ?>">
                    
                    <!--Datos personales-->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="nombre" 
                                   value="<?= htmlspecialchars($usuario_editar['nombre'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido" 
                                   value="<?= htmlspecialchars($usuario_editar['apellido'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!--Contacto-->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= htmlspecialchars($usuario_editar['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" 
                                   value="<?= htmlspecialchars($usuario_editar['telefono'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!--Dirección-->
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2"><?= htmlspecialchars($usuario_editar['direccion'] ?? '') ?></textarea>
                    </div>
                    
                    <!--Rol y contraseña-->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol *</label>
                            <?php if ($es_admin_actual && $usuario_editar): ?>
                                <!--Rol fijo para administrador actual-->
                                <input type="hidden" name="id_rol" value="1">
                                <input type="text" class="form-control" value="Administrador" disabled>
                                <small class="text-muted">Tu rol de administrador no puede ser modificado.</small>
                            <?php else: ?>
                                <select class="form-select" name="id_rol" required>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id_rol'] ?>" 
                                            <?= ($usuario_editar['id_rol'] ?? 2) == $rol['id_rol'] ? 'selected' : '' ?>>
                                            <?= $rol['nombre_rol'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3 password-field">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" 
                                   id="passwordInput" placeholder="Dejar en blanco para no cambiar">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            <small class="text-muted">
                                <?php if ($usuario_editar): ?>
                                    Solo rellena si quieres cambiar la contraseña
                                <?php else: ?>
                                    Si se deja en blanco, se generará una automáticamente
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <!--Pie del modal con botones-->
                <div class="modal-footer">
                    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="guardar_usuario" class="btn btn-<?= $accion === 'editar' ? 'warning' : 'success' ?>">
                        <?= $usuario_editar ? 'Actualizar' : 'Crear' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!--Scripts de Bootstrap y funcionalidad personalizada-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
//Funcionalidad para mostrar/ocultar contraseña
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }
    
    //Mantener modal abierto cuando se carga para editar/nuevo
    <?php if ($accion === 'editar' || $accion === 'nuevo'): ?>
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
    <?php endif; ?>
});
</script>
</body>
</html>