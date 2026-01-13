<?php
require_once '../includes/config.php';
//Manejo errores
$error = '';
$success = '';
//Verificacion con post ya que estamos manejando contraseñas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizar($_POST['email']);
    $password = $_POST['password'];
    //Buscar usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    //Más verificaciones
    if ($user) {
        if ($password === 'demo123' || password_verify($password, $user['password_hash'])) {
            //Crear sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['id_rol'];
            // Redirección
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
            if ($user['id_rol'] == 1) {
                header('Location: ' . BASE_URL . '/admin/');
            } elseif (!empty($redirect)) {
                header('Location: ' . urldecode($redirect));
            } else {
                header('Location: ' . BASE_URL . '/perfil/');
            }
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "No existe una cuenta con ese email";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                       required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Contraseña
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                </button>
                            </div>
                            <div class="text-center">
                                <p class="mb-2">
                                    ¿No tienes cuenta? 
                                    <a href="<?= BASE_URL ?>/auth/registro.php" class="text-success">
                                        Regístrate aquí
                                    </a>
                                </p>
                                <p class="mb-0">
                                    <a href="#" class="text-muted small">¿Olvidaste tu contraseña?</a>
                                </p>
                            </div>
                        </form>
                        <hr class="my-4">
                        <div class="text-center">
                            <p class="text-muted small mb-2">Credenciales de prueba:</p>
                            <div class="border rounded p-3 bg-light">
                                <p class="mb-1"><strong>Admin:</strong> admin@adoptaweb.com / demo123</p>
                                <p class="mb-0"><strong>Usuario:</strong> ana@gmail.com / demo123</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>