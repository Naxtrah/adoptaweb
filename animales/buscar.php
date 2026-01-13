<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
$accion = $_GET['accion'] ?? 'listar';
$pagina = intval($_GET['pagina'] ?? 1);
$porPagina = 6;
//filtros
if ($accion === 'filtros') {
    header('Content-Type: text/html');
    //Obtener opciones únicas de BD
    $especies = $pdo->query("SELECT DISTINCT especie FROM animales WHERE especie IS NOT NULL")->fetchAll();
    $razas = $pdo->query("SELECT DISTINCT raza FROM animales WHERE raza IS NOT NULL AND raza != ''")->fetchAll();
    $centros = $pdo->query("SELECT id_centro, nombre FROM centros")->fetchAll();
    echo '<form id="filtros-form">';
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Especie</label>';
    foreach ($especies as $especie) {
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" name="especie[]" value="' . htmlspecialchars($especie['especie']) . '" id="especie-' . urlencode($especie['especie']) . '">';
        echo '<label class="form-check-label" for="especie-' . urlencode($especie['especie']) . '">' . htmlspecialchars($especie['especie']) . '</label>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Sexo</label>';
    echo '<div class="form-check">';
    echo '<input class="form-check-input" type="checkbox" name="sexo[]" value="Macho" id="sexo-macho">';
    echo '<label class="form-check-label" for="sexo-macho">Macho</label>';
    echo '</div>';
    echo '<div class="form-check">';
    echo '<input class="form-check-input" type="checkbox" name="sexo[]" value="Hembra" id="sexo-hembra">';
    echo '<label class="form-check-label" for="sexo-hembra">Hembra</label>';
    echo '</div>';
    echo '</div>';
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Edad</label>';
    echo '<select class="form-select" name="edad_max">';
    echo '<option value="">Cualquier edad</option>';
    echo '<option value="1">Menos de 1 año</option>';
    echo '<option value="3">Menos de 3 años</option>';
    echo '<option value="5">Menos de 5 años</option>';
    echo '<option value="10">Menos de 10 años</option>';
    echo '</select>';
    echo '</div>';
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Centro</label>';
    echo '<select class="form-select" name="centro">';
    echo '<option value="">Todos los centros</option>';
    foreach ($centros as $centro) {
        echo '<option value="' . $centro['id_centro'] . '">' . htmlspecialchars($centro['nombre']) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Estado</label>';
    echo '<div class="form-check">';
    echo '<input class="form-check-input" type="checkbox" name="estado[]" value="Disponible" id="estado-disponible" checked>';
    echo '<label class="form-check-label" for="estado-disponible">Disponible</label>';
    echo '</div>';
    echo '<div class="form-check">';
    echo '<input class="form-check-input" type="checkbox" name="estado[]" value="Reservado" id="estado-reservado">';
    echo '<label class="form-check-label" for="estado-reservado">Reservado</label>';
    echo '</div>';
    echo '</div>';
    echo '<button type="button" class="btn btn-success w-100" onclick="aplicarFiltros()">';
    echo '<i class="fas fa-filter me-1"></i>Aplicar Filtros';
    echo '</button>';
    echo '<button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="limpiarFiltros()">';
    echo '<i class="fas fa-times me-1"></i>Limpiar Filtros';
    echo '</button>';
    echo '</form>';
    echo '<script>
    function aplicarFiltros() {
        const form = document.getElementById("filtros-form");
        const formData = new FormData(form);
        filters = Object.fromEntries(formData.entries());
        currentPage = 1;
        cargarAnimales();
    }
    function limpiarFiltros() {
        document.getElementById("filtros-form").reset();
        filters = {};
        currentPage = 1;
        cargarAnimales();
    }
    </script>';
    exit;
}
//detalles de animal
if ($accion === 'detalle') {
    header('Content-Type: text/html');
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("
        SELECT a.*, c.nombre as centro_nombre, c.direccion, c.telefono, c.email
        FROM animales a
        LEFT JOIN centros c ON a.id_centro = c.id_centro
        WHERE a.id_animal = ?
    ");
    $stmt->execute([$id]);
    $animal = $stmt->fetch();
    if ($animal) {
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<img src="' . ($animal['imagen_url'] ?: BASE_URL . '/assets/img/default-animal.jpg') . '" 
                   class="img-fluid rounded" alt="' . htmlspecialchars($animal['nombre']) . '">';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<h4>' . htmlspecialchars($animal['nombre']) . '</h4>';
        echo '<p><strong>Especie:</strong> ' . htmlspecialchars($animal['especie']) . '</p>';
        echo '<p><strong>Raza:</strong> ' . ($animal['raza'] ? htmlspecialchars($animal['raza']) : 'Mestizo') . '</p>';
        echo '<p><strong>Edad:</strong> ' . $animal['edad'] . ' años</p>';
        echo '<p><strong>Sexo:</strong> ' . $animal['sexo'] . '</p>';
        echo '<p><strong>Estado:</strong> <span class="badge bg-' . ($animal['estado'] === 'Disponible' ? 'success' : 'warning') . '">' . $animal['estado'] . '</span></p>';
        echo '<p><strong>Centro:</strong> ' . htmlspecialchars($animal['centro_nombre']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="row mt-3">';
        echo '<div class="col-12">';
        echo '<h5>Descripción</h5>';
        echo '<p>' . nl2br(htmlspecialchars($animal['descripcion'])) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="row mt-3">';
        echo '<div class="col-12">';
        echo '<h5>Información del centro</h5>';
        echo '<p><strong>Dirección:</strong> ' . htmlspecialchars($animal['direccion']) . '</p>';
        echo '<p><strong>Teléfono:</strong> ' . htmlspecialchars($animal['telefono']) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($animal['email']) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="mt-3 text-center">';
        echo '<a href="' . BASE_URL . '/animales/detalle.php?id=' . $id . '" class="btn btn-success me-2">Ver ficha completa</a>';
        if ($animal['estado'] === 'Disponible') {
            echo '<a href="' . BASE_URL . '/animales/adoptar.php?id=' . $id . '" class="btn btn-warning">Solicitar adopción</a>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-danger">Animal no encontrado</div>';
    }
    exit;
}
//listar animales con filtros
$sql = "SELECT SQL_CALC_FOUND_ROWS a.*, c.nombre as centro_nombre 
        FROM animales a 
        LEFT JOIN centros c ON a.id_centro = c.id_centro 
        WHERE 1=1";
$params = [];
//Aplicar filtros
if (!empty($_GET['busqueda'])) {
    $sql .= " AND (a.nombre LIKE ? OR a.especie LIKE ? OR a.descripcion LIKE ?)";
    $searchTerm = '%' . $_GET['busqueda'] . '%';
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}
if (!empty($_GET['especie'])) {
    if (is_array($_GET['especie'])) {
        $placeholders = str_repeat('?,', count($_GET['especie']) - 1) . '?';
        $sql .= " AND a.especie IN ($placeholders)";
        $params = array_merge($params, $_GET['especie']);
    } else {
        $sql .= " AND a.especie = ?";
        $params[] = $_GET['especie'];
    }
}
if (!empty($_GET['sexo'])) {
    if (is_array($_GET['sexo'])) {
        $placeholders = str_repeat('?,', count($_GET['sexo']) - 1) . '?';
        $sql .= " AND a.sexo IN ($placeholders)";
        $params = array_merge($params, $_GET['sexo']);
    } else {
        $sql .= " AND a.sexo = ?";
        $params[] = $_GET['sexo'];
    }
}
if (!empty($_GET['edad_max'])) {
    $sql .= " AND a.edad <= ?";
    $params[] = intval($_GET['edad_max']);
}
if (!empty($_GET['centro'])) {
    $sql .= " AND a.id_centro = ?";
    $params[] = intval($_GET['centro']);
}
if (!empty($_GET['estado'])) {
    if (is_array($_GET['estado'])) {
        $placeholders = str_repeat('?,', count($_GET['estado']) - 1) . '?';
        $sql .= " AND a.estado IN ($placeholders)";
        $params = array_merge($params, $_GET['estado']);
    } else {
        $sql .= " AND a.estado = ?";
        $params[] = $_GET['estado'];
    }
} else {
    $sql .= " AND a.estado IN ('Disponible', 'Reservado')";
}
$sql .= " ORDER BY a.fecha_ingreso DESC";
$sql .= " LIMIT " . (($pagina - 1) * $porPagina) . ", $porPagina";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animales = $stmt->fetchAll();
$totalStmt = $pdo->query("SELECT FOUND_ROWS()");
$total = $totalStmt->fetchColumn();
$totalPaginas = ceil($total / $porPagina);
echo json_encode([
    'animales' => $animales,
    'total' => $total,
    'totalPaginas' => $totalPaginas,
    'paginaActual' => $pagina
]);
