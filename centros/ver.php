<?php
require_once '../includes/config.php';
$id_centro = isset($_GET['id']) ? (int)$_GET['id'] : 0;
/*Sumar los animales disponibles por centro*/
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

if (!$centro) {
    header('Location: ' . BASE_URL . '/centros');
    exit();
}
/*Conseguir los datos de los animales disponibles y reservadors*/
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
        .animal-card:hover {
            transform: translateY(-5px);
        }
        
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid mt-4">
        <div class="centro-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <?php if (!empty($centro['imagen_url'])): ?>
                            <div class="mb-3">
                                <img src="../<?= ltrim($centro['imagen_url'], './') ?>" alt="<?= htmlspecialchars($centro['nombre']) ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($centro['nombre']) ?></h1>
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <span class="badge bg-light text-dark fs-6"><i class="fas fa-paw me-1"></i><?= $centro['total_animales'] ?> animales</span>
                            <span class="badge bg-light text-dark fs-6"><i class="fas fa-heart me-1"></i><?= $centro['disponibles'] ?> disponibles</span>
                            <?php if ($centro['web']): ?>
                                <a href="https://instagram.com" target="_blank" class="badge bg-white text-success fs-6 text-decoration-none"><i class="fas fa-globe me-1"></i>Web oficial</a>
                            <?php endif; ?>
                        </div>
                        <a href="<?= BASE_URL ?>/animales?centro=<?= $id_centro ?>" class="btn btn-light btn-lg"><i class="fas fa-paw me-2"></i>Ver animales</a>
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

    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5><i class="fas fa-info-circle me-2"></i>Información del centro</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Dirección:</strong> <?= htmlspecialchars($centro['direccion']) ?></p>
                                <p><strong>Teléfono:</strong> <?= htmlspecialchars($centro['telefono']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?= htmlspecialchars($centro['email']) ?></p>
                                <?php if ($centro['web']): ?>
                                    <p><strong>Web:</strong> <a href="<?= $centro['web'] ?>" target="_blank"><?= htmlspecialchars($centro['web']) ?></a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5><i class="fas fa-paw me-2"></i>Animales de este centro</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($animales->rowCount() > 0): ?>
                            <div class="row g-3">
                                <?php while ($animal = $animales->fetch()): ?>
                                    <?php
                                    $imagen_animal = '';
                                    if ($animal['imagen_url']) {
                                        $imagen_animal = strpos($animal['imagen_url'], './img/') === 0 ? '../' . substr($animal['imagen_url'], 2) : '../img/animales/' . basename($animal['imagen_url']);
                                    } else {
                                        $imagen_animal = '../img/animales/default.jpg';
                                    }
                                    ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 animal-card">
                                            <img src="<?= $imagen_animal ?>" class="card-img-top" alt="<?= htmlspecialchars($animal['nombre']) ?>" style="height: 150px; object-fit: cover;" onerror="this.src='../img/animales/default.jpg'">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($animal['nombre']) ?></h6>
                                                <p class="small text-muted"><?= $animal['especie'] ?> · <?= $animal['edad'] ?> años</p>
                                                <a href="<?= BASE_URL ?>/animales/ver.php?id=<?= $animal['id_animal'] ?>" class="btn btn-sm btn-outline-success w-100">Ver ficha</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay animales disponibles actualmente.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-map-marked-alt me-2"></i>Ubicación</h5>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-3x text-success mb-3"></i>
                        <p><?= htmlspecialchars($centro['direccion']) ?></p>
                        <a href="https://instagram.com" class="btn btn-success w-100">Ver en el mapa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>