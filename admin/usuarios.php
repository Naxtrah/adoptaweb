<<<<<<< HEAD
<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
//Variables para acción y mensajes
$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Guardar o actualizar usuario
    if (isset($_POST['guardar_usuario'])) {
        $nombre = sanitizar($_POST['nombre']);
        $apellido = sanitizar($_POST['apellido'] ?? '');
        $email = sanitizar($_POST['email']);
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $direccion = sanitizar($_POST['direccion'] ?? '');
        $id_rol = (int)$_POST['id_rol'];
        $nueva_password = $_POST['password'] ?? '';
        try {
            if ($id > 0) {
                //Actualizar usuario existente
                if (!empty($nueva_password)) {
                    $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, id_rol = ?, password_hash = ? WHERE id_usuario = ?");
                    $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $password_hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, id_rol = ? WHERE id_usuario = ?");
                    $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $id]);
                }
                $mensaje = 'Usuario actualizado correctamente';
            } else {
                //Crear nuevo usuario
                $password_hash = password_hash($nueva_password ?: 'password123', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, direccion, id_rol, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $password_hash]);
                $mensaje = 'Usuario creado correctamente';
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } 
    //Eliminar usuario
    elseif (isset($_POST['eliminar_usuario'])) {
        $id_eliminar = (int)$_POST['id_usuario'];
        if ($id_eliminar == $_SESSION['user_id']) {
            $error = 'No puedes eliminar tu propio usuario';
        } else {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id_eliminar]);
            $mensaje = 'Usuario eliminado correctamente';
        }
    }
}
//Configuración de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 15;
$inicio = ($pagina - 1) * $por_pagina;
//Obtener total de usuarios
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_paginas = ceil($total_usuarios / $por_pagina);
//Consultar usuarios con paginación
$stmt = $pdo->prepare("SELECT u.*, r.nombre_rol FROM usuarios u LEFT JOIN roles r ON u.id_rol = r.id_rol ORDER BY u.fecha_registro DESC LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();
//Obtener roles y datos de usuario
$roles = $pdo->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();
$usuario_editar = null;
if ($accion == 'editar' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $usuario_editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<!--Cabecera del panel de administración-->
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <!--Menú lateral-->
        <?php include 'sidebar.php'; ?>        
        <div class="col-md-10 p-4">
            <!--Encabezado con botón nuevo usuario-->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </button>
            </div>
            <!--Mensajes de éxito/error-->
            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
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
                                    <th>Rol</th>
                                    <th>Registro</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario['id_usuario'] ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $usuario['id_rol'] == 1 ? 'warning' : 'success' ?>">
                                            <?= $usuario['nombre_rol'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                    <td class="text-center">
                                        <!--Botón editar-->
                                        <a href="usuarios.php?accion=editar&id=<?= $usuario['id_usuario'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           data-bs-toggle="modal" data-bs-target="#modalUsuario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!--Formulario eliminar-->
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                            <button type="submit" name="eliminar_usuario" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
    <!--Modal para crear/editar usuarios-->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_usuario" value="<?= $usuario_editar['id_usuario'] ?? 0 ?>">
                        <!--Nombre y apellido-->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" 
                                       value="<?= $usuario_editar['nombre'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control" name="apellido" 
                                       value="<?= $usuario_editar['apellido'] ?? '' ?>">
                            </div>
                        </div>
                        <!--Email y teléfono-->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?= $usuario_editar['email'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" 
                                       value="<?= $usuario_editar['telefono'] ?? '' ?>">
                            </div>
                        </div>
                        <!--Dirección-->
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion" rows="2"><?= $usuario_editar['direccion'] ?? '' ?></textarea>
                        </div>
                        <!--Rol y contraseña-->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rol *</label>
                                <select class="form-select" name="id_rol" required>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id_rol'] ?>" 
                                            <?= ($usuario_editar['id_rol'] ?? 2) == $rol['id_rol'] ? 'selected' : '' ?>>
                                            <?= $rol['nombre_rol'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" 
                                       placeholder="Dejar en blanco para no cambiar">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar_usuario" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
=======
<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guardar_usuario'])) {
        $nombre = sanitizar($_POST['nombre']);
        $apellido = sanitizar($_POST['apellido'] ?? '');
        $email = sanitizar($_POST['email']);
        $telefono = sanitizar($_POST['telefono'] ?? '');
        $direccion = sanitizar($_POST['direccion'] ?? '');
        $id_rol = (int)$_POST['id_rol'];
        $nueva_password = $_POST['password'] ?? '';

        try {
            if ($id > 0) {
                if (!empty($nueva_password)) {
                    $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, id_rol = ?, password_hash = ? WHERE id_usuario = ?");
                    $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $password_hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, telefono = ?, direccion = ?, id_rol = ? WHERE id_usuario = ?");
                    $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $id]);
                }
                $mensaje = 'Usuario actualizado correctamente';
            } else {
                $password_hash = password_hash($nueva_password ?: 'password123', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, direccion, id_rol, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $apellido, $email, $telefono, $direccion, $id_rol, $password_hash]);
                $mensaje = 'Usuario creado correctamente';
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['eliminar_usuario'])) {
        $id_eliminar = (int)$_POST['id_usuario'];
        if ($id_eliminar == $_SESSION['user_id']) {
            $error = 'No puedes eliminar tu propio usuario';
        } else {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id_eliminar]);
            $mensaje = 'Usuario eliminado correctamente';
        }
    }
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 15;
$inicio = ($pagina - 1) * $por_pagina;
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_paginas = ceil($total_usuarios / $por_pagina);
$stmt = $pdo->prepare("SELECT u.*, r.nombre_rol FROM usuarios u LEFT JOIN roles r ON u.id_rol = r.id_rol ORDER BY u.fecha_registro DESC LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();
$roles = $pdo->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();
$usuario_editar = null;
if ($accion == 'editar' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $usuario_editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUsuario"><i class="fas fa-plus me-2"></i>Nuevo Usuario</button>
            </div>
            <?php if ($mensaje): ?><div class="alert alert-success alert-dismissible fade show"><?= $mensaje ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light"><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Registro</th><th class="text-center">Acciones</th></tr></thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario['id_usuario'] ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><span class="badge bg-<?= $usuario['id_rol'] == 1 ? 'warning' : 'success' ?>"><?= $usuario['nombre_rol'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                    <td class="text-center">
                                        <a href="usuarios.php?accion=editar&id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUsuario"><i class="fas fa-edit"></i></a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                            <button type="submit" name="eliminar_usuario" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($total_paginas > 1): ?>
                    <nav class="mt-4"><ul class="pagination justify-content-center">
                        <?php if ($pagina > 1): ?><li class="page-item"><a class="page-link" href="?pagina=<?= $pagina - 1 ?>"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?><li class="page-item <?= $i == $pagina ? 'active' : '' ?>"><a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?>
                        <?php if ($pagina < $total_paginas): ?><li class="page-item"><a class="page-link" href="?pagina=<?= $pagina + 1 ?>"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
                    </ul></nav><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white"><h5 class="modal-title">Usuario</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_usuario" value="<?= $usuario_editar['id_usuario'] ?? 0 ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Nombre *</label><input type="text" class="form-control" name="nombre" value="<?= $usuario_editar['nombre'] ?? '' ?>" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Apellido</label><input type="text" class="form-control" name="apellido" value="<?= $usuario_editar['apellido'] ?? '' ?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Email *</label><input type="email" class="form-control" name="email" value="<?= $usuario_editar['email'] ?? '' ?>" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input type="tel" class="form-control" name="telefono" value="<?= $usuario_editar['telefono'] ?? '' ?>"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Dirección</label><textarea class="form-control" name="direccion" rows="2"><?= $usuario_editar['direccion'] ?? '' ?></textarea></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Rol *</label><select class="form-select" name="id_rol" required><?php foreach ($roles as $rol): ?><option value="<?= $rol['id_rol'] ?>" <?= ($usuario_editar['id_rol'] ?? 2) == $rol['id_rol'] ? 'selected' : '' ?>><?= $rol['nombre_rol'] ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Contraseña</label><input type="password" class="form-control" name="password" placeholder="Dejar en blanco para no cambiar"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" name="guardar_usuario" class="btn btn-success">Guardar</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
</html>