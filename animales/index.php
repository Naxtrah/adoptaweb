<?php
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animales para Adopción - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-3 col-xl-2">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-filter me-2"></i>
                            Filtros de Búsqueda
                        </h6>
                    </div>
                    <div class="card-body" id="filtros-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Cargando filtros...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-xl-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="mb-1">
                                    <i class="fas fa-paw text-success me-2"></i>
                                    Animales para Adopción
                                </h2>
                                <p class="text-muted mb-0">Encuentra a tu nuevo compañero</p>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="buscador-animales" placeholder="Buscar por nombre, especie, raza...">
                                    <button class="btn btn-outline-secondary" id="btn-limpiar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="resultados-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Cargando animales...</span>
                        </div>
                    </div>
                </div>
                <nav id="paginacion-container" class="d-none mt-4">
                    <ul class="pagination justify-content-center" id="paginacion"></ul>
                </nav>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
   
    <script src="<?= JS_URL ?>main.js"></script>
</body>
</html>