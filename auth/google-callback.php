<<<<<<< HEAD
<?php
require_once '../includes/config.php';
require_once '../includes/google-auth.php';

$google_auth = new GoogleAuth();

if (isset($_GET['code'])) {
    $user_data = $google_auth->authenticate($_GET['code']);

    if ($user_data) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$user_data['email']]);
        $user = $stmt->fetch();

        if (!$user) {
            $password_hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_data['nombre'], $user_data['apellido'], $user_data['email'], $password_hash]);
            $user_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        }

        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['id_rol'];

        header('Location: ' . BASE_URL . '/perfil/');
        exit();
    }
}

header('Location: ' . BASE_URL . '/auth/login.php?error=google_auth');
=======
<?php
require_once '../includes/config.php';
require_once '../includes/google-auth.php';

$google_auth = new GoogleAuth();

if (isset($_GET['code'])) {
    $user_data = $google_auth->authenticate($_GET['code']);

    if ($user_data) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$user_data['email']]);
        $user = $stmt->fetch();

        if (!$user) {
            $password_hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_data['nombre'], $user_data['apellido'], $user_data['email'], $password_hash]);
            $user_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        }

        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['id_rol'];

        header('Location: ' . BASE_URL . '/perfil/');
        exit();
    }
}

header('Location: ' . BASE_URL . '/auth/login.php?error=google_auth');
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
exit();