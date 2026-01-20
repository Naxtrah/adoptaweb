<?php
require_once '../includes/config.php';
<<<<<<< HEAD
/*Paginación*/
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 9;
$inicio = ($pagina - 1) * $por_pagina;
/*Suma de los animales disponibles por centro*/
=======
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 9;
$inicio = ($pagina - 1) * $por_pagina;

>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
$stmt = $pdo->prepare("
    SELECT c.*, 
           COUNT(a.id_animal) as total_animales,
           SUM(CASE WHEN a.estado = 'Disponible' THEN 1 ELSE 0 END) as disponibles
    FROM centros c
    LEFT JOIN animales a ON c.id_centro = a.id_centro
    GROUP BY c.id_centro
    ORDER BY c.nombre
    LIMIT :inicio, :por_pagina
");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$centros = $stmt->fetchAll();

$total_centros = $pdo->query("SELECT COUNT(*) FROM centros")->fetchColumn();
$total_paginas = ceil($total_centros / $por_pagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centros de Adopción - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="fw-bold"><i class="fas fa-home text-success me-2"></i>Centros de Adopción</h1>
                <p class="text-muted">Conoce nuestros centros colaboradores.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= BASE_URL ?>/mapa" class="btn btn-success"><i class="fas fa-map-marked-alt me-2"></i>Ver en Mapa</a>
            </div>
        </div>
        <div class="row g-4">
<<<<<<< HEAD
        <!--Inserción datos de la consulta-->
=======
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
            <?php foreach ($centros as $centro): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <?php if (!empty($centro['imagen_url'])): ?>
                                <img src="<?= '../' . ltrim($centro['imagen_url'], './') ?>" alt="<?= htmlspecialchars($centro['nombre']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-home fa-4x text-success"></i>
                                </div>
                            <?php endif; ?>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2"><?= $centro['disponibles'] ?> disponibles</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($centro['nombre']) ?></h5>
                            <p class="card-text small text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($centro['direccion']) ?><br>
                                <i class="fas fa-phone me-1"></i><?= htmlspecialchars($centro['telefono']) ?><br>
                                <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($centro['email']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark"><i class="fas fa-paw me-1"></i><?= $centro['total_animales'] ?> animales</span>
                                <a href="<?= BASE_URL ?>/centros/ver.php?id=<?= $centro['id_centro'] ?>" class="btn btn-sm btn-success">Ver centro</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($total_paginas > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($pagina > 1): ?><li class="page-item"><a class="page-link" href="?pagina=<?= $pagina - 1 ?>"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?><li class="page-item <?= $i == $pagina ? 'active' : '' ?>"><a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?>
                    <?php if ($pagina < $total_paginas): ?><li class="page-item"><a class="page-link" href="?pagina=<?= $pagina + 1 ?>"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>