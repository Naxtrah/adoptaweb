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
//Procesamiento de formularios POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Guardar o actualizar animal
    if (isset($_POST['guardar_animal'])) {
        // Obtener y sanitizar datos del formulario
        $nombre = sanitizar($_POST['nombre']);
        $especie = sanitizar($_POST['especie']);
        $raza = sanitizar($_POST['raza'] ?? '');
        $edad = (int)$_POST['edad'];
        $sexo = sanitizar($_POST['sexo']);
        $descripcion = sanitizar($_POST['descripcion']);
        $estado = sanitizar($_POST['estado']);
        $id_centro = (int)$_POST['id_centro'];
        //Manejo de imagen: mantener actual o subir nueva
        $imagen_url = $_POST['imagen_actual'] ?? '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $upload_dir = '../img/animales/';
            $file_name = uniqid() . '_' . basename($_FILES['imagen']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $file_path)) {
                $imagen_url = './img/animales/' . $file_name;
            }
        }
        //Insertar o actualizar en la base de datos
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE animales SET nombre = ?, especie = ?, raza = ?, edad = ?, sexo = ?, descripcion = ?, estado = ?, id_centro = ?, imagen_url = ? WHERE id_animal = ?");
                $stmt->execute([$nombre, $especie, $raza, $edad, $sexo, $descripcion, $estado, $id_centro, $imagen_url, $id]);
                $mensaje = 'Animal actualizado correctamente';
            } else {
                $fecha_ingreso = date('Y-m-d');
                $stmt = $pdo->prepare("INSERT INTO animales (nombre, especie, raza, edad, sexo, descripcion, estado, id_centro, imagen_url, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $especie, $raza, $edad, $sexo, $descripcion, $estado, $id_centro, $imagen_url, $fecha_ingreso]);
                $mensaje = 'Animal creado correctamente';
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } 
    elseif (isset($_POST['eliminar_animal'])) {
        $id_eliminar = (int)$_POST['id_animal'];
        $stmt = $pdo->prepare("DELETE FROM animales WHERE id_animal = ?");
        $stmt->execute([$id_eliminar]);
        $mensaje = 'Animal eliminado correctamente';
    }
}
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 12;
$inicio = ($pagina - 1) * $por_pagina;
$where = [];
$params = [];
if (!empty($_GET['especie'])) {
    $where[] = "a.especie = ?";
    $params[] = $_GET['especie'];
}
if (!empty($_GET['estado'])) {
    $where[] = "a.estado = ?";
    $params[] = $_GET['estado'];
}
if (!empty($_GET['centro'])) {
    $where[] = "a.id_centro = ?";
    $params[] = (int)$_GET['centro'];
}
if (!empty($_GET['busqueda'])) {
    $where[] = "(a.nombre LIKE ? OR a.raza LIKE ?)";
    $search = '%' . $_GET['busqueda'] . '%';
    $params[] = $search;
    $params[] = $search;
}
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
//Número total de animales
$count_sql = "SELECT COUNT(*) FROM animales a $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_animales = $count_stmt->fetchColumn();
$total_paginas = ceil($total_animales / $por_pagina);
//Consulta principal con paginación y filtros
$sql = "SELECT a.*, c.nombre as centro_nombre FROM animales a LEFT JOIN centros c ON a.id_centro = c.id_centro $where_clause ORDER BY a.fecha_ingreso DESC LIMIT :inicio, :por_pagina";
$stmt = $pdo->prepare($sql);
foreach ($params as $i => $param) $stmt->bindValue($i + 1, $param);
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$animales = $stmt->fetchAll();
//Obtener lista de centros y datos de animal para editar
$centros = $pdo->query("SELECT * FROM centros ORDER BY nombre")->fetchAll();
$animal_editar = null;
if ($accion == 'editar' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM animales WHERE id_animal = ?");
    $stmt->execute([$id]);
    $animal_editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Animales - Admin</title>
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
                <h2><i class="fas fa-paw me-2"></i>Gestión de Animales</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAnimal">
                    <i class="fas fa-plus me-2"></i>Nuevo Animal
                </button>
            </div>            
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
            <!--estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales")->fetchColumn() ?></h3>
                            <p class="text-muted mb-0">Total animales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Disponible'")->fetchColumn() ?></h3>
                            <p class="text-muted mb-0">Disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Reservado'")->fetchColumn() ?></h3>
                            <p class="text-muted mb-0">Reservados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-info mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Adoptado'")->fetchColumn() ?></h3>
                            <p class="text-muted mb-0">Adoptados</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--Formulario de filtros-->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Filtros de búsqueda</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre o raza" value="<?= $_GET['busqueda'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="especie">
                                <option value="">Todas las especies</option>
                                <option value="Perro" <?= ($_GET['especie'] ?? '') == 'Perro' ? 'selected' : '' ?>>Perro</option>
                                <option value="Gato" <?= ($_GET['especie'] ?? '') == 'Gato' ? 'selected' : '' ?>>Gato</option>
                                <option value="Otro" <?= ($_GET['especie'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="Disponible" <?= ($_GET['estado'] ?? '') == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                                <option value="Reservado" <?= ($_GET['estado'] ?? '') == 'Reservado' ? 'selected' : '' ?>>Reservado</option>
                                <option value="Adoptado" <?= ($_GET['estado'] ?? '') == 'Adoptado' ? 'selected' : '' ?>>Adoptado</option>
                                <option value="En tratamiento" <?= ($_GET['estado'] ?? '') == 'En tratamiento' ? 'selected' : '' ?>>En tratamiento</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="centro">
                                <option value="">Todos los centros</option>
                                <?php foreach ($centros as $centro): ?>
                                    <option value="<?= $centro['id_centro'] ?>" <?= ($_GET['centro'] ?? '') == $centro['id_centro'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($centro['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!--Tabla de animales-->
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listado de Animales</h5>
                    <span class="badge bg-success">Mostrando <?= count($animales) ?> de <?= $total_animales ?></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th><th>Imagen</th><th>Nombre</th><th>Especie/Raza</th>
                                    <th>Edad/Sexo</th><th>Estado</th><th>Centro</th><th>Ingreso</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($animales as $animal): 
                                    //Preparar URL de imagen
                                    $imagen = $animal['imagen_url'] ? '../' . ltrim($animal['imagen_url'], './') : '../img/animales/default.jpg';
                                ?>
                                <tr>
                                    <td><?= $animal['id_animal'] ?></td>
                                    <td>
                                        <img src="<?= $imagen ?>" class="img-thumbnail" 
                                             style="width:60px;height:60px;object-fit:cover;" 
                                             onerror="this.src='../img/animales/default.jpg'">
                                    </td>
                                    <td><strong><?= htmlspecialchars($animal['nombre']) ?></strong></td>
                                    <td>
                                        <small class="text-muted"><?= $animal['especie'] ?></small><br>
                                        <small><?= $animal['raza'] ?: 'Mestizo' ?></small>
                                    </td>
                                    <td>
                                        <?= $animal['edad'] ?> años<br>
                                        <small><?= $animal['sexo'] ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            ['Disponible'=>'success','Reservado'=>'warning','Adoptado'=>'info','En tratamiento'=>'danger'][$animal['estado']] ?? 'secondary' 
                                        ?>">
                                            <?= $animal['estado'] ?>
                                        </span>
                                    </td>
                                    <td><small><?= htmlspecialchars($animal['centro_nombre']) ?></small></td>
                                    <td><small><?= date('d/m/Y', strtotime($animal['fecha_ingreso'])) ?></small></td>
                                    <td class="text-center">
                                        <a href="../animales/ver.php?id=<?= $animal['id_animal'] ?>" 
                                           class="btn btn-sm btn-info me-1" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="animales.php?accion=editar&id=<?= $animal['id_animal'] ?>" 
                                           class="btn btn-sm btn-warning me-1" 
                                           data-bs-toggle="modal" data-bs-target="#modalAnimal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                            <input type="hidden" name="id_animal" value="<?= $animal['id_animal'] ?>">
                                            <button type="submit" name="eliminar_animal" class="btn btn-sm btn-danger">
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
                            <?php if ($pagina > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&<?= http_build_query($_GET) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>                  
                            <?php if ($pagina < $total_paginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&<?= http_build_query($_GET) ?>">
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
    <!--Modal para crear/editar animales-->
    <div class="modal fade" id="modalAnimal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Animal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_animal" value="<?= $animal_editar['id_animal'] ?? 0 ?>">
                        <!--Datos del animal-->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" 
                                       value="<?= $animal_editar['nombre'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Especie *</label>
                                <select class="form-select" name="especie" required>
                                    <option value="Perro" <?= ($animal_editar['especie'] ?? '') == 'Perro' ? 'selected' : '' ?>>Perro</option>
                                    <option value="Gato" <?= ($animal_editar['especie'] ?? '') == 'Gato' ? 'selected' : '' ?>>Gato</option>
                                    <option value="Otro" <?= ($animal_editar['especie'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Raza</label>
                                <input type="text" class="form-control" name="raza" 
                                       value="<?= $animal_editar['raza'] ?? '' ?>">
                            </div>
                        </div>
          
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Edad (años) *</label>
                                <input type="number" class="form-control" name="edad" 
                                       min="0" max="30" value="<?= $animal_editar['edad'] ?? 1 ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sexo *</label>
                                <select class="form-select" name="sexo" required>
                                    <option value="Macho" <?= ($animal_editar['sexo'] ?? '') == 'Macho' ? 'selected' : '' ?>>Macho</option>
                                    <option value="Hembra" <?= ($animal_editar['sexo'] ?? '') == 'Hembra' ? 'selected' : '' ?>>Hembra</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estado *</label>
                                <select class="form-select" name="estado" required>
                                    <option value="Disponible" <?= ($animal_editar['estado'] ?? '') == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                                    <option value="Reservado" <?= ($animal_editar['estado'] ?? '') == 'Reservado' ? 'selected' : '' ?>>Reservado</option>
                                    <option value="Adoptado" <?= ($animal_editar['estado'] ?? '') == 'Adoptado' ? 'selected' : '' ?>>Adoptado</option>
                                    <option value="En tratamiento" <?= ($animal_editar['estado'] ?? '') == 'En tratamiento' ? 'selected' : '' ?>>En tratamiento</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Centro *</label>
                                <select class="form-select" name="id_centro" required>
                                    <?php foreach ($centros as $centro): ?>
                                    <option value="<?= $centro['id_centro'] ?>" 
                                        <?= ($animal_editar['id_centro'] ?? '') == $centro['id_centro'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($centro['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3"><?= $animal_editar['descripcion'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen</label>
                            <input type="file" class="form-control" name="imagen" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar_animal" class="btn btn-success">Guardar</button>
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
    if (isset($_POST['guardar_animal'])) {
        $nombre = sanitizar($_POST['nombre']);
        $especie = sanitizar($_POST['especie']);
        $raza = sanitizar($_POST['raza'] ?? '');
        $edad = (int)$_POST['edad'];
        $sexo = sanitizar($_POST['sexo']);
        $descripcion = sanitizar($_POST['descripcion']);
        $estado = sanitizar($_POST['estado']);
        $id_centro = (int)$_POST['id_centro'];
        $imagen_url = $_POST['imagen_actual'] ?? '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $upload_dir = '../img/animales/';
            $file_name = uniqid() . '_' . basename($_FILES['imagen']['name']);
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $file_path)) {
                $imagen_url = './img/animales/' . $file_name;
            }
        }
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE animales SET nombre = ?, especie = ?, raza = ?, edad = ?, sexo = ?, descripcion = ?, estado = ?, id_centro = ?, imagen_url = ? WHERE id_animal = ?");
                $stmt->execute([$nombre, $especie, $raza, $edad, $sexo, $descripcion, $estado, $id_centro, $imagen_url, $id]);
                $mensaje = 'Animal actualizado correctamente';
            } else {
                $fecha_ingreso = date('Y-m-d');
                $stmt = $pdo->prepare("INSERT INTO animales (nombre, especie, raza, edad, sexo, descripcion, estado, id_centro, imagen_url, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $especie, $raza, $edad, $sexo, $descripcion, $estado, $id_centro, $imagen_url, $fecha_ingreso]);
                $mensaje = 'Animal creado correctamente';
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['eliminar_animal'])) {
        $id_eliminar = (int)$_POST['id_animal'];
        $stmt = $pdo->prepare("DELETE FROM animales WHERE id_animal = ?");
        $stmt->execute([$id_eliminar]);
        $mensaje = 'Animal eliminado correctamente';
    }
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 12;
$inicio = ($pagina - 1) * $por_pagina;
$where = [];
$params = [];

if (!empty($_GET['especie'])) {
    $where[] = "a.especie = ?";
    $params[] = $_GET['especie'];
}
if (!empty($_GET['estado'])) {
    $where[] = "a.estado = ?";
    $params[] = $_GET['estado'];
}
if (!empty($_GET['centro'])) {
    $where[] = "a.id_centro = ?";
    $params[] = (int)$_GET['centro'];
}
if (!empty($_GET['busqueda'])) {
    $where[] = "(a.nombre LIKE ? OR a.raza LIKE ?)";
    $search = '%' . $_GET['busqueda'] . '%';
    $params[] = $search;
    $params[] = $search;
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$count_sql = "SELECT COUNT(*) FROM animales a $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_animales = $count_stmt->fetchColumn();
$total_paginas = ceil($total_animales / $por_pagina);

$sql = "SELECT a.*, c.nombre as centro_nombre FROM animales a LEFT JOIN centros c ON a.id_centro = c.id_centro $where_clause ORDER BY a.fecha_ingreso DESC LIMIT :inicio, :por_pagina";
$stmt = $pdo->prepare($sql);
foreach ($params as $i => $param) $stmt->bindValue($i + 1, $param);
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$animales = $stmt->fetchAll();

$centros = $pdo->query("SELECT * FROM centros ORDER BY nombre")->fetchAll();
$animal_editar = null;
if ($accion == 'editar' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM animales WHERE id_animal = ?");
    $stmt->execute([$id]);
    $animal_editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Animales - Admin</title>
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
                <h2><i class="fas fa-paw me-2"></i>Gestión de Animales</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAnimal"><i class="fas fa-plus me-2"></i>Nuevo Animal</button>
            </div>
            <?php if ($mensaje): ?><div class="alert alert-success alert-dismissible fade show"><?= $mensaje ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <div class="row mb-4">
                <div class="col-md-3"><div class="card border-success"><div class="card-body text-center"><h3 class="text-success mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales")->fetchColumn() ?></h3><p class="text-muted mb-0">Total animales</p></div></div></div>
                <div class="col-md-3"><div class="card border-success"><div class="card-body text-center"><h3 class="text-success mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Disponible'")->fetchColumn() ?></h3><p class="text-muted mb-0">Disponibles</p></div></div></div>
                <div class="col-md-3"><div class="card border-warning"><div class="card-body text-center"><h3 class="text-warning mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Reservado'")->fetchColumn() ?></h3><p class="text-muted mb-0">Reservados</p></div></div></div>
                <div class="col-md-3"><div class="card border-info"><div class="card-body text-center"><h3 class="text-info mb-1"><?= $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Adoptado'")->fetchColumn() ?></h3><p class="text-muted mb-0">Adoptados</p></div></div></div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-light"><h6 class="mb-0">Filtros de búsqueda</h6></div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3"><input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre o raza" value="<?= $_GET['busqueda'] ?? '' ?>"></div>
                        <div class="col-md-2"><select class="form-select" name="especie"><option value="">Todas las especies</option><option value="Perro" <?= ($_GET['especie'] ?? '') == 'Perro' ? 'selected' : '' ?>>Perro</option><option value="Gato" <?= ($_GET['especie'] ?? '') == 'Gato' ? 'selected' : '' ?>>Gato</option><option value="Otro" <?= ($_GET['especie'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option></select></div>
                        <div class="col-md-2"><select class="form-select" name="estado"><option value="">Todos los estados</option><option value="Disponible" <?= ($_GET['estado'] ?? '') == 'Disponible' ? 'selected' : '' ?>>Disponible</option><option value="Reservado" <?= ($_GET['estado'] ?? '') == 'Reservado' ? 'selected' : '' ?>>Reservado</option><option value="Adoptado" <?= ($_GET['estado'] ?? '') == 'Adoptado' ? 'selected' : '' ?>>Adoptado</option><option value="En tratamiento" <?= ($_GET['estado'] ?? '') == 'En tratamiento' ? 'selected' : '' ?>>En tratamiento</option></select></div>
                        <div class="col-md-3"><select class="form-select" name="centro"><option value="">Todos los centros</option><?php foreach ($centros as $centro): ?><option value="<?= $centro['id_centro'] ?>" <?= ($_GET['centro'] ?? '') == $centro['id_centro'] ? 'selected' : '' ?>><?= htmlspecialchars($centro['nombre']) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Buscar</button></div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center"><h5 class="mb-0">Listado de Animales</h5><span class="badge bg-success">Mostrando <?= count($animales) ?> de <?= $total_animales ?></span></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light"><tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Especie/Raza</th><th>Edad/Sexo</th><th>Estado</th><th>Centro</th><th>Ingreso</th><th class="text-center">Acciones</th></tr></thead>
                            <tbody>
                                <?php foreach ($animales as $animal): $imagen = $animal['imagen_url'] ? '../' . ltrim($animal['imagen_url'], './') : '../img/animales/default.jpg'; ?>
                                <tr>
                                    <td><?= $animal['id_animal'] ?></td>
                                    <td><img src="<?= $imagen ?>" class="img-thumbnail" style="width:60px;height:60px;object-fit:cover;" onerror="this.src='../img/animales/default.jpg'"></td>
                                    <td><strong><?= htmlspecialchars($animal['nombre']) ?></strong></td>
                                    <td><small class="text-muted"><?= $animal['especie'] ?></small><br><small><?= $animal['raza'] ?: 'Mestizo' ?></small></td>
                                    <td><?= $animal['edad'] ?> años<br><small><?= $animal['sexo'] ?></small></td>
                                    <td><span class="badge bg-<?= ['Disponible'=>'success','Reservado'=>'warning','Adoptado'=>'info','En tratamiento'=>'danger'][$animal['estado']] ?? 'secondary' ?>"><?= $animal['estado'] ?></span></td>
                                    <td><small><?= htmlspecialchars($animal['centro_nombre']) ?></small></td>
                                    <td><small><?= date('d/m/Y', strtotime($animal['fecha_ingreso'])) ?></small></td>
                                    <td class="text-center">
                                        <a href="../animales/ver.php?id=<?= $animal['id_animal'] ?>" class="btn btn-sm btn-info me-1" target="_blank"><i class="fas fa-eye"></i></a>
                                        <a href="animales.php?accion=editar&id=<?= $animal['id_animal'] ?>" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalAnimal"><i class="fas fa-edit"></i></a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')"><input type="hidden" name="id_animal" value="<?= $animal['id_animal'] ?>"><button type="submit" name="eliminar_animal" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($total_paginas > 1): ?>
                    <nav class="mt-4"><ul class="pagination justify-content-center">
                        <?php if ($pagina > 1): ?><li class="page-item"><a class="page-link" href="?pagina=<?= $pagina - 1 ?>&<?= http_build_query($_GET) ?>"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?><li class="page-item <?= $i == $pagina ? 'active' : '' ?>"><a class="page-link" href="?pagina=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a></li><?php endfor; ?>
                        <?php if ($pagina < $total_paginas): ?><li class="page-item"><a class="page-link" href="?pagina=<?= $pagina + 1 ?>&<?= http_build_query($_GET) ?>"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
                    </ul></nav><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAnimal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white"><h5 class="modal-title">Animal</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_animal" value="<?= $animal_editar['id_animal'] ?? 0 ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Nombre *</label><input type="text" class="form-control" name="nombre" value="<?= $animal_editar['nombre'] ?? '' ?>" required></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Especie *</label><select class="form-select" name="especie" required><option value="Perro" <?= ($animal_editar['especie'] ?? '') == 'Perro' ? 'selected' : '' ?>>Perro</option><option value="Gato" <?= ($animal_editar['especie'] ?? '') == 'Gato' ? 'selected' : '' ?>>Gato</option><option value="Otro" <?= ($animal_editar['especie'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option></select></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Raza</label><input type="text" class="form-control" name="raza" value="<?= $animal_editar['raza'] ?? '' ?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Edad (años) *</label><input type="number" class="form-control" name="edad" min="0" max="30" value="<?= $animal_editar['edad'] ?? 1 ?>" required></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Sexo *</label><select class="form-select" name="sexo" required><option value="Macho" <?= ($animal_editar['sexo'] ?? '') == 'Macho' ? 'selected' : '' ?>>Macho</option><option value="Hembra" <?= ($animal_editar['sexo'] ?? '') == 'Hembra' ? 'selected' : '' ?>>Hembra</option></select></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Estado *</label><select class="form-select" name="estado" required><option value="Disponible" <?= ($animal_editar['estado'] ?? '') == 'Disponible' ? 'selected' : '' ?>>Disponible</option><option value="Reservado" <?= ($animal_editar['estado'] ?? '') == 'Reservado' ? 'selected' : '' ?>>Reservado</option><option value="Adoptado" <?= ($animal_editar['estado'] ?? '') == 'Adoptado' ? 'selected' : '' ?>>Adoptado</option><option value="En tratamiento" <?= ($animal_editar['estado'] ?? '') == 'En tratamiento' ? 'selected' : '' ?>>En tratamiento</option></select></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Centro *</label><select class="form-select" name="id_centro" required><?php foreach ($centros as $centro): ?><option value="<?= $centro['id_centro'] ?>" <?= ($animal_editar['id_centro'] ?? '') == $centro['id_centro'] ? 'selected' : '' ?>><?= htmlspecialchars($centro['nombre']) ?></option><?php endforeach; ?></select></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" rows="3"><?= $animal_editar['descripcion'] ?? '' ?></textarea></div>
                        <div class="mb-3"><label class="form-label">Imagen</label><input type="file" class="form-control" name="imagen" accept="image/*"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" name="guardar_animal" class="btn btn-success">Guardar</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
</html>