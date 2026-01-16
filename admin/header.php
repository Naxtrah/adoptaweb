<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand text-warning fw-bold" href="index.php">
            <i class="fas fa-user-shield me-2"></i>Panel Admin
        </a>
        <div class="ms-auto d-flex align-items-center text-white">
            <span class="me-3"><i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
            </a>
        </div>
    </div>
</nav>