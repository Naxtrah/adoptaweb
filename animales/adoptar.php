<?php
require_once '../includes/config.php';

if (!estaLogueado()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error_message'] = 'Debes iniciar sesión para solicitar una adopción';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

$id_animal = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT a.*, c.nombre as centro_nombre, c.direccion as centro_direccion, 
           c.telefono as centro_telefono, c.email as centro_email
    FROM animales a
    LEFT JOIN centros c ON a.id_centro = c.id_centro
    WHERE a.id_animal = ?
");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch();

$error = '';
if (!$animal) {
    $error = 'Animal no encontrado';
} elseif ($animal['estado'] !== 'Disponible') {
    $error = 'Este animal no está disponible para adopción';
}

if ($error) {
    $_SESSION['error_message'] = $error;
    header('Location: ' . BASE_URL . '/animales');
    exit();
}

$usuario = obtenerUsuario();
if (!$usuario) {
    header('Location: ' . BASE_URL . '/auth/logout.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM adopciones 
    WHERE id_usuario = ? AND id_animal = ? AND estado = 'Pendiente'
");
$stmt->execute([$_SESSION['user_id'], $id_animal]);
$tiene_solicitud = $stmt->fetchColumn() > 0;

$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$tiene_solicitud) {
    $motivacion = trim($_POST['motivacion'] ?? '');
    $experiencia = trim($_POST['experiencia'] ?? '');
    $vivienda = $_POST['vivienda'] ?? '';
    $otros_animales = $_POST['otros_animales'] ?? '';
    $horas_solo = $_POST['horas_solo'] ?? '';
    $acepto_terminos = isset($_POST['acepto_terminos']);
    
    if (empty($motivacion)) {
        $error = 'Por favor, explica tu motivación para adoptar';
    } elseif (empty($experiencia)) {
        $error = 'Por favor, describe tu experiencia con animales';
    } elseif (empty($vivienda)) {
        $error = 'Por favor, selecciona el tipo de vivienda';
    } elseif (!$acepto_terminos) {
        $error = 'Debes aceptar los términos y condiciones';
    } else {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                INSERT INTO adopciones 
                (id_usuario, id_animal, fecha_solicitud, estado, notas) 
                VALUES (?, ?, CURDATE(), 'Pendiente', ?)
            ");
            
            $notas = "Motivación: $motivacion\n";
            $notas .= "Experiencia: $experiencia\n";
            $notas .= "Tipo de vivienda: $vivienda\n";
            $notas .= "Otros animales: $otros_animales\n";
            $notas .= "Horas solo al día: $horas_solo";
            
            $stmt->execute([$_SESSION['user_id'], $id_animal, $notas]);
            $id_adopcion = $pdo->lastInsertId();
            
            $token_pago = hash('sha256', uniqid('', true));
            
            $sql_pago = "INSERT INTO adopciones_pagos (id_adopcion, id_usuario, monto, estado, token_pago) 
                         VALUES (?, ?, 60.00, 'Pendiente', ?)";
            $stmt = $pdo->prepare($sql_pago);
            $stmt->execute([$id_adopcion, $_SESSION['user_id'], $token_pago]);
            
            $stmt = $pdo->prepare("
                UPDATE animales SET estado = 'Reservado' 
                WHERE id_animal = ?
            ");
            $stmt->execute([$id_animal]);
            
            $pdo->commit();
            
            $success = '¡Solicitud de adopción enviada! El administrador revisará tu solicitud. Si es aprobada, podrás proceder al pago.';
            $tiene_solicitud = true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error al procesar la solicitud: ' . $e->getMessage();
        }
    }
}

$imagen_animal = '';
if ($animal['imagen_url']) {
    if (strpos($animal['imagen_url'], './img/') === 0) {
        $imagen_animal = '../' . substr($animal['imagen_url'], 2);
    } else {
        $imagen_animal = $animal['imagen_url'];
    }
} else {
    $imagen_animal = '../img/animales/default.jpg';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Adopción de <?= htmlspecialchars($animal['nombre']) ?> - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
    <style>
        .animal-img {
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }
        .step.active .step-number {
            background: #218838;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/animales">Animales</a></li>
                <li class="breadcrumb-item"><a href="ver.php?id=<?= $id_animal ?>"><?= htmlspecialchars($animal['nombre']) ?></a></li>
                <li class="breadcrumb-item active">Solicitar Adopción</li>
            </ol>
        </nav>
        
        <div class="step-indicator">
            <div class="step active">
                <div class="step-number">1</div>
                <div>Formulario</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div>Revisión</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div>Contrato</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div>Finalizado</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-paw me-2"></i>
                            Animal a adoptar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="<?= $imagen_animal ?>" 
                                 alt="<?= htmlspecialchars($animal['nombre']) ?>" 
                                 class="animal-img w-100"
                                 onerror="this.src='../img/animales/default.jpg'">
                        </div>
                        <h4 class="text-center"><?= htmlspecialchars($animal['nombre']) ?></h4>
                        <div class="info-box">
                            <p><strong>Especie:</strong> <?= $animal['especie'] ?></p>
                            <p><strong>Raza:</strong> <?= $animal['raza'] ? htmlspecialchars($animal['raza']) : 'Mestizo' ?></p>
                            <p><strong>Edad:</strong> <?= $animal['edad'] ?> años</p>
                            <p><strong>Sexo:</strong> <?= $animal['sexo'] ?></p>
                            <p><strong>Centro:</strong> <?= htmlspecialchars($animal['centro_nombre']) ?></p>
                        </div>
                        
                        <?php if ($tiene_solicitud): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Ya tienes una solicitud pendiente para este animal.
                                <a href="<?= BASE_URL ?>/perfil/" class="alert-link">Ver estado</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-home me-2"></i>
                            Centro responsable
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6><?= htmlspecialchars($animal['centro_nombre']) ?></h6>
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= htmlspecialchars($animal['centro_direccion']) ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-phone me-1"></i>
                            <?= htmlspecialchars($animal['centro_telefono']) ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-envelope me-1"></i>
                            <?= htmlspecialchars($animal['centro_email']) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-file-signature me-2"></i>
                            Formulario de Adopción
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <div class="mt-3">
                                    <a href="<?= BASE_URL ?>/perfil/" class="btn btn-success me-2">
                                        Ver mis solicitudes
                                    </a>
                                    <a href="<?= BASE_URL ?>/animales" class="btn btn-outline-success">
                                        Ver más animales
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$success && !$tiene_solicitud): ?>
                            <div class="info-box mb-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Tus datos
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']) ?></p>
                                        <p><strong>Dirección:</strong> <?= htmlspecialchars($usuario['direccion']) ?></p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="<?= BASE_URL ?>/perfil/" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i>Actualizar datos
                                    </a>
                                </div>
                            </div>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="motivacion" class="form-label">
                                        <strong>¿Por qué quieres adoptar a <?= htmlspecialchars($animal['nombre']) ?>? *</strong>
                                    </label>
                                    <textarea class="form-control" id="motivacion" name="motivacion" rows="3" required
                                              placeholder="Explica por qué quieres adoptar este animal..."><?= $_POST['motivacion'] ?? '' ?></textarea>
                                    <div class="form-text">Sé lo más detallado posible. Esta información es importante para el centro.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="experiencia" class="form-label">
                                        <strong>¿Tienes experiencia con animales? *</strong>
                                    </label>
                                    <textarea class="form-control" id="experiencia" name="experiencia" rows="3" required
                                              placeholder="Describe tu experiencia previa con animales..."><?= $_POST['experiencia'] ?? '' ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="vivienda" class="form-label">
                                            <strong>Tipo de vivienda *</strong>
                                        </label>
                                        <select class="form-select" id="vivienda" name="vivienda" required>
                                            <option value="">Selecciona...</option>
                                            <option value="Casa con jardín" <?= ($_POST['vivienda'] ?? '') == 'Casa con jardín' ? 'selected' : '' ?>>Casa con jardín</option>
                                            <option value="Casa sin jardín" <?= ($_POST['vivienda'] ?? '') == 'Casa sin jardín' ? 'selected' : '' ?>>Casa sin jardín</option>
                                            <option value="Piso" <?= ($_POST['vivienda'] ?? '') == 'Piso' ? 'selected' : '' ?>>Piso</option>
                                            <option value="Ático" <?= ($_POST['vivienda'] ?? '') == 'Ático' ? 'selected' : '' ?>>Ático</option>
                                            <option value="Dúplex" <?= ($_POST['vivienda'] ?? '') == 'Dúplex' ? 'selected' : '' ?>>Dúplex</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="horas_solo" class="form-label">
                                            <strong>¿Cuántas horas pasaría solo al día? *</strong>
                                        </label>
                                        <select class="form-select" id="horas_solo" name="horas_solo" required>
                                            <option value="">Selecciona...</option>
                                            <option value="Menos de 4 horas" <?= ($_POST['horas_solo'] ?? '') == 'Menos de 4 horas' ? 'selected' : '' ?>>Menos de 4 horas</option>
                                            <option value="4-8 horas" <?= ($_POST['horas_solo'] ?? '') == '4-8 horas' ? 'selected' : '' ?>>4-8 horas</option>
                                            <option value="Más de 8 horas" <?= ($_POST['horas_solo'] ?? '') == 'Más de 8 horas' ? 'selected' : '' ?>>Más de 8 horas</option>
                                            <option value="Nunca está solo" <?= ($_POST['horas_solo'] ?? '') == 'Nunca está solo' ? 'selected' : '' ?>>Nunca está solo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="otros_animales" class="form-label">
                                        <strong>¿Tienes otros animales en casa?</strong>
                                    </label>
                                    <textarea class="form-control" id="otros_animales" name="otros_animales" rows="2"
                                              placeholder="Indica qué otros animales tienes (especie, edad, sexo)..."><?= $_POST['otros_animales'] ?? '' ?></textarea>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file-contract me-2"></i>
                                            Términos y Condiciones
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                        <p>Al enviar esta solicitud, aceptas:</p>
                                        <ul>
                                            <li>Proporcionar todos los cuidados necesarios al animal</li>
                                            <li>Realizar seguimientos veterinarios periódicos</li>
                                            <li>No utilizar al animal para cría o fines comerciales</li>
                                            <li>Permitir visitas de seguimiento del centro</li>
                                            <li>En caso de no poder continuar, devolver el animal al centro</li>
                                            <li>Asumir todos los gastos derivados de la adopción</li>
                                        </ul>
                                        <p class="mb-0">Para más detalles, consulta los <a href="<?= BASE_URL ?>/terminos.php" target="_blank">términos completos</a>.</p>
                                    </div>
                                    <div class="card-footer">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos" value="1"
                                                   <?= ($_POST['acepto_terminos'] ?? '') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="acepto_terminos">
                                                <strong>Acepto los términos y condiciones de adopción *</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Enviar Solicitud de Adopción
                                    </button>
                                    <a href="ver.php?id=<?= $id_animal ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Volver a la ficha del animal
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Sobre el proceso de adopción
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <div class="rounded-circle bg-success bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-file-alt fa-xl text-success"></i>
                                    </div>
                                    <h6>1. Solicitud</h6>
                                    <p class="small text-muted">Envía el formulario y espera la respuesta del centro</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <div class="rounded-circle bg-success bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-calendar-check fa-xl text-success"></i>
                                    </div>
                                    <h6>2. Entrevista</h6>
                                    <p class="small text-muted">El centro contactará contigo para una entrevista</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <div class="rounded-circle bg-success bg-opacity-25 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="fas fa-home fa-xl text-success"></i>
                                    </div>
                                    <h6>3. Adopción</h6>
                                    <p class="small text-muted">Firma del contrato y recogida del animal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const terminos = document.getElementById('acepto_terminos');
                    if (!terminos.checked) {
                        e.preventDefault();
                        alert('Debes aceptar los términos y condiciones para continuar');
                        terminos.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>