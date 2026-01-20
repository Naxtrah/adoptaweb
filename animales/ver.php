<?php
require_once '../includes/config.php';
$id_animal = isset($_GET['id']) ? (int)$_GET['id'] : 0;

//Obtener información del animal
$stmt = $pdo->prepare("
    SELECT a.*, c.nombre as centro_nombre, c.direccion, c.telefono, c.email, c.web, c.latitud, c.longitud
    FROM animales a
    LEFT JOIN centros c ON a.id_centro = c.id_centro
    WHERE a.id_animal = ?
");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch();

if (!$animal) {
    header('Location: ' . BASE_URL . '/animales');
    exit();
}

//Obtener vacunas del animal
$vacunas = $pdo->prepare("
    SELECT v.*, av.fecha_aplicacion, av.fecha_proxima
    FROM animal_vacunas av
    JOIN vacunas v ON av.id_vacuna = v.id_vacuna
    WHERE av.id_animal = ?
");
$vacunas->execute([$id_animal]);

//Verificar si el usuario actual ya tiene solicitud para este animal
$tiene_solicitud = false;
if (estaLogueado()) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM adopciones 
        WHERE id_usuario = ? AND id_animal = ? AND estado IN ('Pendiente', 'Aprobada')
    ");
    $stmt->execute([$_SESSION['user_id'], $id_animal]);
    $tiene_solicitud = $stmt->fetchColumn() > 0;
}

//Corregir ruta de imagen
$imagen_animal = '';
if ($animal['imagen_url']) {
    //Si la ruta comienza con ./img/, corregirla
    if (strpos($animal['imagen_url'], './img/') === 0) {
        $imagen_animal = '../' . substr($animal['imagen_url'], 2);
    } else {
        $imagen_animal = $animal['imagen_url'];
    }
} else {
    $imagen_animal = '../img/animales/default.jpg';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($animal['nombre']) ?> - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
    <style>
        .animal-header {
            background: linear-gradient(rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            border-radius: 15px;
            padding: 30px;
        }
        .animal-img {
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .info-badge {
            background: #e9f7ef;
            color: #28a745;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .vacuna-item {
            border-left: 3px solid #28a745;
            padding-left: 15px;
            margin-bottom: 10px;
        }
        a{
            text-decoration: none;
            color: #727573;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <!--Ruta de navegación -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/animales">Animales</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($animal['nombre']) ?></li>
            </ol>
        </nav>
        <!--Header del animal-->
        <div class="animal-header mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="<?= $imagen_animal ?>" 
                             class="animal-img w-100" 
                             alt="<?= htmlspecialchars($animal['nombre']) ?>"
                             onerror="this.src='../img/animales/default.jpg'">
                        <span class="badge bg-<?= $animal['estado'] == 'Disponible' ? 'success' : 'warning' ?> position-absolute top-0 end-0 m-3 fs-6">
                            <?= $animal['estado'] ?>
                        </span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($animal['nombre']) ?></h1>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="info-badge">
                            <i class="fas fa-paw me-1"></i><?= $animal['especie'] ?>
                        </span>
                        <span class="info-badge">
                            <i class="fas fa-<?= $animal['sexo'] == 'Macho' ? 'mars' : 'venus' ?> me-1"></i><?= $animal['sexo'] ?>
                        </span>
                        <span class="info-badge">
                            <i class="fas fa-birthday-cake me-1"></i><?= $animal['edad'] ?> años
                        </span>
                        <?php if ($animal['raza']): ?>
                        <span class="info-badge">
                            <i class="fas fa-dna me-1"></i><?= htmlspecialchars($animal['raza']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <h5 class="mb-2">
                            <i class="fas fa-home text-success me-2"></i>
                            Centro
                        </h5>
                        <p class="mb-1">
                            <a href="<?= BASE_URL ?>/centros/ver.php?id=<?= $animal['id_centro'] ?>" 
                               class="text-decoration-none">
                                <?= htmlspecialchars($animal['centro_nombre']) ?>
                            </a>
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= htmlspecialchars($animal['direccion']) ?>
                        </p>
                    </div>
                    <!--Botones de acción-->
                    <div class="d-grid gap-3">
                        <?php if ($animal['estado'] == 'Disponible'): ?>
                            <?php if (estaLogueado()): ?>
                                <?php if ($tiene_solicitud): ?>
                                    <button class="btn btn-warning btn-lg" disabled>
                                        <i class="fas fa-clock me-2"></i>Solicitud en proceso
                                    </button>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/pagos/realizar.php?id_adopcion=<?= $id_adopcion ?>&tipo=adopcion" class="btn btn-success btn-lg">
                                        <i class="fas fa-heart me-2"></i>Solicitar adopción
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/auth/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                                   class="btn btn-success btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para adoptar
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-ban me-2"></i>No disponible para adopción
                            </button>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/centros/ver.php?id=<?= $animal['id_centro'] ?>" 
                           class="btn btn-outline-success btn-lg">
                            <i class="fas fa-home me-2"></i>Contactar con el centro
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!--Descripción-->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Sobre <?= htmlspecialchars($animal['nombre']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="lead"><?= nl2br(htmlspecialchars($animal['descripcion'] ?? 'Sin descripción disponible')) ?></p>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="mb-3">
                                    <i class="fas fa-calendar-alt text-success me-2"></i>
                                    Fechas importantes
                                </h6>
                                <p class="mb-2">
                                    <strong>Fecha de ingreso:</strong><br>
                                    <?= date('d/m/Y', strtotime($animal['fecha_ingreso'])) ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    Estado de salud
                                </h6>
                                <p class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Desparasitado
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Con chip identificativo
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Vacunas-->
                <?php if ($vacunas->rowCount() > 0): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-syringe me-2"></i>
                            Cartilla de vacunación
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php while ($vacuna = $vacunas->fetch()): ?>
                            <div class="col-md-6 mb-3">
                                <div class="vacuna-item">
                                    <h6 class="mb-1"><?= htmlspecialchars($vacuna['nombre']) ?></h6>
                                    <p class="small text-muted mb-1">
                                        <?= htmlspecialchars($vacuna['descripcion']) ?>
                                    </p>
                                    <p class="small mb-1">
                                        <strong>Aplicada:</strong> 
                                        <?= date('d/m/Y', strtotime($vacuna['fecha_aplicacion'])) ?>
                                    </p>
                                    <p class="small mb-0">
                                        <strong>Próxima:</strong> 
                                        <?= date('d/m/Y', strtotime($vacuna['fecha_proxima'])) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <!--Información del centro-->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-home me-2"></i>
                            Centro de acogida
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2"><?= htmlspecialchars($animal['centro_nombre']) ?></h6>
                        <p class="small text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= htmlspecialchars($animal['direccion']) ?>
                        </p>
                        <p class="small text-muted mb-2">
                            <i class="fas fa-phone me-1"></i>
                            <?= htmlspecialchars($animal['telefono']) ?>
                        </p>
                        <p class="small text-muted mb-3">
                            <i class="fas fa-envelope me-1"></i>
                            <?= htmlspecialchars($animal['email']) ?>
                        </p>
                        <div class="d-grid gap-2">
                            <a href="<?= BASE_URL ?>/centros/ver.php?id=<?= $animal['id_centro'] ?>" 
                               class="btn btn-sm btn-success">
                                Ver centro
                            </a>
                            <a href="tel:<?= $animal['telefono'] ?>" 
                               class="btn btn-sm btn-outline-success">
                                <i class="fas fa-phone me-1"></i>Llamar
                            </a>
                            <?php if ($animal['latitud'] && $animal['longitud']): ?>
                            <a href="https://maps.google.com/?q=<?= $animal['latitud'] ?>,<?= $animal['longitud'] ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-map me-1"></i>Ver en Google Maps
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!--Información adicional-->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información adicional
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small mb-3">
                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                            Todos los animales son entregados con contrato de adopción.
                        </p>
                        <p class="small mb-3">
                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                            Se realiza seguimiento post-adopción durante 6 meses.
                        </p>
                        <p class="small mb-0">
                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                            Se puede requerir visita al domicilio antes de la adopción.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!--Animales relacionados-->
        <?php
        $relacionados = $pdo->prepare("
            SELECT a.*, c.nombre as centro_nombre
            FROM animales a
            LEFT JOIN centros c ON a.id_centro = c.id_centro
            WHERE a.id_centro = ? 
            AND a.id_animal != ? 
            AND a.estado = 'Disponible'
            ORDER BY RAND()
            LIMIT 3
        ");
        $relacionados->execute([$animal['id_centro'], $id_animal]);
        if ($relacionados->rowCount() > 0):
        ?>
        <div class="mt-5">
            <h4 class="mb-4">
                <i class="fas fa-paw text-success me-2"></i>
                Otros animales del mismo centro
            </h4>
            <div class="row g-4">
                <?php while ($relacionado = $relacionados->fetch()): 
                    // Corregir ruta de imagen para relacionados
                    $imagen_relacionado = '';
                    if ($relacionado['imagen_url']) {
                        if (strpos($relacionado['imagen_url'], './img/') === 0) {
                            $imagen_relacionado = '../' . substr($relacionado['imagen_url'], 2);
                        } else {
                            $imagen_relacionado = $relacionado['imagen_url'];
                        }
                    } else {
                        $imagen_relacionado = '../img/animales/default.jpg';
                    }
                ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?= $imagen_relacionado ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($relacionado['nombre']) ?>"
                             style="height: 200px; object-fit: cover;"
                             onerror="this.src='../img/animales/default.jpg'">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($relacionado['nombre']) ?></h5>
                            <p class="card-text small text-muted">
                                <?= $relacionado['especie'] ?> · <?= $relacionado['edad'] ?> años
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="ver.php?id=<?= $relacionado['id_animal'] ?>" 
                               class="btn btn-sm btn-success w-100">
                                Ver ficha
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>