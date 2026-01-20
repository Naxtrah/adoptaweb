<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes - AdoptaWeb</title>
    <?php include __DIR__ . '/header.php'; ?>
    <style>
        .faq-header {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            padding: 60px 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 40px;
        }
        .faq-category {
            border-left: 4px solid #28a745;
            padding-left: 15px;
            margin: 30px 0 20px;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e8f5e9;
            color: #28a745;
        }
        .accordion-button:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        .search-box {
            max-width: 500px;
            margin: 0 auto 30px;
        }
        .faq-icon {
            color: #28a745;
            font-size: 1.2rem;
            margin-right: 10px;
        }
        .contact-cta {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 40px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    
 
    <div class="faq-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-question-circle me-3"></i>
                        Preguntas Frecuentes
                    </h1>
                    <p class="lead mb-4">
                        Encuentra respuestas a las dudas más comunes sobre el proceso de adopción.
                    </p>
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" id="faq-search" placeholder="Buscar en preguntas frecuentes...">
                            <button class="btn btn-light" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white bg-opacity-25 p-4 rounded-circle d-inline-block">
                        <i class="fas fa-question fa-4x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mb-5">
      
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Inicio</a></li>
                        <li class="breadcrumb-item active">FAQ</li>
                    </ol>
                </nav>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="#proceso-adopcion" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-paw me-1"></i>Proceso de adopción
                    </a>
                    <a href="#requisitos" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list-check me-1"></i>Requisitos
                    </a>
                    <a href="#costos" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-euro-sign me-1"></i>Costos
                    </a>
                    <a href="#post-adopcion" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-home me-1"></i>Post-adopción
                    </a>
                    <a href="#tecnicas" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-laptop me-1"></i>Aspectos técnicos
                    </a>
                </div>
            </div>
        </div>
        
       
        <div class="row">
            <div class="col-lg-8">
                
                <h3 class="faq-category" id="proceso-adopcion">
                    <i class="fas fa-paw faq-icon"></i>Proceso de adopción
                </h3>
                <div class="accordion mb-4" id="accordionProceso">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#proceso1">
                                ¿Cuánto tiempo tarda el proceso completo de adopción?
                            </button>
                        </h2>
                        <div id="proceso1" class="accordion-collapse collapse show" data-bs-parent="#accordionProceso">
                            <div class="accordion-body">
                                El proceso completo suele tardar entre 1 y 3 semanas, dependiendo del centro y de la documentación requerida. Incluye: revisión de solicitud (2-3 días), entrevista (1 semana), visita al hogar (opcional, 3-5 días) y firma del contrato.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#proceso2">
                                ¿Puedo adoptar si vivo fuera de Madrid?
                            </button>
                        </h2>
                        <div id="proceso2" class="accordion-collapse collapse" data-bs-parent="#accordionProceso">
                            <div class="accordion-body">
                                Sí, muchos centros permiten adopciones en otras provincias. Sin embargo, es posible que requieran una visita presencial o video-llamada para conocer tu hogar. Algunos centros pueden pedirte que vayas a recoger al animal personalmente.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#proceso3">
                                ¿Qué documentación necesito para adoptar?
                            </button>
                        </h2>
                        <div id="proceso3" class="accordion-collapse collapse" data-bs-parent="#accordionProceso">
                            <div class="accordion-body">
                                <ul>
                                    <li>DNI o pasaporte en vigor</li>
                                    <li>Justificante de domicilio (última factura de luz, agua o teléfono)</li>
                                    <li>Contrato de alquiler o escrituras de propiedad (si aplica)</li>
                                    <li>En algunos casos, declaración de la comunidad de vecinos permitiendo animales</li>
                                    <li>Formulario de adopción completo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
               
                <h3 class="faq-category" id="requisitos">
                    <i class="fas fa-list-check faq-icon"></i>Requisitos para adoptar
                </h3>
                <div class="accordion mb-4" id="accordionRequisitos">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#requisito1">
                                ¿Cuáles son los requisitos mínimos de edad para adoptar?
                            </button>
                        </h2>
                        <div id="requisito1" class="accordion-collapse collapse show" data-bs-parent="#accordionRequisitos">
                            <div class="accordion-body">
                                Debes ser mayor de 18 años. Algunos centros pueden requerir que tengas al menos 21 años para adoptar ciertas razas o animales con necesidades especiales.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#requisito2">
                                ¿Puedo adoptar si vivo en un piso sin jardín?
                            </button>
                        </h2>
                        <div id="requisito2" class="accordion-collapse collapse" data-bs-parent="#accordionRequisitos">
                            <div class="accordion-body">
                                Sí, muchos animales se adaptan perfectamente a la vida en piso. Lo importante es el compromiso de sacarlo a pasear regularmente (perros) o proporcionarle suficiente enriquecimiento ambiental (gatos). Algunas razas muy activas pueden no ser adecuadas para pisos pequeños.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#requisito3">
                                ¿Qué pasa si trabajo muchas horas al día?
                            </button>
                        </h2>
                        <div id="requisito3" class="accordion-collapse collapse" data-bs-parent="#accordionRequisitos">
                            <div class="accordion-body">
                                Depende del animal. Los gatos adultos son más independientes y pueden estar solos más tiempo. Los perros, especialmente cachorros, necesitan más atención. Si trabajas muchas horas, considera adoptar dos gatos para que se hagan compañía, o busca un perro adulto y tranquilo.
                            </div>
                        </div>
                    </div>
                </div>
                
             
                <h3 class="faq-category" id="costos">
                    <i class="fas fa-euro-sign faq-icon"></i>Costos de adopción
                </h3>
                <div class="accordion mb-4" id="accordionCostos">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#costo1">
                                ¿Cuánto cuesta adoptar un animal?
                            </button>
                        </h2>
                        <div id="costo1" class="accordion-collapse collapse show" data-bs-parent="#accordionCostos">
                            <div class="accordion-body">
                                Las tasas de adopción varían según el centro y el animal, pero generalmente oscilan entre:
                                <ul>
                                    <li>Perros: 100€ - 200€</li>
                                    <li>Gatos: 50€ - 100€</li>
                                    <li>Animales exóticos: 75€ - 150€</li>
                                </ul>
                                Esta tasa incluye: vacunas, chip, desparasitación, esterilización y revisión veterinaria.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#costo2">
                                ¿La tasa de adopción es una compra?
                            </button>
                        </h2>
                        <div id="costo2" class="accordion-collapse collapse" data-bs-parent="#accordionCostos">
                            <div class="accordion-body">
                                No, no es una compra. La tasa cubre los gastos veterinarios que el centro ha tenido con el animal. Está prohibido vender animales en protectoras. Es una donación obligatoria para ayudar al centro a seguir rescatando más animales.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#costo3">
                                ¿Hay gastos mensuales estimados?
                            </button>
                        </h2>
                        <div id="costo3" class="accordion-collapse collapse" data-bs-parent="#accordionCostos">
                            <div class="accordion-body">
                                Sí, debes considerar:
                                <ul>
                                    <li>Alimentación: 30€ - 80€/mes</li>
                                    <li>Seguro veterinario: 20€ - 50€/mes (opcional pero recomendado)</li>
                                    <li>Gastos veterinarios anuales (vacunas, desparasitación): 100€ - 200€/año</li>
                                    <li>Accesorios y juguetes: 20€ - 50€/mes</li>
                                    <li>Guardería/paseador si viajas: variable</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
               
                <h3 class="faq-category" id="post-adopcion">
                    <i class="fas fa-home faq-icon"></i>Post-adopción
                </h3>
                <div class="accordion mb-4" id="accordionPost">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#post1">
                                ¿Qué seguimiento hace el centro después de la adopción?
                            </button>
                        </h2>
                        <div id="post1" class="accordion-collapse collapse show" data-bs-parent="#accordionPost">
                            <div class="accordion-body">
                                La mayoría de centros realizan seguimiento durante 6 meses a 1 año. Esto incluye:
                                <ul>
                                    <li>Llamada a los 7 días para ver cómo se adapta</li>
                                    <li>Llamada a los 30 días</li>
                                    <li>Llamada a los 6 meses</li>
                                    <li>Posible visita al hogar (algunos centros)</li>
                                    <li>Disponibilidad para consultas las 24/7</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#post2">
                                ¿Qué pasa si el animal no se adapta?
                            </button>
                        </h2>
                        <div id="post2" class="accordion-collapse collapse" data-bs-parent="#accordionPost">
                            <div class="accordion-body">
                                Si tras un período de adaptación razonable (normalmente 2-3 semanas) el animal no se adapta, DEBES devolverlo al centro. En el contrato de adopción se especifica que no puedes regalarlo, venderlo ni abandonarlo. El centro siempre lo recibirá de vuelta.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#post3">
                                ¿Puedo cambiar el nombre del animal?
                            </button>
                        </h2>
                        <div id="post3" class="accordion-collapse collapse" data-bs-parent="#accordionPost">
                            <div class="accordion-body">
                                Sí, puedes cambiarle el nombre, pero es recomendable hacerlo gradualmente, especialmente con animales adultos. Usa ambos nombres por un tiempo hasta que se acostumbre al nuevo.
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <h3 class="faq-category" id="tecnicas">
                    <i class="fas fa-laptop faq-icon"></i>Aspectos técnicos de la plataforma
                </h3>
                <div class="accordion mb-4" id="accordionTecnicas">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#tecnica1">
                                ¿Cómo puedo recuperar mi contraseña?
                            </button>
                        </h2>
                        <div id="tecnica1" class="accordion-collapse collapse show" data-bs-parent="#accordionTecnicas">
                            <div class="accordion-body">
                                En la página de login, haz clic en "¿Olvidaste tu contraseña?" e introduce tu email. Recibirás un enlace para crear una nueva contraseña. Si no recibes el email, revisa la carpeta de spam o contacta con soporte.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tecnica2">
                                ¿Cómo actualizo mis datos personales?
                            </button>
                        </h2>
                        <div id="tecnica2" class="accordion-collapse collapse" data-bs-parent="#accordionTecnicas">
                            <div class="accordion-body">
                                Accede a tu perfil (icono de usuario en la barra superior) y selecciona "Editar perfil". Allí podrás actualizar tu información personal, dirección, teléfono, etc. Es importante mantener estos datos actualizados para el proceso de adopción.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tecnica3">
                                ¿Cómo cancelo mi suscripción al newsletter?
                            </button>
                        </h2>
                        <div id="tecnica3" class="accordion-collapse collapse" data-bs-parent="#accordionTecnicas">
                            <div class="accordion-body">
                                En cada email del newsletter encontrarás un enlace en la parte inferior para darte de baja. También puedes contactar con soporte indicando el email que quieres eliminar de la lista.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
           
            <div class="col-lg-4">
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Estadísticas de adopción
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php
                     
                        if (isset($pdo)) {
                            $estadisticas = [
                                'adopciones_mes' => $pdo->query("SELECT COUNT(*) FROM adopciones WHERE MONTH(fecha_solicitud) = MONTH(NOW())")->fetchColumn(),
                                'animales_disponibles' => $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Disponible'")->fetchColumn(),
                                'tiempo_medio' => $pdo->query("SELECT AVG(DATEDIFF(fecha_adopcion, fecha_solicitud)) FROM adopciones WHERE fecha_adopcion IS NOT NULL")->fetchColumn(),
                            ];
                        } else {
                          
                            $estadisticas = [
                                'adopciones_mes' => 0,
                                'animales_disponibles' => 0,
                                'tiempo_medio' => 0
                            ];
                        }
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Adopciones este mes</span>
                                <span class="small fw-bold text-success"><?= $estadisticas['adopciones_mes'] ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Animales disponibles</span>
                                <span class="small fw-bold text-success"><?= $estadisticas['animales_disponibles'] ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Tiempo medio de proceso</span>
                                <span class="small fw-bold text-success"><?= round($estadisticas['tiempo_medio'] ?? 0) ?> días</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-fire me-2"></i>
                            Preguntas populares
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <a href="#costo1" class="text-decoration-none small">
                                    <i class="fas fa-question-circle text-success me-2"></i>
                                    ¿Cuánto cuesta adoptar?
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#requisito2" class="text-decoration-none small">
                                    <i class="fas fa-question-circle text-success me-2"></i>
                                    ¿Puedo adoptar en piso?
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#proceso1" class="text-decoration-none small">
                                    <i class="fas fa-question-circle text-success me-2"></i>
                                    ¿Cuánto tarda el proceso?
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#post2" class="text-decoration-none small">
                                    <i class="fas fa-question-circle text-success me-2"></i>
                                    ¿Y si no se adapta?
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                
                <div class="contact-cta shadow-sm">
                    <h5 class="mb-3">
                        <i class="fas fa-headset text-success me-2"></i>
                        ¿No encuentras tu respuesta?
                    </h5>
                    <p class="small text-muted mb-3">
                        Nuestro equipo de soporte está aquí para ayudarte con cualquier duda adicional.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>/contacto.php" class="btn btn-success">
                            <i class="fas fa-envelope me-2"></i>Contactar con soporte
                        </a>
                        <a href="tel:+34900123456" class="btn btn-outline-success">
                            <i class="fas fa-phone me-2"></i>Llamar al 900 123 456
                        </a>
                    </div>
                    <div class="mt-3">
                        <p class="small text-muted mb-1">
                            <i class="fas fa-clock me-1"></i>
                            Horario atención: L-V 9:00-18:00
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-envelope me-1"></i>
                            Email: soporte@adoptaweb.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="contact-cta text-center mt-5">
            <h4 class="mb-3">¿Listo para empezar tu proceso de adopción?</h4>
            <p class="text-muted mb-4">
                Miles de animales esperan un hogar. Encuentra a tu compañero ideal hoy mismo.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="<?= BASE_URL ?>/animales" class="btn btn-success btn-lg">
                    <i class="fas fa-paw me-2"></i>Ver animales disponibles
                </a>
                <a href="<?= BASE_URL ?>/centros" class="btn btn-outline-success btn-lg">
                    <i class="fas fa-home me-2"></i>Conocer los centros
                </a>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const searchInput = document.getElementById('faq-search');
        const accordionButtons = document.querySelectorAll('.accordion-button');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                if (searchTerm.length < 2) {
                   
                    accordionButtons.forEach(btn => {
                        btn.parentElement.parentElement.style.display = '';
                    });
                    return;
                }
                
                
                accordionButtons.forEach(btn => {
                    const questionText = btn.textContent.toLowerCase();
                    const parentItem = btn.parentElement.parentElement;
                    
                    if (questionText.includes(searchTerm)) {
                        parentItem.style.display = '';
                        
                        const collapseId = btn.getAttribute('data-bs-target');
                        const collapseElement = document.querySelector(collapseId);
                        if (collapseElement) {
                            new bootstrap.Collapse(collapseElement, { toggle: true });
                        }
                    } else {
                        parentItem.style.display = 'none';
                    }
                });
            });
        }
        
       
        const totalQuestions = document.querySelectorAll('.accordion-item').length;
        const questionCountElement = document.createElement('div');
        questionCountElement.className = 'text-muted small mt-3';
        questionCountElement.innerHTML = `<i class="fas fa-info-circle me-1"></i> Mostrando ${totalQuestions} preguntas frecuentes`;
        
        const faqContainer = document.querySelector('.col-lg-8');
        if (faqContainer) {
            faqContainer.appendChild(questionCountElement);
        }
        
       
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
    </script>
</body>
</html>