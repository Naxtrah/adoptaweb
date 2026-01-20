<?php
require_once '../includes/config.php';

$accion = $_GET['accion'] ?? 'listar';

//paginación
$pagina = intval($_GET['pagina'] ?? 1);
<<<<<<< HEAD

//Número de animales a mostrar por página
$porPagina = 8;

//Generar formulario de filtros
if ($accion === 'filtros') {
    header('Content-Type: text/html');
    
    //Consultar datos para los filtros desde la base de datos
=======
$porPagina = 8;

if ($accion === 'filtros') {
    header('Content-Type: text/html');
    
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
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
    
<<<<<<< HEAD
    //Filtro de sexo
=======
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
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
    
<<<<<<< HEAD
    //Filtro de edad
=======
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
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
    
<<<<<<< HEAD
    //Filtro de centro
=======
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Centro</label>';
    echo '<select class="form-select" name="centro">';
    echo '<option value="">Todos los centros</option>';
    foreach ($centros as $centro) {
        echo '<option value="' . $centro['id_centro'] . '">' . htmlspecialchars($centro['nombre']) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    
<<<<<<< HEAD
    //Filtro de estado
=======
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
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
<<<<<<< HEAD
    exit; //Terminar ejecución después de enviar el HTML de filtros
}

// json para listar animales con filtro

header('Content-Type: application/json');

//Consulta base para obtener animales
=======
    exit;
}


header('Content-Type: application/json');

>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
$sql = "SELECT SQL_CALC_FOUND_ROWS a.*, c.nombre as centro_nombre 
        FROM animales a 
        LEFT JOIN centros c ON a.id_centro = c.id_centro 
        WHERE 1=1";
$params = [];

if (!empty($_GET['busqueda'])) {
    $sql .= " AND (a.nombre LIKE ? OR a.especie LIKE ? OR a.raza LIKE ?)";
    $searchTerm = '%' . $_GET['busqueda'] . '%';
<<<<<<< HEAD
    $params[] = $searchTerm; 
    $params[] = $searchTerm; 
    $params[] = $searchTerm; 
=======
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
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
    $params[] = intval($_GET['edad_max']); // Convertir a entero por seguridad
}

if (!empty($_GET['centro'])) {
    $sql .= " AND a.id_centro = ?";
    $params[] = intval($_GET['centro']); // Convertir a entero por seguridad
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
    //Si no se especifica estado, mostrar solo disponibles y reservados
    $sql .= " AND a.estado IN ('Disponible', 'Reservado')";
}

<<<<<<< HEAD
//Ordenar por fecha de ingreso (más recientes primero)
$sql .= " ORDER BY a.fecha_ingreso DESC";

//Aplicar límites para paginación
$offset = ($pagina - 1) * $porPagina;
$sql .= " LIMIT $offset, $porPagina";
=======
$sql .= " ORDER BY a.fecha_ingreso DESC";
$sql .= " LIMIT " . (($pagina - 1) * $porPagina) . ", $porPagina";
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animales = $stmt->fetchAll();

<<<<<<< HEAD
foreach ($animales as &$animal) {
    //Verificar si tiene imagen y no es URL absoluta (http/https)
    if ($animal['imagen_url'] && !str_starts_with($animal['imagen_url'], 'http')) {
        //Ajustar rutas relativas
        if (str_starts_with($animal['imagen_url'], './img/')) {
            //Convertir ./img/ a ../img/ (un nivel arriba)
            $animal['imagen_url'] = '../' . substr($animal['imagen_url'], 2);
        } elseif (!str_starts_with($animal['imagen_url'], '../')) {
            //Si no tiene prefijo, asumir que está en img/animales/
=======

foreach ($animales as &$animal) {
    if ($animal['imagen_url'] && !str_starts_with($animal['imagen_url'], 'http')) {
        if (str_starts_with($animal['imagen_url'], './img/')) {
            $animal['imagen_url'] = '../' . substr($animal['imagen_url'], 2);
        } elseif (!str_starts_with($animal['imagen_url'], '../')) {
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
            $animal['imagen_url'] = '../img/animales/' . basename($animal['imagen_url']);
        }
    }
}

$totalStmt = $pdo->query("SELECT FOUND_ROWS()");
$total = $totalStmt->fetchColumn(); // Total de animales que cumplen filtros

$totalPaginas = ceil($total / $porPagina);

echo json_encode([
<<<<<<< HEAD
    'animales' => $animales,          
    'total' => $total,                
    'totalPaginas' => $totalPaginas,  
    'paginaActual' => $pagina        
]);
?>
=======
    'animales' => $animales,
    'total' => $total,
    'totalPaginas' => $totalPaginas,
    'paginaActual' => $pagina
]);
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
