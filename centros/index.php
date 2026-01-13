<?php
require_once '../includes/config.php';
//Paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 9;
$inicio = ($pagina - 1) * $por_pagina;
//Obtener centros con paginación con pdo
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
//Contar total
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
        <!--Header-->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="fw-bold">
                    <i class="fas fa-home text-success me-2"></i>
                    Centros de Adopción
                </h1>
                <p class="text-muted">
                    Conoce nuestros centros colaboradores. Cada uno trabaja incansablemente 
                    para rescatar y cuidar animales.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= BASE_URL ?>/mapa" class="btn btn-success">
                    <i class="fas fa-map-marked-alt me-2"></i>Ver en Mapa
                </a>
            </div>
        </div>
        <!--Estadisticas web-->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h3 class="text-success mb-1"><?= $total_centros ?></h3>
                        <p class="text-muted mb-0">Centros</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <?php
                        $total_animales = $pdo->query("SELECT COUNT(*) FROM animales")->fetchColumn();
                        ?>
                        <h3 class="text-success mb-1"><?= $total_animales ?></h3>
                        <p class="text-muted mb-0">Animales totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <?php
                        $disponibles = $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Disponible'")->fetchColumn();
                        ?>
                        <h3 class="text-success mb-1"><?= $disponibles ?></h3>
                        <p class="text-muted mb-0">Disponibles</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <?php
                        $adoptados = $pdo->query("SELECT COUNT(*) FROM adopciones WHERE estado = 'Aprobada'")->fetchColumn();
                        ?>
                        <h3 class="text-success mb-1"><?= $adoptados ?></h3>
                        <p class="text-muted mb-0">Adopciones</p>
                    </div>
                </div>
            </div>
        </div>
        <!--Lista de todos los centros-->
        <?php if (count($centros) > 0): ?>
            <div class="row g-4">
                <?php foreach ($centros as $centro): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <div class="card-img-top bg-success bg-opacity-10" 
                                 style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-home fa-4x text-success"></i>
                            </div>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                <?= $centro['disponibles'] ?> disponibles
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($centro['nombre']) ?></h5>
                            <p class="card-text small text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?= htmlspecialchars($centro['direccion']) ?><br>
                                <i class="fas fa-phone me-1"></i>
                                <?= htmlspecialchars($centro['telefono']) ?><br>
                                <i class="fas fa-envelope me-1"></i>
                                <?= htmlspecialchars($centro['email']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-paw me-1"></i>
                                    <?= $centro['total_animales'] ?> animales
                                </span>
                                <a href="<?= BASE_URL ?>/centros/ver/<?= $centro['id_centro'] ?>" 
                                   class="btn btn-sm btn-success">
                                    Ver centro
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>  
            <!--Paginacion centros-->
            <?php if ($total_paginas > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($pagina > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($pagina < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-home fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay centros registrados</h4>
                <p class="text-muted">Pronto añadiremos centros colaboradores.</p>
            </div>
        <?php endif; ?>
        <!--Informacion adicional centro-->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Sobre nuestros centros
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Nuestros centros colaboradores son organizaciones sin ánimo de lucro 
                            dedicadas al rescate, cuidado y protección de animales.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Todos los animales están vacunados
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Desparasitación completa
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Chip identificativo
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Esterilización según edad
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Revisión veterinaria completa
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Seguimiento post-adopción
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>