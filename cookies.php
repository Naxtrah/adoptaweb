<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Cookies - AdoptaWeb</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .cookie-section {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
        }
        .cookie-table {
            background: white;
        }
        .cookie-table th {
            background-color: #f1f8e9;
        }
        .required-cookie {
            border-left: 4px solid #dc3545;
        }
        .analytical-cookie {
            border-left: 4px solid #ffc107;
        }
        .third-party-cookie {
            border-left: 4px solid #007bff;
        }
        a{
            color:gray;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <!--Div principal-->
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Inicio</a></li>
                        <li class="breadcrumb-item active">Política de Cookies</li>
                    </ol>
                </nav>
                
                <div class="card shadow">
                    <div class="card-header bg-success text-white py-3">
                        <h2 class="mb-0">
                            <i class="fas fa-cookie-bite me-2"></i>
                            Política de Cookies
                        </h2>
                    </div>
                    
                    <div class="card-body p-4">
                        <!--Introducción-->
                        <div class="cookie-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                ¿Qué son las cookies?
                            </h4>
                            <p class="mb-0">
                                Las cookies son pequeños archivos de texto que los sitios web colocan en tu dispositivo cuando los visitas. 
                                Se utilizan para hacer que los sitios web funcionen de manera más eficiente, así como para proporcionar 
                                información a los propietarios del sitio.
                            </p>
                        </div>
                        
                        <!--Tipos de cookies-->
                        <div class="cookie-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-list me-2"></i>
                                Tipos de cookies que utilizamos
                            </h4>
                            
                            <div class="table-responsive">
                                <table class="table cookie-table">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Propósito</th>
                                            <th>Duración</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--Cookies necesarias-->
                                        <tr class="required-cookie">
                                            <td><strong>Cookies necesarias</strong></td>
                                            <td>
                                                <i class="fas fa-shield-alt text-danger me-1"></i>
                                                Esenciales para el funcionamiento del sitio (sesión de usuario, seguridad)
                                            </td>
                                            <td>Sesión</td>
                                        </tr>
                                        <!--Cookies de preferencias-->
                                        <tr>
                                            <td><strong>Cookies de preferencias</strong></td>
                                            <td>
                                                <i class="fas fa-cog text-success me-1"></i>
                                                Recuerdan tus ajustes (idioma, región, temas visuales)
                                            </td>
                                            <td>1 año</td>
                                        </tr>
                                        <!--Cookies analíticas-->
                                        <tr class="analytical-cookie">
                                            <td><strong>Cookies analíticas</strong></td>
                                            <td>
                                                <i class="fas fa-chart-bar text-warning me-1"></i>
                                                Nos ayudan a mejorar la web analizando el tráfico y uso
                                            </td>
                                            <td>2 años</td>
                                        </tr>
                                        <!--Cookies de terceros-->
                                        <tr class="third-party-cookie">
                                            <td><strong>Cookies de terceros</strong></td>
                                            <td>
                                                <i class="fas fa-external-link-alt text-primary me-1"></i>
                                                Integraciones con redes sociales y servicios externos
                                            </td>
                                            <td>Varía</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!--Cookies específicas-->
                        <div class="cookie-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-cookie me-2"></i>
                                Cookies específicas de AdoptaWeb
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-user-check me-1"></i>
                                                Sesión de usuario
                                            </h6>
                                            <p class="small mb-1"><strong>Nombre:</strong> adoptaweb_session</p>
                                            <p class="small mb-1"><strong>Propósito:</strong> Mantener tu sesión activa</p>
                                            <p class="small mb-0"><strong>Duración:</strong> Hasta que cierres el navegador</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-thumbs-up me-1"></i>
                                                Preferencias
                                            </h6>
                                            <p class="small mb-1"><strong>Nombre:</strong> adoptaweb_prefs</p>
                                            <p class="small mb-1"><strong>Propósito:</strong> Recordar tus preferencias</p>
                                            <p class="small mb-0"><strong>Duración:</strong> 1 año</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!--Más información-->
                        <div class="alert alert-info mt-4">
                            <h5 class="alert-heading">
                                <i class="fas fa-question-circle me-2"></i>
                                ¿Necesitas más información?
                            </h5>
                            <p class="mb-2">
                                Si tienes preguntas sobre nuestra política de cookies, puedes contactarnos:
                            </p>
                            <ul class="mb-0">
                                <li><i class="fas fa-envelope me-1"></i> Email: privacidad@adoptaweb.com</li>
                                <li><i class="fas fa-phone me-1"></i> Teléfono: +34 900 123 456</li>
                                <li><i class="fas fa-file-contract me-1"></i> <a href="<?= BASE_URL ?>/privacidad.php" class="text-info">Ver política de privacidad completa</a></li>
                            </ul>
                        </div>
                        <!--Visitar la política de privacidad-->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= BASE_URL ?>/privacidad.php" class="btn btn-outline-success">
                                <i class="fas fa-user-shield me-1"></i>
                                Política de Privacidad
                            </a>
                            <a href="<?= BASE_URL ?>/" class="btn btn-success">
                                <i class="fas fa-home me-1"></i>
                                Volver al inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>