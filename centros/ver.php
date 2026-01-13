<?php
require_once '../includes/config.php';
$id_centro = isset($_GET['id']) ? (int)$_GET['id'] : 0;
//Obtener informacion del centro
$stmt = $pdo->prepare("
    SELECT c.*, 
           COUNT(a.id_animal) as total_animales,
           SUM(CASE WHEN a.estado = 'Disponible' THEN 1 ELSE 0 END) as disponibles
    FROM centros c
    LEFT JOIN animales a ON c.id_centro = a.id_centro
    WHERE c.id_centro = ?
    GROUP BY c.id_centro
");
$stmt->execute([$id_centro]);
$centro = $stmt->fetch();
//Sino existe centro redigir al index
if (!$centro) {
    header('Location: ' . BASE_URL . '/centros');
    exit();
}
//Obtener animales de dicho centro
$animales = $pdo->prepare("
    SELECT * FROM animales 
    WHERE id_centro = ? 
    AND estado IN ('Disponible', 'Reservado')
    ORDER BY fecha_ingreso DESC 
    LIMIT 6
");
$animales->execute([$id_centro]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($centro['nombre']) ?> - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
    <style>
        .centro-header {
            background: linear-gradient(rgba(40, 167, 69, 0.9), rgba(40, 167, 69, 0.8));
            color: white;
            padding: 60px 0;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .info-item {
            padding: 15px;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }
        .animal-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .animal-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <!--Header de centro-->
    <div class="container-fluid mt-4">
        <div class="centro-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold mb-3">
                            <?= htmlspecialchars($centro['nombre']) ?>
                        </h1>
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-paw me-1"></i>
                                <?= $centro['total_animales'] ?> animales
                            </span>
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-heart me-1"></i>
                                <?= $centro['disponibles'] ?> disponibles
                            </span>
                            <?php if ($centro['web']): ?>
                            <a href="<?= $centro['web'] ?>" 
                               target="_blank" 
                               class="badge bg-white text-success fs-6 text-decoration-none">
                                <i class="fas fa-globe me-1"></i>Web oficial
                            </a>
                            <?php endif; ?>
                        </div>
                        <a href="<?= BASE_URL ?>/animales?centro=<?= $id_centro ?>" 
                           class="btn btn-light btn-lg">
                            <i class="fas fa-paw me-2"></i>Ver animales de este centro
                        </a>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="bg-white bg-opacity-25 p-4 rounded-circle d-inline-block">
                            <i class="fas fa-home fa-5x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Informacion de centro-->
    <div class="container">
        <div class="row g-4">
            <!--Informacion principal de centro-->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información del centro
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6>
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                                        Dirección
                                    </h6>
                                    <p class="mb-0"><?= htmlspecialchars($centro['direccion']) ?></p>
                                </div>
                                <div class="info-item">
                                    <h6>
                                        <i class="fas fa-phone text-success me-2"></i>
                                        Teléfono
                                    </h6>
                                    <p class="mb-0"><?= htmlspecialchars($centro['telefono']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6>
                                        <i class="fas fa-envelope text-success me-2"></i>
                                        Email
                                    </h6>
                                    <p class="mb-0"><?= htmlspecialchars($centro['email']) ?></p>
                                </div>
                                <?php if ($centro['web']): ?>
                                <div class="info-item">
                                    <h6>
                                        <i class="fas fa-globe text-success me-2"></i>
                                        Sitio web
                                    </h6>
                                    <p class="mb-0">
                                        <a href="<?= $centro['web'] ?>" target="_blank">
                                            <?= htmlspecialchars($centro['web']) ?>
                                        </a>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h6 class="mb-3">
                                <i class="fas fa-calendar-alt text-success me-2"></i>
                                Fecha de registro
                            </h6>
                            <p class="text-muted">
                                <?= date('d/m/Y', strtotime($centro['fecha_alta'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!--Animales del centro-->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-paw me-2"></i>
                                Animales de este centro
                            </h5>
                            <a href="<?= BASE_URL ?>/animales?centro=<?= $id_centro ?>" 
                               class="btn btn-sm btn-success">
                                Ver todos
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($animales->rowCount() > 0): ?>
                            <div class="row g-3">
                                <?php while ($animal = $animales->fetch()): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card animal-card h-100">
                                        <div class="position-relative">
                                            <img src="<?= $animal['imagen_url'] ?: ASSETS_URL . 'img/animales/default.jpg' ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= htmlspecialchars($animal['nombre']) ?>"
                                                 style="height: 150px; object-fit: cover;">
                                            <span class="badge bg-<?= $animal['estado'] == 'Disponible' ? 'success' : 'warning' ?> position-absolute top-0 end-0 m-2">
                                                <?= $animal['estado'] ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title mb-1"><?= htmlspecialchars($animal['nombre']) ?></h6>
                                            <p class="card-text small text-muted mb-2">
                                                <?= $animal['especie'] ?> · 
                                                <?= $animal['edad'] ?> años
                                            </p>
                                            <a href="<?= BASE_URL ?>/animales/ver/<?= $animal['id_animal'] ?>" 
                                               class="btn btn-sm btn-outline-success w-100">
                                                Ver ficha
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">
                                    Este centro no tiene animales disponibles actualmente
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--Barra lateral-->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            Ubicación
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-map-marker-alt fa-3x text-success mb-3"></i>
                            <p class="mb-0"><?= htmlspecialchars($centro['direccion']) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/mapa?centro=<?= $id_centro ?>" 
                           class="btn btn-success w-100">
                            <i class="fas fa-map me-2"></i>Ver en el mapa
                        </a>
                    </div>
                </div>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            ¿Cómo visitar?
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Se requiere cita previa
                        </p>
                        <p class="small mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Horario: Lunes a Viernes 10:00-18:00
                        </p>
                        <p class="small mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Sábados: 10:00-14:00
                        </p>
                        <p class="small mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Domingos y festivos: Cerrado
                        </p>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-share-alt me-2"></i>
                            Compartir
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-primary">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-outline-info">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-outline-danger">
                                <i class="fab fa-instagram"></i>
                            </button>
                            <button class="btn btn-outline-success">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>