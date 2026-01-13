<?php
require_once '../includes/config.php';
obtenerUsuario();
$id_animal = isset($_GET['id']) ? (int)$_GET['id'] : 0;
//Obtener información del animal
$stmt = $pdo->prepare("SELECT * FROM animales WHERE id_animal = ?");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch();

if (!$animal || $animal['estado'] != 'Disponible') {
    header('Location: ' . BASE_URL . '/animales');
    exit();
}
//Verificar si ya tiene solicitud
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM adopciones 
    WHERE id_usuario = ? AND id_animal = ? AND estado IN ('Pendiente', 'Aprobada')
");
$stmt->execute([$_SESSION['user_id'], $id_animal]);
if ($stmt->fetchColumn() > 0) {
    header('Location: ' . BASE_URL . '/animales/ver/' . $id_animal);
    exit();
}
//Obtener datos del usuario
$usuario = obtenerUsuario();
//Procesar formulario
$mensaje = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notas = trim($_POST['notas'] ?? '');
    $experiencia = trim($_POST['experiencia'] ?? '');
    $vivienda = $_POST['vivienda'] ?? '';
    $otros_animales = $_POST['otros_animales'] ?? '';
    //Validaciones
    if (empty($experiencia)) {
        $error = 'Por favor, describe tu experiencia con animales';
    } elseif (empty($vivienda)) {
        $error = 'Por favor, selecciona el tipo de vivienda';
    } else {
        try {
            $pdo->beginTransaction(); 
            // Crear solicitud de adopción
            $stmt = $pdo->prepare("
                INSERT INTO adopciones (id_usuario, id_animal, notas, fecha_solicitud, estado)
                VALUES (?, ?, ?, NOW(), 'Pendiente')
            ");       
            $notas_completas = "Experiencia previa: $experiencia\n";
            $notas_completas .= "Tipo de vivienda: $vivienda\n";
            $notas_completas .= "Otros animales en casa: $otros_animales\n";
            $notas_completas .= "Notas adicionales: $notas";        
            $stmt->execute([$_SESSION['usuario_id'], $id_animal, $notas_completas]);         
            //Actualizar estado del animal
            $stmt = $pdo->prepare("
                UPDATE animales SET estado = 'Reservado' WHERE id_animal = ?
            ");
            $stmt->execute([$id_animal]);
            $pdo->commit();
            $mensaje = '¡Solicitud enviada correctamente! El centro se pondrá en contacto contigo.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error al procesar la solicitud: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar adopción - AdoptaWeb</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <!--Progreso -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span class="text-success">
                        <i class="fas fa-check-circle me-1"></i>Animal seleccionado
                    </span>
                    <span class="text-success fw-bold">
                        <i class="fas fa-check-circle me-1"></i>Formulario de adopción
                    </span>
                    <span class="text-muted">
                        <i class="fas fa-clock me-1"></i>Confirmación
                    </span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-heart me-2"></i>
                            Solicitar adopción de <?= htmlspecialchars($animal['nombre']) ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $mensaje ?>
                            </div>
                            <div class="text-center mt-4">
                                <a href="<?= BASE_URL ?>/perfil/adopciones" class="btn btn-success me-2">
                                    <i class="fas fa-list me-1"></i>Ver mis solicitudes
                                </a>
                                <a href="<?= BASE_URL ?>/animales" class="btn btn-outline-success">
                                    <i class="fas fa-paw me-1"></i>Buscar más animales
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>   
                            <!--Información del animal -->
                            <div class="card mb-4 border-success">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <img src="<?= $animal['imagen_url'] ?: ASSETS_URL . 'img/animales/default.jpg' ?>" 
                                                 class="img-fluid rounded" 
                                                 alt="<?= htmlspecialchars($animal['nombre']) ?>">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="mb-1"><?= htmlspecialchars($animal['nombre']) ?></h5>
                                            <p class="text-muted mb-2">
                                                <?= $animal['especie'] ?> · 
                                                <?= $animal['edad'] ?> años · 
                                                <?= $animal['sexo'] ?>
                                            </p>
                                            <p class="mb-0 small">
                                                <?= nl2br(htmlspecialchars($animal['descripcion'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>              
                            <!--Formulario -->
                            <form method="POST">
                                <h5 class="mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Tus datos
                                </h5>                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre completo</label>
                                            <input type="text" class="form-control" 
                                                   value="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" 
                                                   value="<?= htmlspecialchars($usuario['email']) ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" 
                                                   value="<?= htmlspecialchars($usuario['telefono']) ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Dirección</label>
                                            <input type="text" class="form-control" 
                                                   value="<?= htmlspecialchars($usuario['direccion']) ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                </div>                
                                <h5 class="mb-3">
                                    <i class="fas fa-home me-2"></i>
                                    Información sobre tu hogar
                                </h5>                  
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">¿Qué tipo de vivienda tienes? *</label>
                                            <select class="form-select" name="vivienda" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="Piso">Piso</option>
                                                <option value="Casa con patio">Casa con patio</option>
                                                <option value="Casa con jardín">Casa con jardín</option>
                                                <option value="Ático">Ático</option>
                                                <option value="Dúplex">Dúplex</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">¿Tienes otros animales en casa?</label>
                                            <select class="form-select" name="otros_animales">
                                                <option value="">Selecciona una opción</option>
                                                <option value="No">No</option>
                                                <option value="Sí, perros">Sí, perros</option>
                                                <option value="Sí, gatos">Sí, gatos</option>
                                                <option value="Sí, perros y gatos">Sí, perros y gatos</option>
                                                <option value="Sí, otros animales">Sí, otros animales</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>                      
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-paw me-1"></i>
                                        Describe tu experiencia con animales *
                                    </label>
                                    <textarea class="form-control" name="experiencia" 
                                              rows="4" 
                                              placeholder="¿Has tenido mascotas antes? ¿Qué experiencia tienes?" 
                                              required></textarea>
                                </div>                         
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-edit me-1"></i>
                                        Notas adicionales (opcional)
                                    </label>
                                    <textarea class="form-control" name="notas" 
                                              rows="4" 
                                              placeholder="Cualquier información adicional que quieras compartir con el centro..."></textarea>
                                </div>                         
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Proceso de adopción
                                    </h6>
                                    <p class="mb-2 small">
                                        1. El centro revisará tu solicitud en 2-3 días hábiles<br>
                                        2. Te contactarán para una entrevista (presencial o telefónica)<br>
                                        3. Si todo está bien, se coordinará la entrega<br>
                                        4. Habrá seguimiento post-adopción durante 6 meses
                                    </p>
                                </div>                      
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="terminos" required>
                                    <label class="form-check-label" for="terminos">
                                        Acepto los 
                                        <a href="<?= BASE_URL ?>/terminos" target="_blank">términos y condiciones</a> 
                                        y comprendo que esta es una solicitud, no una adopción confirmada.
                                    </label>
                                </div>                        
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Enviar solicitud de adopción
                                    </button>
                                    <a href="<?= BASE_URL ?>/animales/ver/<?= $id_animal ?>" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Volver a la ficha del animal
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>