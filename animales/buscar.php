<?php
//Iniciar buffer de salida
ob_start();
require_once '../includes/config.php';

//Determinar acción solicitada
$accion = $_GET['accion'] ?? 'listar';
$pagina = intval($_GET['pagina'] ?? 1);
$porPagina = 8;

//Devolver HTML de filtros
if ($accion === 'filtros') {
    //Limpiar buffer y establecer tipo de contenido
    ob_end_clean();
    header('Content-Type: text/html');
    
    //Consultar opciones para filtros
    $especies = $pdo->query("SELECT DISTINCT especie FROM animales WHERE especie IS NOT NULL")->fetchAll();
    $razas = $pdo->query("SELECT DISTINCT raza FROM animales WHERE raza IS NOT NULL AND raza != ''")->fetchAll();
    $centros = $pdo->query("SELECT id_centro, nombre FROM centros")->fetchAll();
    
    //Generar formulario de filtros
    echo '<div id="filtros-form">';
    
    //Filtro de especies
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Especie</label>';
    foreach ($especies as $especie) {
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" name="especie[]" value="' . htmlspecialchars($especie['especie']) . '" id="especie-' . urlencode($especie['especie']) . '">';
        echo '<label class="form-check-label" for="especie-' . urlencode($especie['especie']) . '">' . htmlspecialchars($especie['especie']) . '</label>';
        echo '</div>';
    }
    echo '</div>';
    
    //Filtro de sexo
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
    
    //Filtro de edad máxima
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Edad</label>';
    echo '<select class="form-select" name="edad_max" id="edad_max">';
    echo '<option value="">Cualquier edad</option>';
    echo '<option value="1">Menos de 1 año</option>';
    echo '<option value="3">Menos de 3 años</option>';
    echo '<option value="5">Menos de 5 años</option>';
    echo '<option value="10">Menos de 10 años</option>';
    echo '</select>';
    echo '</div>';
    
    //Filtro de centro
    echo '<div class="mb-3">';
    echo '<label class="form-label fw-bold">Centro</label>';
    echo '<select class="form-select" name="centro" id="centro">';
    echo '<option value="">Todos los centros</option>';
    foreach ($centros as $centro) {
        echo '<option value="' . $centro['id_centro'] . '">' . htmlspecialchars($centro['nombre']) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    
    //Filtro de estado
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
    
    //Botón para limpiar filtros
    echo '<button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">';
    echo '<i class="fas fa-times me-1"></i>Limpiar Filtros';
    echo '</button>';
    echo '</div>';
    exit;
}

//Listar animales con filtros aplicados
ob_end_clean();
header('Content-Type: application/json');

try {
    //Construir consulta SQL dinámica con filtros
    $sql = "SELECT SQL_CALC_FOUND_ROWS a.*, c.nombre as centro_nombre 
            FROM animales a 
            LEFT JOIN centros c ON a.id_centro = c.id_centro 
            WHERE 1=1";
    $params = [];

    //Filtro de búsqueda por texto
    if (!empty($_GET['busqueda'])) {
        $sql .= " AND (a.nombre LIKE ? OR a.especie LIKE ? OR a.raza LIKE ?)";
        $searchTerm = '%' . $_GET['busqueda'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    //Filtro de especie
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

    //Filtro de sexo
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

    //Filtro de edad máxima
    if (!empty($_GET['edad_max'])) {
        $sql .= " AND a.edad <= ?";
        $params[] = intval($_GET['edad_max']);
    }

    //Filtro de centro
    if (!empty($_GET['centro'])) {
        $sql .= " AND a.id_centro = ?";
        $params[] = intval($_GET['centro']);
    }

    //Filtro de estado
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
        //Estado por defecto si no se especifica
        $sql .= " AND a.estado = 'Disponible'";
    }

    //Ordenar y paginar
    $sql .= " ORDER BY a.fecha_ingreso DESC";
    $sql .= " LIMIT " . (($pagina - 1) * $porPagina) . ", $porPagina";

    //Ejecutar consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $animales = $stmt->fetchAll();

    //Procesar URLs de imágenes
    foreach ($animales as &$animal) {
        if ($animal['imagen_url']) {
            if (strpos($animal['imagen_url'], './img/') === 0) {
                $animal['imagen_url'] = '../' . substr($animal['imagen_url'], 2);
            }
            elseif (strpos($animal['imagen_url'], 'http') !== 0) {
                if (strpos($animal['imagen_url'], '../') !== 0) {
                    $animal['imagen_url'] = '../img/animales/' . basename($animal['imagen_url']);
                }
            }
        } else {
            $animal['imagen_url'] = '../img/animales/default.jpg';
        }
    }

    //Calcular paginación
    $totalStmt = $pdo->query("SELECT FOUND_ROWS()");
    $total = $totalStmt->fetchColumn();
    $totalPaginas = ceil($total / $porPagina);

    //Devolver respuesta JSON
    echo json_encode([
        'animales' => $animales,
        'total' => $total,
        'totalPaginas' => $totalPaginas,
        'paginaActual' => $pagina
    ]);
    
} catch (Exception $e) {
    //Manejar errores y devolver respuesta de error
    echo json_encode([
        'error' => $e->getMessage(),
        'animales' => [],
        'total' => 0,
        'totalPaginas' => 0,
        'paginaActual' => 1
    ]);
}
exit;