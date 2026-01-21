<?php
//Archivo: centros.php - Gestión de centros de adopción
require_once '../includes/config.php';
//Solo admin logueado puede acceder
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

//Procesar formulario de guardar/editar centro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_centro'])) {
    $id = isset($_POST['id_centro']) ? (int)$_POST['id_centro'] : 0;
    $nombre = sanitizar($_POST['nombre']);
    $direccion = sanitizar($_POST['direccion'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $web = sanitizar($_POST['web'] ?? '');
    $latitud = $_POST['latitud'] ? (float)$_POST['latitud'] : null;
    $longitud = $_POST['longitud'] ? (float)$_POST['longitud'] : null;

    $imagen_url = $_POST['imagen_actual'] ?? '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = '../img/centros/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $file_path)) {
            $imagen_url = './img/centros/' . $file_name;
            
            if (!empty($_POST['imagen_actual']) && file_exists('../' . ltrim($_POST['imagen_actual'], './'))) {
                unlink('../' . ltrim($_POST['imagen_actual'], './'));
            }
        }
    }

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE centros SET nombre = ?, direccion = ?, telefono = ?, email = ?, web = ?, latitud = ?, longitud = ?, imagen_url = ? WHERE id_centro = ?");
        $stmt->execute([$nombre, $direccion, $telefono, $email, $web, $latitud, $longitud, $imagen_url, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO centros (nombre, direccion, telefono, email, web, latitud, longitud, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $direccion, $telefono, $email, $web, $latitud, $longitud, $imagen_url]);
    }
    header("Location: centros.php");
    exit();
}

//Procesar eliminación de centro
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM animales WHERE id_centro = ?");
    $stmt->execute([$id]);
    $total = $stmt->fetchColumn();

    if ($total > 0) {
        $_SESSION['error'] = "No se puede eliminar: tiene $total animales asignados.";
    } else {
        $pdo->prepare("DELETE FROM centros WHERE id_centro = ?")->execute([$id]);
        $_SESSION['exito'] = "Centro eliminado correctamente.";
    }
    header("Location: centros.php");
    exit();
}

//Obtener lista de centros
$centros = $pdo->query("SELECT * FROM centros ORDER BY nombre")->fetchAll();

//Mostrar mensajes de éxito/error
$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Centros - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-home me-2"></i>Gestionar Centros</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCentro">
                    <i class="fas fa-plus me-2"></i>Agregar Centro
                </button>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($exito): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $exito ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
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
                        <td>
                            <?php if (!empty($c['imagen_url'])): ?>
                                <img src="../<?= ltrim($c['imagen_url'], './') ?>" 
                                     alt="<?= htmlspecialchars($c['nombre']) ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px; border-radius: 5px;">
                                    <i class="fas fa-home fa-lg text-secondary"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['direccion']) ?></td>
                        <td><?= htmlspecialchars($c['telefono']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-editar-centro" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCentro"
                                    data-id="<?= $c['id_centro'] ?>"
                                    data-nombre="<?= htmlspecialchars($c['nombre']) ?>"
                                    data-direccion="<?= htmlspecialchars($c['direccion']) ?>"
                                    data-telefono="<?= htmlspecialchars($c['telefono']) ?>"
                                    data-email="<?= htmlspecialchars($c['email']) ?>"
                                    data-web="<?= htmlspecialchars($c['web']) ?>"
                                    data-latitud="<?= $c['latitud'] ?>"
                                    data-longitud="<?= $c['longitud'] ?>"
                                    data-imagen="<?= $c['imagen_url'] ?>">
                                Editar
                            </button>
                            <a href="?eliminar=<?= $c['id_centro'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Seguro que deseas eliminar este centro?')">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar centro -->
<div class="modal fade" id="modalCentro" tabindex="-1" aria-labelledby="modalCentroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formCentro">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalCentroLabel">Nuevo Centro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_centro" id="id_centro" value="0">
                    <input type="hidden" name="imagen_actual" id="imagen_actual" value="">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="nombre" id="centro_nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="centro_telefono">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" id="centro_direccion">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="centro_email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Web</label>
                            <input type="text" class="form-control" name="web" id="centro_web">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitud</label>
                            <input type="text" class="form-control" name="latitud" id="centro_latitud">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitud</label>
                            <input type="text" class="form-control" name="longitud" id="centro_longitud">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" class="form-control" name="imagen" accept="image/*">
                        <div class="mt-2" id="imagen_preview"></div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
//JavaScript para manejar el modal de centros
document.addEventListener('DOMContentLoaded', function() {
    const modalCentro = document.getElementById('modalCentro');
    const btnEditarCentro = document.querySelectorAll('.btn-editar-centro');
    const modalTitle = document.getElementById('modalCentroLabel');
    const formCentro = document.getElementById('formCentro');
    const imagenPreview = document.getElementById('imagen_preview');
    const imagenActual = document.getElementById('imagen_actual');
    
    
    const idCentro = document.getElementById('id_centro');
    const nombreInput = document.getElementById('centro_nombre');
    const direccionInput = document.getElementById('centro_direccion');
    const telefonoInput = document.getElementById('centro_telefono');
    const emailInput = document.getElementById('centro_email');
    const webInput = document.getElementById('centro_web');
    const latitudInput = document.getElementById('centro_latitud');
    const longitudInput = document.getElementById('centro_longitud');
    

    btnEditarCentro.forEach(btn => {
        btn.addEventListener('click', function() {
            idCentro.value = this.getAttribute('data-id');
            nombreInput.value = this.getAttribute('data-nombre');
            direccionInput.value = this.getAttribute('data-direccion');
            telefonoInput.value = this.getAttribute('data-telefono');
            emailInput.value = this.getAttribute('data-email');
            webInput.value = this.getAttribute('data-web');
            latitudInput.value = this.getAttribute('data-latitud');
            longitudInput.value = this.getAttribute('data-longitud');
            
          
            const imagenUrl = this.getAttribute('data-imagen');
            imagenActual.value = imagenUrl;
            imagenPreview.innerHTML = '';
            
            if (imagenUrl) {
                const img = document.createElement('img');
                img.src = '../' + imagenUrl.replace('./', '');
                img.alt = 'Imagen actual';
                img.className = 'img-thumbnail mt-2';
                img.style.maxWidth = '150px';
                imagenPreview.appendChild(img);
                
                const p = document.createElement('p');
                p.className = 'text-muted small mt-1';
                p.textContent = 'Imagen actual. Sube una nueva para reemplazarla.';
                imagenPreview.appendChild(p);
            }
            
            modalTitle.textContent = 'Editar Centro';
        });
    });
    
    //Limpiar formulario al cerrar modal
    modalCentro.addEventListener('hidden.bs.modal', function() {
        formCentro.reset();
        idCentro.value = '0';
        imagenActual.value = '';
        imagenPreview.innerHTML = '';
        modalTitle.textContent = 'Nuevo Centro';
    });
});
</script>
</body>
</html>