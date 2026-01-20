<<<<<<< HEAD
<?php
require_once 'config.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
/*Validación del correo para el registro a la newsletter e inserción del registro a la base de datos*/
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email requerido']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter (email, fecha_registro) VALUES (?, NOW())");
    $stmt->execute([$email]);
    
    echo json_encode(['success' => true, 'message' => 'Suscripción exitosa']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Este email ya está suscrito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    }
=======
<?php
require_once 'config.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email requerido']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter (email, fecha_registro) VALUES (?, NOW())");
    $stmt->execute([$email]);
    
    echo json_encode(['success' => true, 'message' => 'Suscripción exitosa']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Este email ya está suscrito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    }
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
}