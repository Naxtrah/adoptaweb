<?php
$usuario = obtenerUsuario();
$script_name = $_SERVER['SCRIPT_NAME'];
$pagina_actual = basename($script_name, '.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/index.php">
            <i class="fas fa-paw me-2 fa-lg"></i>
            <strong>AdoptaWeb</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPrincipal">
            <ul class="navbar-nav me-auto">
                <!--Inicio-->
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual == 'index') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/index.php">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>
                <!--Animales-->
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($script_name, 'animales') !== false) ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/animales/index.php">
                        <i class="fas fa-paw me-1"></i>Animales
                    </a>
                </li>
                <!--Centros-->
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($script_name, 'centros') !== false) ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/centros/index.php">
                        <i class="fas fa-home me-1"></i>Centros
                    </a>
                </li>
                <!--Mapa-->
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual == 'mapa') ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/mapa.php">
                        <i class="fas fa-map-marked-alt me-1"></i>Mapa
                    </a>
                </li>
                <!--Panel de control de admin-->
                <?php if (estaLogueado() && esAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link text-warning <?= (strpos($script_name, 'admin') !== false) ? 'active' : '' ?>" 
                       href="<?= BASE_URL ?>/admin/index.php">
                        <i class="fas fa-cog me-1"></i>Panel Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="navbar-nav">
                <?php if (estaLogueado()): ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" 
                           href="#" id="dropdownUsuario" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-circle bg-light text-success me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?= htmlspecialchars($usuario['nombre']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUsuario">
                            <!--Enlaces opciones usuarios-->
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/perfil/index.php">
                                    <i class="fas fa-user-circle me-2"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/perfil/adopciones.php">
                                    <i class="fas fa-heart me-2"></i>Mis Adopciones
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/perfil/datos.php">
                                    <i class="fas fa-edit me-2"></i>Editar Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-light me-2" href="<?= BASE_URL ?>/auth/login.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                    </a>
                    <a class="btn btn-light" href="<?= BASE_URL ?>/auth/registro.php">
                        <i class="fas fa-user-plus me-1"></i>Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<!--Estilos css-->
<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
</style>