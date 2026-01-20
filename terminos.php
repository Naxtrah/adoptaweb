<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - AdoptaWeb</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .terms-container {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .terms-container h2 {
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .terms-container h4 {
            color: #28a745;
            margin-top: 30px;
        }
        .terms-container ul {
            padding-left: 20px;
        }
        .terms-container li {
            margin-bottom: 10px;
        }
        .highlight-box {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
              
                <div class="mb-4">
                    <a href="javascript:history.back()" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
                
               
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h1 class="mb-0">
                            <i class="fas fa-file-contract me-2"></i>
                            Términos y Condiciones de AdoptaWeb
                        </h1>
                        <p class="mb-0 mt-2">Última actualización: <?= date('d/m/Y') ?></p>
                    </div>
                    <div class="card-body p-4 terms-container">
                        <div class="text-center mb-4">
                            <p class="lead">Por favor, lea atentamente los siguientes términos y condiciones antes de utilizar nuestros servicios.</p>
                        </div>
                        
                        <h2>1. Aceptación de los Términos</h2>
                        <p>Al acceder y utilizar el sitio web AdoptaWeb, usted acepta estar legalmente obligado por estos términos y condiciones. Si no está de acuerdo con alguna parte de estos términos, no debe utilizar nuestro sitio.</p>
                        
                        <h4>1.1. Definiciones</h4>
                        <ul>
                            <li><strong>AdoptaWeb:</strong> Plataforma digital que conecta centros de acogida con posibles adoptantes.</li>
                            <li><strong>Centro Colaborador:</strong> Entidad registrada que alberga animales disponibles para adopción.</li>
                            <li><strong>Usuario:</strong> Persona registrada en la plataforma.</li>
                            <li><strong>Animal:</strong> Mascota disponible para adopción a través de la plataforma.</li>
                        </ul>
                        
                        <h2>2. Proceso de Adopción</h2>
                        <h4>2.1. Requisitos para Adoptar</h4>
                        <ul>
                            <li>Ser mayor de 18 años.</li>
                            <li>Proporcionar información veraz y completa en el formulario de adopción.</li>
                            <li>Aceptar una posible visita al domicilio por parte del centro colaborador.</li>
                            <li>Firmar contrato de adopción con el centro correspondiente.</li>
                        </ul>
                        
                        <h4>2.2. Compromisos del Adoptante</h4>
                        <ul>
                            <li>Proporcionar todos los cuidados necesarios al animal (alimentación, veterinario, ejercicio).</li>
                            <li>No utilizar al animal para fines comerciales o de cría.</li>
                            <li>Notificar cualquier cambio de domicilio o situación al centro colaborador.</li>
                            <li>Permitir seguimientos post-adopción durante los primeros 6 meses.</li>
                            <li>En caso de no poder continuar con la adopción, devolver el animal al centro colaborador.</li>
                        </ul>
                        
                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle text-warning me-2"></i>Importante</h5>
                            <p class="mb-0">La adopción es un compromiso para toda la vida del animal. Por favor, asegúrese de estar preparado para asumir esta responsabilidad antes de iniciar el proceso.</p>
                        </div>
                        
                        <h2>3. Donaciones y Pagos</h2>
                        <h4>3.1. Donaciones a Centros</h4>
                        <p>Las donaciones realizadas a través de la plataforma están destinadas íntegramente a los centros colaboradores para el cuidado de los animales.</p>
                        
                        <h4>3.2. Tasas de Adopción</h4>
                        <p>Algunos centros pueden requerir una tasa de adopción que cubre:</p>
                        <ul>
                            <li>Vacunaciones completas</li>
                            <li>Desparasitación</li>
                            <li>Chip identificativo</li>
                            <li>Esterilización (en animales adultos)</li>
                            <li>Cuidados veterinarios previos</li>
                        </ul>
                        
                        <h2>4. Responsabilidades y Limitaciones</h2>
                        <h4>4.1. Responsabilidad de AdoptaWeb</h4>
                        <p>AdoptaWeb actúa como intermediario entre centros y adoptantes. No somos responsables directos de:</p>
                        <ul>
                            <li>La salud o comportamiento de los animales.</li>
                            <li>Las decisiones finales de los centros colaboradores.</li>
                            <li>Incidencias posteriores a la adopción.</li>
                        </ul>
                        
                        <h4>4.2. Responsabilidad de los Centros</h4>
                        <p>Cada centro colaborador es responsable de:</p>
                        <ul>
                            <li>Proporcionar información veraz sobre los animales.</li>
                            <li>Realizar el proceso de selección de adoptantes.</li>
                            <li>Proporcionar seguimiento post-adopción.</li>
                            <li>Gestionar los contratos de adopción.</li>
                        </ul>
                        
                        <h2>5. Privacidad y Protección de Datos</h2>
                        <p>Nos comprometemos a proteger su privacidad. Los datos personales recogidos serán utilizados únicamente para:</p>
                        <ul>
                            <li>Gestionar el proceso de adopción.</li>
                            <li>Mantenerle informado sobre el estado de su solicitud.</li>
                            <li>Enviar información relevante sobre adopciones (si ha dado su consentimiento).</li>
                            <li>Cumplir con obligaciones legales.</li>
                        </ul>
                        
                        <p>Puede ejercer sus derechos ARCO (Acceso, Rectificación, Cancelación y Oposición) contactando con nosotros.</p>
                        
                        <h2>6. Propiedad Intelectual</h2>
                        <p>Todo el contenido de AdoptaWeb (textos, imágenes, logotipos, diseño) está protegido por derechos de autor y no puede ser reproducido sin autorización.</p>
                        
                        <h2>7. Modificaciones de los Términos</h2>
                        <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán publicados en esta página con la fecha de última actualización.</p>
                        
                        <h2>8. Legislación Aplicable</h2>
                        <p>Estos términos se rigen por la legislación española. Cualquier disputa será resuelta en los tribunales de Madrid.</p>
                        
                        <div class="highlight-box">
                            <h5><i class="fas fa-phone-alt text-success me-2"></i>Contacto</h5>
                            <p class="mb-1">Para cualquier duda sobre estos términos, puede contactarnos:</p>
                            <p class="mb-0">
                                <strong>Email:</strong> legal@adoptaweb.com<br>
                                <strong>Teléfono:</strong> 910 000 000<br>
                                <strong>Dirección:</strong> Calle Legal, 123, 28001 Madrid
                            </p>
                        </div>
                        
                        <div class="text-center mt-5">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="acceptTerms" disabled>
                                <label class="form-check-label" for="acceptTerms">
                                    He leído y acepto los términos y condiciones
                                </label>
                            </div>
                            <button class="btn btn-success btn-lg" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimir Términos
                            </button>
                        </div>
                    </div>
                </div>
                
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-shield fa-2x text-success mb-3"></i>
                                <h5>Política de Privacidad</h5>
                                <a href="privacidad.php" class="btn btn-outline-success btn-sm">Leer más</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-cookie-bite fa-2x text-success mb-3"></i>
                                <h5>Política de Cookies</h5>
                                <a href="cookies.php" class="btn btn-outline-success btn-sm">Leer más</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-question-circle fa-2x text-success mb-3"></i>
                                <h5>Preguntas Frecuentes</h5>
                                <a href="faq.php" class="btn btn-outline-success btn-sm">Ver FAQ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        
        document.getElementById('acceptTerms').addEventListener('click', function() {
            if(this.checked) {
                alert('Gracias por aceptar nuestros términos y condiciones.');
            }
        });
        document.getElementById('acceptTerms').disabled = false;
    </script>
</body>
</html>