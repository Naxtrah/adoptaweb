<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_centro'])) {
    //sanitizar datos
    $nombre = sanitizar($_POST['nombre']);
    $direccion = sanitizar($_POST['direccion'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $web = sanitizar($_POST['web'] ?? '');
    $latitud = $_POST['latitud'] ? (float)$_POST['latitud'] : null;
    $longitud = $_POST['longitud'] ? (float)$_POST['longitud'] : null;
    //Manejo de imagen
    $imagen_url = $_POST['imagen_actual'] ?? '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = '../img/centros/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $file_path)) {
            $imagen_url = './img/centros/' . $file_name;
        }
    }
    //Insertar o actualizar en la base de datos
    if ($id > 0) {
        //Actualizar centro existente
        $stmt = $pdo->prepare("UPDATE centros SET nombre = ?, direccion = ?, telefono = ?, email = ?, web = ?, latitud = ?, longitud = ?, imagen_url = ? WHERE id_centro = ?");
        $stmt->execute([$nombre, $direccion, $telefono, $email, $web, $latitud, $longitud, $imagen_url, $id]);
    } else {
        //Crear nuevo centro
        $stmt = $pdo->prepare("INSERT INTO centros (nombre, direccion, telefono, email, web, latitud, longitud, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $direccion, $telefono, $email, $web, $latitud, $longitud, $imagen_url]);
    }   
    header("Location: centros.php?accion=listar");
    exit();
}
//Eliminar centro
if ($accion === 'eliminar' && $id > 0) {
    $pdo->prepare("DELETE FROM centros WHERE id_centro = ?")->execute([$id]);
    header("Location: centros.php?accion=listar");
    exit();
}
//Obtener lista de centros y datos para edición
$centros = $pdo->query("SELECT * FROM centros ORDER BY nombre")->fetchAll();
$centro_editar = null;
if ($accion == 'editar' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM centros WHERE id_centro = ?");
    $stmt->execute([$id]);
    $centro_editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Centros - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <!--Encabezado con botón para agregar-->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-home me-2"></i>Gestionar Centros</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCentro">
                    <i class="fas fa-plus me-2"></i>Agregar Centro
                </button>
            </div>
            <!--Tabla de centros-->
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($centros as $c): ?>
                    <tr>
                        <!--Imagen del centro-->
                        <td>
                            <?php if (!empty($c['imagen_url'])): ?>
                                <img src="../<?= ltrim($c['imagen_url'], './') ?>" 
                                     alt="<?= htmlspecialchars($c['nombre']) ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            <?php else: ?>
                                <!--Icono por defecto si no hay imagen-->
                                <i class="fas fa-home fa-2x text-success"></i>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['direccion']) ?></td>
                        <td><?= htmlspecialchars($c['telefono']) ?></td>
                        <!--Botones de acción-->
                        <td>
                            <a href="?accion=editar&id=<?= $c['id_centro'] ?>" 
                               class="btn btn-sm btn-warning">Editar</a>
                            <a href="?accion=eliminar&id=<?= $c['id_centro'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--Modal para crear/editar centros-->
<div class="modal fade" id="modalCentro" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Centro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!--ID oculto para edición-->
                    <input type="hidden" name="id_centro" value="<?= $centro_editar['id_centro'] ?? 0 ?>">
                    <!--Nombre y teléfono-->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="nombre" 
                                   value="<?= $centro_editar['nombre'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" 
                                   value="<?= $centro_editar['telefono'] ?? '' ?>">
                        </div>
                    </div>
                    <!--Dirección-->
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" 
                               value="<?= $centro_editar['direccion'] ?? '' ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= $centro_editar['email'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Web</label>
                            <input type="text" class="form-control" name="web" 
                                   value="<?= $centro_editar['web'] ?? '' ?>">
                        </div>
                    </div>
                    <!--Coordenadas geográficas-->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitud</label>
                            <input type="text" class="form-control" name="latitud" 
                                   value="<?= $centro_editar['latitud'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitud</label>
                            <input type="text" class="form-control" name="longitud" 
                                   value="<?= $centro_editar['longitud'] ?? '' ?>">
                        </div>
                    </div>
                    <!--Subida de imagen-->
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" class="form-control" name="imagen" accept="image/*">                  
                        <!--Mostrar imagen actual si existe-->
                        <?php if (!empty($centro_editar['imagen_url'])): ?>
                            <input type="hidden" name="imagen_actual" value="<?= $centro_editar['imagen_url'] ?>">
                            <img src="../<?= ltrim($centro_editar['imagen_url'], './') ?>" 
                                 class="img-thumbnail mt-2" width="150">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="guardar_centro" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Bootstrap JavaScript-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>