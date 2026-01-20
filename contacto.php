<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $telefono = sanitizar($_POST['telefono'] ?? '');
    $asunto = sanitizar($_POST['asunto'] ?? '');
    $mensaje = sanitizar($_POST['mensaje'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $error = 'Por favor, completa todos los campos obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contactos 
                (nombre, email, telefono, asunto, mensaje, fecha_contacto, estado) 
                VALUES (?, ?, ?, ?, ?, NOW(), 'Pendiente')
            ");
            $stmt->execute([$nombre, $email, $telefono, $asunto, $mensaje]);
            
            $success = 'Mensaje enviado correctamente. Te responderemos en breve.';
            
            $_POST = [];
            
        } catch (PDOException $e) {
            $error = 'Error al enviar el mensaje: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - AdoptaWeb</title>
    <?php include 'includes/header.php'; ?>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
        }
        
        .contact-form-card {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .contact-info-box {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border-radius: 15px;
            height: 100%;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        
        footer {
            width: 100% !important;
            margin-top: auto !important;
            background-color: #343a40 !important;
        }
        
        footer .container {
            max-width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        .container {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        
        <div class="main-content">
            <div class="container mt-4 mb-5">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <nav aria-label="breadcrumb" class="mb-4">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Inicio</a></li>
                                <li class="breadcrumb-item active">Contacto</li>
                            </ol>
                        </nav>
                        
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="card shadow contact-form-card">
                                    <div class="card-header bg-success text-white py-3">
                                        <h4 class="mb-0">
                                            <i class="fas fa-envelope me-2"></i>
                                            Envíanos tu mensaje
                                        </h4>
                                    </div>
                                    <div class="card-body p-4">
                                        <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <?= $error ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($success): ?>
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <?= $success ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <form method="POST" action="" id="contactForm">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nombre" class="form-label">
                                                        <i class="fas fa-user text-success me-1"></i>
                                                        Nombre completo *
                                                    </label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                                           value="<?= $_POST['nombre'] ?? '' ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">
                                                        <i class="fas fa-envelope text-success me-1"></i>
                                                        Email *
                                                    </label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                           value="<?= $_POST['email'] ?? '' ?>" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="telefono" class="form-label">
                                                    <i class="fas fa-phone text-success me-1"></i>
                                                    Teléfono
                                                </label>
                                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                                       value="<?= $_POST['telefono'] ?? '' ?>">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="asunto" class="form-label">
                                                    <i class="fas fa-tag text-success me-1"></i>
                                                    Asunto *
                                                </label>
                                                <select class="form-select" id="asunto" name="asunto" required>
                                                    <option value="">Selecciona un asunto...</option>
                                                    <option value="Consulta general" <?= ($_POST['asunto'] ?? '') == 'Consulta general' ? 'selected' : '' ?>>Consulta general</option>
                                                    <option value="Problema con adopción" <?= ($_POST['asunto'] ?? '') == 'Problema con adopción' ? 'selected' : '' ?>>Problema con adopción</option>
                                                    <option value="Sugerencia" <?= ($_POST['asunto'] ?? '') == 'Sugerencia' ? 'selected' : '' ?>>Sugerencia</option>
                                                    <option value="Reportar error" <?= ($_POST['asunto'] ?? '') == 'Reportar error' ? 'selected' : '' ?>>Reportar error</option>
                                                    <option value="Colaboración" <?= ($_POST['asunto'] ?? '') == 'Colaboración' ? 'selected' : '' ?>>Colaboración</option>
                                                    <option value="Otro" <?= ($_POST['asunto'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label for="mensaje" class="form-label">
                                                    <i class="fas fa-comment text-success me-1"></i>
                                                    Mensaje *
                                                </label>
                                                <textarea class="form-control" id="mensaje" name="mensaje" rows="6" required><?= $_POST['mensaje'] ?? '' ?></textarea>
                                                <div class="form-text">Describe tu consulta con el máximo detalle posible.</div>
                                            </div>
                                            
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-success btn-lg py-3">
                                                    <i class="fas fa-paper-plane me-2"></i>
                                                    Enviar mensaje
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="contact-info-box p-4 shadow">
                                    <h4 class="mb-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Información de contacto
                                    </h4>
                                    
                                    <div class="mb-4">
                                        <div class="contact-icon mx-auto">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <h6>Dirección</h6>
                                        <p class="mb-0">Calle Animales 123<br>28001 Madrid, España</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="contact-icon mx-auto">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <h6>Teléfono</h6>
                                        <p class="mb-0">+34 900 123 456</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="contact-icon mx-auto">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <h6>Email</h6>
                                        <p class="mb-0">info@adoptaweb.com</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="contact-icon mx-auto">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <h6>Horario de atención</h6>
                                        <p class="mb-0">Lunes a Viernes: 9:00 - 18:00<br>Sábados: 10:00 - 14:00</p>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <h6>Síguenos en redes</h6>
                                        <div class="d-flex justify-content-center gap-3 mt-2">
                                            <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                                            <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                                            <a href="#" class="text-white fs-4"><i class="fab fa-youtube"></i></a>
                                            <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const mensaje = document.getElementById('mensaje');
            if (mensaje.value.trim().length < 10) {
                e.preventDefault();
                alert('Por favor, escribe un mensaje más detallado (mínimo 10 caracteres)');
                mensaje.focus();
            }
        });
    </script>
</body>
</html>