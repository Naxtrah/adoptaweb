<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - AdoptaWeb</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .privacy-section {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
        }
        .highlight-box {
            background: #e8f5e8;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .data-table th {
            background-color: #f1f8e9;
        }
        .update-badge {
            background-color: #ffc107;
            color: #212529;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        a{
            color:gray;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Inicio</a></li>
                        <li class="breadcrumb-item active">Política de Privacidad</li>
                    </ol>
                </nav>
                
                <div class="card shadow">
                    <div class="card-header bg-success text-white py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1">
                                    <i class="fas fa-user-shield me-2"></i>
                                    Política de Privacidad
                                </h2>
                                <p class="mb-0">AdoptaWeb - Plataforma de adopción animal</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Introducción -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                1. Introducción
                            </h4>
                            <p>
                                En <strong>AdoptaWeb</strong>, nos comprometemos a proteger la privacidad de nuestros usuarios. 
                                Esta política describe cómo recopilamos, utilizamos y protegemos tu información personal 
                                cuando utilizas nuestro sitio web y servicios relacionados con la adopción animal.
                            </p>
                            <p class="mb-0">
                                Al utilizar nuestro sitio, aceptas las prácticas descritas en esta política de privacidad. 
                                Si no estás de acuerdo con estos términos, por favor no utilices nuestros servicios.
                            </p>
                        </div>
                        
                        <!-- Datos que recopilamos -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-database me-2"></i>
                                2. Datos que recopilamos
                            </h4>
                            
                            <div class="table-responsive mb-4">
                                <table class="table data-table">
                                    <thead>
                                        <tr>
                                            <th>Tipo de dato</th>
                                            <th>Ejemplos</th>
                                            <th>Finalidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Datos personales</strong></td>
                                            <td>Nombre, email, teléfono, dirección</td>
                                            <td>Gestión de adopciones y contacto</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Datos de perfil</strong></td>
                                            <td>Preferencias de adopción, historial</td>
                                            <td>Personalización del servicio</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Datos técnicos</strong></td>
                                            <td>IP, navegador, dispositivo</td>
                                            <td>Seguridad y análisis</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Datos de uso</strong></td>
                                            <td>Páginas visitadas, interacciones</td>
                                            <td>Mejora del sitio web</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="highlight-box">
                                <h5 class="text-success">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Datos sensibles que NO recopilamos
                                </h5>
                                <p class="mb-2">
                                    <i class="fas fa-times text-danger me-1"></i>
                                    No solicitamos ni almacenamos datos especialmente protegidos como:
                                </p>
                                <ul class="mb-0">
                                    <li>Datos de salud o médicos</li>
                                    <li>Información financiera detallada</li>
                                    <li>Datos raciales o étnicos</li>
                                    <li>Orientación sexual o religión</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Cómo usamos tus datos -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-cogs me-2"></i>
                                3. Cómo utilizamos tu información
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-heart me-1"></i>
                                                Gestión de adopciones
                                            </h6>
                                            <ul class="small">
                                                <li>Procesar solicitudes de adopción</li>
                                                <li>Contactar con centros de acogida</li>
                                                <li>Gestionar seguimiento post-adopción</li>
                                                <li>Coordinar visitas a animales</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-envelope me-1"></i>
                                                Comunicación
                                            </h6>
                                            <ul class="small">
                                                <li>Enviar actualizaciones de procesos</li>
                                                <li>Informar sobre nuevos animales</li>
                                                <li>Newsletter (solo con consentimiento)</li>
                                                <li>Recordatorios importantes</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-chart-line me-1"></i>
                                                Mejora del servicio
                                            </h6>
                                            <ul class="small">
                                                <li>Análisis de uso del sitio</li>
                                                <li>Optimización de funcionalidades</li>
                                                <li>Detección y solución de problemas</li>
                                                <li>Personalización de contenido</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Seguridad
                                            </h6>
                                            <ul class="small">
                                                <li>Prevención de fraude</li>
                                                <li>Protección de datos personales</li>
                                                <li>Detección de actividades sospechosas</li>
                                                <li>Cumplimiento legal</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Compartir datos -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-share-alt me-2"></i>
                                4. Compartir información con terceros
                            </h4>
                            <p>
                                Tu información personal es confidencial. Solo compartimos datos en las siguientes circunstancias:
                            </p>
                            
                            <div class="alert alert-light">
                                <h6 class="alert-heading">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Con tu consentimiento explícito
                                </h6>
                                <p class="mb-2 small">
                                    Para coordinar adopciones con centros colaboradores o en procesos específicos que requieran compartir información.
                                </p>
                            </div>
                            
                            <div class="alert alert-light">
                                <h6 class="alert-heading">
                                    <i class="fas fa-gavel text-warning me-1"></i>
                                    Por requisitos legales
                                </h6>
                                <p class="mb-2 small">
                                    Cuando sea necesario para cumplir con leyes aplicables, regulaciones, procesos legales o solicitudes gubernamentales.
                                </p>
                            </div>
                            
                            <div class="alert alert-light">
                                <h6 class="alert-heading">
                                    <i class="fas fa-handshake text-primary me-1"></i>
                                    Proveedores de servicios
                                </h6>
                                <p class="mb-0 small">
                                    Empresas que nos ayudan a operar nuestro servicio (alojamiento web, análisis, correo electrónico), bajo estrictos acuerdos de confidencialidad.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Tus derechos -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-user-check me-2"></i>
                                5. Tus derechos de protección de datos
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-eye text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Derecho de acceso</h6>
                                            <p class="small mb-0">Puedes solicitar una copia de tus datos personales.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-edit text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Derecho de rectificación</h6>
                                            <p class="small mb-0">Puedes corregir datos inexactos o incompletos.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-trash-alt text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Derecho de supresión</h6>
                                            <p class="small mb-0">Puedes solicitar la eliminación de tus datos.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-ban text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Derecho de oposición</h6>
                                            <p class="small mb-0">Puedes oponerte al tratamiento de tus datos.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <p class="mb-2">
                                    <strong>Para ejercer tus derechos:</strong> Envía un email a 
                                    <a href="mailto:privacidad@adoptaweb.com" class="text-success">privacidad@adoptaweb.com</a> 
                                    con el asunto "Ejercicio de derechos ARCO-PD"
                                </p>
                                <p class="small text-muted mb-0">
                                    Te responderemos en un plazo máximo de 30 días hábiles.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Seguridad -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-lock me-2"></i>
                                6. Medidas de seguridad
                            </h4>
                            <p>
                                Implementamos medidas técnicas y organizativas apropiadas para proteger tus datos personales contra 
                                pérdida, uso indebido y acceso no autorizado.
                            </p>
                            
                            <div class="row text-center mt-3">
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 border rounded">
                                        <i class="fas fa-server fa-2x text-success mb-2"></i>
                                        <h6>Encriptación SSL</h6>
                                        <p class="small mb-0">Conexiones seguras</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 border rounded">
                                        <i class="fas fa-firewall fa-2x text-success mb-2"></i>
                                        <h6>Firewalls</h6>
                                        <p class="small mb-0">Protección perimetral</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 border rounded">
                                        <i class="fas fa-user-lock fa-2x text-success mb-2"></i>
                                        <h6>Control de acceso</h6>
                                        <p class="small mb-0">Acceso restringido</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 border rounded">
                                        <i class="fas fa-sync-alt fa-2x text-success mb-2"></i>
                                        <h6>Copias de seguridad</h6>
                                        <p class="small mb-0">Backups regulares</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cookies -->
                        <div class="privacy-section">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-cookie-bite me-2"></i>
                                7. Política de Cookies
                            </h4>
                            <p>
                                Utilizamos cookies para mejorar tu experiencia en nuestro sitio web. Puedes gestionar tus preferencias 
                                de cookies en cualquier momento desde la configuración de tu navegador o a través de nuestro 
                                banner de cookies.
                            </p>
                            <a href="<?= BASE_URL ?>/cookies.php" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-cookie me-1"></i>
                                Ver política de cookies completa
                            </a>
                        </div>
                        
                        <!-- Contacto -->
                        <div class="alert alert-success mt-4">
                            <h5 class="alert-heading">
                                <i class="fas fa-headset me-2"></i>
                                Contacto y dudas
                            </h5>
                            <p class="mb-2">
                                Si tienes preguntas sobre esta política de privacidad o sobre el tratamiento de tus datos personales, 
                                puedes contactarnos:
                            </p>
                            <ul class="mb-0">
                                <li><i class="fas fa-envelope me-1"></i> <strong>Email:</strong> privacidad@adoptaweb.com</li>
                                <li><i class="fas fa-phone me-1"></i> <strong>Teléfono:</strong> +34 900 123 456</li>
                                <li><i class="fas fa-map-marker-alt me-1"></i> <strong>Dirección:</strong> Calle Animales 123, 28001 Madrid, España</li>
                                <li><i class="fas fa-clock me-1"></i> <strong>Horario atención:</strong> Lunes a Viernes 9:00-18:00</li>
                            </ul>
                        </div>
                        
                        <!-- Cambios -->
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Cambios en la política
                            </h5>
                            <p class="mb-0">
                                Podemos actualizar esta política ocasionalmente. Te notificaremos sobre cambios significativos 
                                mediante un aviso en nuestro sitio web o por correo electrónico. Te recomendamos revisar 
                                periódicamente esta página para estar informado sobre cómo protegemos tu información.
                            </p>
                        </div>
                        
                        <!-- Botones de navegación -->
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <a href="<?= BASE_URL ?>/cookies.php" class="btn btn-outline-success me-2">
                                    <i class="fas fa-cookie-bite me-1"></i>
                                    Política de Cookies
                                </a>
                                <a href="<?= BASE_URL ?>/terminos.php" class="btn btn-outline-success">
                                    <i class="fas fa-file-contract me-1"></i>
                                    Términos y Condiciones
                                </a>
                            </div>
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