<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdoptaWeb</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .hero-section {
            background: linear-gradient(rgba(40, 167, 69, 0.9), rgba(40, 167, 70, 0.51)), 
                        url('img/perros.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 60px;
        }
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-10px);
        }
        .animal-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .animal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .btn-hero {
            padding: 15px 30px;
            font-size: 1.2rem;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Encuentra a tu nuevo mejor amigo</h1>
                    <p class="lead mb-4">AdoptaWeb conecta animales rescatados con personas que buscan darles un hogar. Trabajamos con múltiples centros para salvar tantas vidas como sea posible.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?= BASE_URL ?>/animales" class="btn btn-light btn-hero">
                            <i class="fas fa-paw me-2"></i>Ver Animales
                        </a>
                        <a href="<?= BASE_URL ?>/centros" class="btn btn-outline-light btn-hero">
                            <i class="fas fa-home me-2"></i>Ver Centros
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="container mb-5">
        <div class="row g-4">
            <?php
            $stats = [
                'animales' => $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Disponible'")->fetchColumn(),
                'adopciones' => $pdo->query("SELECT COUNT(*) FROM adopciones WHERE estado = 'Aprobada'")->fetchColumn(),
                'centros' => $pdo->query("SELECT COUNT(*) FROM centros")->fetchColumn(),
                'usuarios' => $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn()
            ];
            ?>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="display-4 text-success fw-bold"><?= $stats['animales'] ?></div>
                    <p class="text-muted mb-0">Animales disponibles</p>
                    <i class="fas fa-paw fa-2x text-success mt-3"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="display-4 text-success fw-bold"><?= $stats['adopciones'] ?></div>
                    <p class="text-muted mb-0">Adopciones exitosas</p>
                    <i class="fas fa-heart fa-2x text-success mt-3"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="display-4 text-success fw-bold"><?= $stats['centros'] ?></div>
                    <p class="text-muted mb-0">Centros colaboradores</p>
                    <i class="fas fa-home fa-2x text-success mt-3"></i>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="display-4 text-success fw-bold"><?= $stats['usuarios'] ?></div>
                    <p class="text-muted mb-0">Usuarios registrados</p>
                    <i class="fas fa-users fa-2x text-success mt-3"></i>
                </div>
            </div>
        </div>
    </section>
    
    <section class="container mb-5">
        <h2 class="fw-bold mb-4">
            <i class="fas fa-star text-warning me-2"></i>
            Animales destacados
        </h2>
        <div class="row g-4">
            <?php
            $animales = $pdo->query("
                SELECT a.*, c.nombre as centro_nombre 
                FROM animales a 
                LEFT JOIN centros c ON a.id_centro = c.id_centro 
                WHERE a.estado = 'Disponible' 
                ORDER BY a.fecha_ingreso DESC 
                LIMIT 4
            ")->fetchAll();
            foreach ($animales as $animal):
                $imagen_animal = '';
                if ($animal['imagen_url']) {
                    if (strpos($animal['imagen_url'], './img/') === 0) {
                        $imagen_animal = 'img/' . substr($animal['imagen_url'], 6);
                    } else {
                        $imagen_animal = $animal['imagen_url'];
                    }
                } else {
                    $imagen_animal = 'img/animales/default.jpg';
                }
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="card animal-card h-100">
                    <div class="position-relative">
                        <img src="<?= $imagen_animal ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($animal['nombre']) ?>"
                             style="height: 200px; object-fit: cover;"
                             onerror="this.src='img/animales/default.jpg'">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Disponible</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($animal['nombre']) ?></h5>
                        <p class="card-text small text-muted">
                            <i class="fas fa-paw me-1"></i><?= $animal['especie'] ?> · <?= $animal['edad'] ?> años<br>
                            <i class="fas fa-home me-1"></i><?= htmlspecialchars($animal['centro_nombre']) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="<?= BASE_URL ?>/animales/ver.php?id=<?= $animal['id_animal'] ?>"
                           class="btn btn-success w-100">
                            <i class="fas fa-heart me-1"></i>Conocer más
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section class="bg-light py-5 mb-5">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">¿Cómo funciona la adopción?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-search text-white fa-2x"></i>
                        </div>
                        <h4>1. Busca</h4>
                        <p class="text-muted">Explora nuestro listado de animales disponibles. Usa filtros para encontrar a tu compañero ideal.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-file-alt text-white fa-2x"></i>
                        </div>
                        <h4>2. Solicita</h4>
                        <p class="text-muted">Completa el formulario de adopción. El centro contactará contigo para una entrevista.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-4">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-home text-white fa-2x"></i>
                        </div>
                        <h4>3. Adopta</h4>
                        <p class="text-muted">Conoce a tu nuevo amigo en el centro y llévatelo a casa. Te ayudaremos con el proceso de adaptación.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="container mb-5">
        <h2 class="text-center mb-5 fw-bold">Historias de éxito</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-user text-white fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Ana Gómez</h5>
                            <small class="text-muted">Adoptó a Luna</small>
                        </div>
                    </div>
                    <p class="text-muted">"Adoptar a Luna fue la mejor decisión. El proceso fue sencillo y el centro nos ayudó en todo momento. ¡Gracias AdoptaWeb!"</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-user text-white fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Carlos López</h5>
                            <small class="text-muted">Adoptó a Max</small>
                        </div>
                    </div>
                    <p class="text-muted">"Max llegó a nuestra familia hace 6 meses y ha cambiado nuestras vidas. La plataforma hizo muy fácil encontrar al perro perfecto para nosotros."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-user text-white fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Lucía Martín</h5>
                            <small class="text-muted">Adoptó a Misu</small>
                        </div>
                    </div>
                    <p class="text-muted">"Misu es la gata más cariñosa del mundo. El seguimiento post-adopción fue excelente. Recomiendo AdoptaWeb a todo el mundo."</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="container mb-5">
        <div class="card bg-success text-white border-0">
            <div class="row g-0 align-items-center">
                <div class="col-md-8 p-5">
                    <h2 class="fw-bold mb-3">¿Listo para cambiar una vida?</h2>
                    <p class="mb-4">Miles de animales esperan un hogar. Tú puedes ser su segunda oportunidad.</p>
                    <a href="<?= BASE_URL ?>/animales" class="btn btn-light btn-lg">
                        <i class="fas fa-heart me-2"></i>Encuentra a tu compañero
                    </a>
                </div>
                <div class="col-md-4">
                    <img src="<?= ASSETS_URL ?>indexMainperro2.jpg" alt="Perro feliz" class="img-fluid rounded-1 pe-3">
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>