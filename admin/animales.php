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


if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

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
        $id_animal = (int)($_POST['id_animal'] ?? 0);

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $upload_dir = '../img/animales/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['imagen']['name']);
            $file_path = $upload_dir . $file_name;
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = mime_content_type($_FILES['imagen']['tmp_name']);
            
            if (in_array($file_type, $allowed_types) && move_uploaded_file($_FILES['imagen']['tmp_name'], $file_path)) {
               
                if (!empty($_POST['imagen_actual']) && $imagen_url != $_POST['imagen_actual']) {
                    $old_path = '../' . ltrim($_POST['imagen_actual'], './');
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $imagen_url = './img/animales/' . $file_name;
            }
        }

        try {
            if ($id_animal > 0) {
                $stmt = $pdo->prepare("UPDATE animales SET nombre = ?, especie = ?, raza = ?, edad = ?, sexo = ?, descripcion = ?, estado = ?, id_centro = ?, imagen_url = ? WHERE id_animal = ?");
                $stmt->execute([$nombre, $especie, $raza, $edad, $sexo, $descripcion, $estado, $id_centro, $imagen_url, $id_animal]);
                $mensaje = 'Animal actualizado correctamente';
            } else {
                $fecha_ingreso = date('Y-m-d');
                $stmt = $pdo->prepare("INSERT INTO animales (nombre, especie, raza, edad, sexo, descripcion, estado, id_centro, imagen_url, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $especie, $raza, $edad, $sexo, $descripcion, $estado, $id_centro, $imagen_url, $fecha_ingreso]);
                $id_animal = $pdo->lastInsertId();
                $mensaje = 'Animal creado correctamente';
            }

            
            $vacunas_seleccionadas = isset($_POST['vacunas']) ? array_map('intval', $_POST['vacunas']) : [];
            
           
            $pdo->prepare("DELETE FROM animal_vacunas WHERE id_animal = ?")->execute([$id_animal]);
            
           
            if (!empty($vacunas_seleccionadas)) {
                $stmt = $pdo->prepare("INSERT INTO animal_vacunas (id_animal, id_vacuna, fecha_aplicacion, fecha_proxima) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR))");
                foreach ($vacunas_seleccionadas as $id_vacuna) {
                    if ($id_vacuna > 0) {
                        $stmt->execute([$id_animal, $id_vacuna]);
                    }
                }
            }
            
           
            header('Location: animales.php?mensaje=' . urlencode($mensaje));
            exit();
            
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
            header('Location: animales.php?error=' . urlencode($error));
            exit();
        }
    } elseif (isset($_POST['eliminar_animal'])) {
        $id_eliminar = (int)$_POST['id_animal'];
        try {
            $pdo->beginTransaction();
            
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM adopciones WHERE id_animal = ?");
            $stmt->execute([$id_eliminar]);
            $tiene_adopciones = $stmt->fetchColumn();
            
            if ($tiene_adopciones > 0) {
                $error = 'No se puede eliminar: el animal tiene adopciones asociadas.';
            } else {
              
                $pdo->prepare("DELETE FROM animal_vacunas WHERE id_animal = ?")->execute([$id_eliminar]);
                
              
                $stmt = $pdo->prepare("DELETE FROM animales WHERE id_animal = ?");
                $stmt->execute([$id_eliminar]);
                
                $pdo->commit();
                $mensaje = 'Animal eliminado correctamente';
            }
            
            if ($mensaje) {
                header('Location: animales.php?mensaje=' . urlencode($mensaje));
                exit();
            } elseif ($error) {
                header('Location: animales.php?error=' . urlencode($error));
                exit();
            }
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Error: ' . $e->getMessage();
            header('Location: animales.php?error=' . urlencode($error));
            exit();
        }
    }
}


if ($accion === 'buscar') {
    $pagina = $_GET['pagina'] ?? 1;
    $busqueda = $_GET['busqueda'] ?? '';
    $especie = $_GET['especie'] ?? '';
    $estado = $_GET['estado'] ?? '';
    $centro = $_GET['centro'] ?? '';

    $limite = 10;
    $offset = ($pagina - 1) * $limite;

    $where = ["1=1"];
    $params = [];
    
    if ($busqueda) {
        $where[] = "(a.nombre LIKE ? OR a.raza LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    if ($especie) {
        $where[] = "a.especie = ?";
        $params[] = $especie;
    }
    if ($estado) {
        $where[] = "a.estado = ?";
        $params[] = $estado;
    }
    if ($centro) {
        $where[] = "a.id_centro = ?";
        $params[] = $centro;
    }

    $where_clause = implode(' AND ', $where);

   
    $sql_count = "SELECT COUNT(*) AS total 
                  FROM animales a
                  LEFT JOIN centros c ON a.id_centro = c.id_centro
                  WHERE $where_clause";
    $stmt = $pdo->prepare($sql_count);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    $totalPaginas = ceil($total / $limite);

    
    $sql_data = "SELECT a.*, c.nombre as centro_nombre 
                 FROM animales a
                 LEFT JOIN centros c ON a.id_centro = c.id_centro
                 WHERE $where_clause 
                 ORDER BY a.id_animal DESC 
                 LIMIT $offset, $limite";
    $stmt = $pdo->prepare($sql_data);
    $stmt->execute($params);
    $animales = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode([
        'animales' => $animales,
        'total' => $total,
        'totalPaginas' => $totalPaginas
    ]);
    exit();
}

$centros = $pdo->query("SELECT * FROM centros ORDER BY nombre")->fetchAll();
$animal_editar = null;
$vacunas_animal = [];

if ($accion === 'editar' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM animales WHERE id_animal = ?");
    $stmt->execute([$id]);
    $animal_editar = $stmt->fetch();
    
    if (!$animal_editar) {
        $error = 'Animal no encontrado.';
        $accion = 'listar';
    } else {
      
        $stmt = $pdo->prepare("SELECT id_vacuna FROM animal_vacunas WHERE id_animal = ?");
        $stmt->execute([$id]);
        $vacunas_animal = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Animales - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .img-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            margin-top: 10px;
        }
        .modal-show {
            display: block !important;
            background-color: rgba(0,0,0,0.5);
        }
        .vacunas-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 5px;
        }
        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }
        .badge-disponible { background-color: #28a745; }
        .badge-reservado { background-color: #ffc107; color: #212529; }
        .badge-adoptado { background-color: #17a2b8; }
        .badge-tratamiento { background-color: #dc3545; }
    </style>
</head>
<body <?php if ($accion === 'editar' || $accion === 'nuevo'): ?>class="modal-open"<?php endif; ?>>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-paw me-2"></i>Gestión de Animales</h2>
                <a href="animales.php?accion=nuevo" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nuevo Animal
                </a>
            </div>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($accion === 'listar'): ?>
            <div class="card mb-4">
                <div class="card-header bg-light"><h6 class="mb-0">Filtros de búsqueda</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="busqueda" placeholder="Buscar por nombre o raza">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="especie">
                                <option value="">Todas las especies</option>
                                <option value="Perro">Perro</option>
                                <option value="Gato">Gato</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="estado">
                                <option value="">Todos los estados</option>
                                <option value="Disponible">Disponible</option>
                                <option value="Reservado">Reservado</option>
                                <option value="Adoptado">Adoptado</option>
                                <option value="En tratamiento">En tratamiento</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="centro">
                                <option value="">Todos los centros</option>
                                <?php foreach ($centros as $centro_item): ?>
                                    <option value="<?= $centro_item['id_centro'] ?>"><?= htmlspecialchars($centro_item['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listado de Animales</h5>
                    <span class="badge bg-success" id="resultado-count">Cargando...</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="resultados-tabla"></div>
                    <nav id="paginacion-container" class="d-none mt-4">
                        <ul class="pagination justify-content-center" id="paginacion"></ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php if ($accion === 'editar' || $accion === 'nuevo'): ?>
<div class="modal fade show modal-show" tabindex="-1" style="display: block;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-<?= $accion === 'editar' ? 'warning' : 'success' ?> text-white">
                <h5 class="modal-title">
                    <?= $accion === 'editar' ? 'Editar Animal' : 'Nuevo Animal' ?>
                </h5>
                <a href="animales.php" class="btn-close btn-close-white"></a>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_animal" value="<?= $animal_editar['id_animal'] ?? 0 ?>">
                    <input type="hidden" name="imagen_actual" id="imagen_actual" value="<?= htmlspecialchars($animal_editar['imagen_url'] ?? '') ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="nombre" 
                                   value="<?= htmlspecialchars($animal_editar['nombre'] ?? '') ?>" required>
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
                                   value="<?= htmlspecialchars($animal_editar['raza'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Edad (años) *</label>
                            <input type="number" class="form-control" name="edad" min="0" max="30" 
                                   value="<?= $animal_editar['edad'] ?? 1 ?>" required>
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
                                <?php foreach ($centros as $centro_item): ?>
                                    <option value="<?= $centro_item['id_centro'] ?>" 
                                        <?= ($animal_editar['id_centro'] ?? '') == $centro_item['id_centro'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($centro_item['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"><?= htmlspecialchars($animal_editar['descripcion'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" class="form-control" name="imagen" accept="image/*" id="inputImagen">
                        
                        <?php if (!empty($animal_editar['imagen_url'])): ?>
                            <div class="mt-2">
                                <img src="../<?= ltrim($animal_editar['imagen_url'], './') ?>" 
                                     alt="Imagen actual" 
                                     class="img-thumbnail img-preview" id="currentImage">
                                <p class="text-muted small mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
                            </div>
                        <?php else: ?>
                            <div id="previewContainer"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Vacunas aplicadas</label>
                        
                        <?php
                        $vacunas = $pdo->query("SELECT * FROM vacunas ORDER BY nombre")->fetchAll();
                        ?>
                        
                        <div class="vacunas-container">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllVacunas">
                                <label class="form-check-label fw-bold" for="selectAllVacunas">
                                    Seleccionar todas / Ninguna
                                </label>
                            </div>
                            
                            <?php foreach ($vacunas as $v): 
                                $checked = in_array($v['id_vacuna'], $vacunas_animal) ? 'checked' : '';
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input vacuna-checkbox" type="checkbox" 
                                           name="vacunas[]" value="<?= $v['id_vacuna'] ?>" 
                                           id="vacuna_<?= $v['id_vacuna'] ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="vacuna_<?= $v['id_vacuna'] ?>">
                                        <?= htmlspecialchars($v['nombre']) ?> 
                                        <span class="text-muted">(<?= number_format($v['precio'], 2) ?> €)</span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <small class="text-muted">
                            Selecciona las vacunas que ha recibido el animal. 
                            <?php if ($accion === 'editar'): ?>
                                Deja en blanco para eliminar todas las vacunas.
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <a href="animales.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="guardar_animal" class="btn btn-<?= $accion === 'editar' ? 'warning' : 'success' ?>">
                        <?= $accion === 'editar' ? 'Actualizar' : 'Crear' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($accion === 'listar'): ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/animales.js"></script>
<?php else: ?>
<script>

document.addEventListener('DOMContentLoaded', function() {
    
    const inputImagen = document.getElementById('inputImagen');
    if (inputImagen) {
        inputImagen.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('previewContainer');
            const currentImage = document.getElementById('currentImage');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (currentImage) {
                        
                        currentImage.src = e.target.result;
                    } else if (previewContainer) {
                        
                        previewContainer.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail img-preview';
                        previewContainer.appendChild(img);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    
    const selectAllCheckbox = document.getElementById('selectAllVacunas');
    if (selectAllCheckbox) {
        const vacunaCheckboxes = document.querySelectorAll('.vacuna-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            vacunaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
    
        vacunaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(vacunaCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(vacunaCheckboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
        
        
        const allChecked = Array.from(vacunaCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(vacunaCheckboxes).some(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }
});
</script>
<?php endif; ?>
</body>
</html>