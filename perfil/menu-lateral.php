<?php

$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Mi Cuenta</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="<?= BASE_URL ?>/perfil/index.php" class="list-group-item list-group-item-action <?= $pagina_actual == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-user-circle me-2"></i>Resumen
        </a>
        <a href="<?= BASE_URL ?>/perfil/mis-datos.php" class="list-group-item list-group-item-action <?= $pagina_actual == 'mis-datos.php' ? 'active' : '' ?>">
            <i class="fas fa-user-edit me-2"></i>Mis Datos
        </a>
        <a href="<?= BASE_URL ?>/perfil/mis-adopciones.php" class="list-group-item list-group-item-action <?= $pagina_actual == 'mis-adopciones.php' ? 'active' : '' ?>">
            <i class="fas fa-paw me-2"></i>Mis Adopciones
        </a>
        <a href="<?= BASE_URL ?>/perfil/mis-pagos.php" class="list-group-item list-group-item-action <?= $pagina_actual == 'mis-pagos.php' ? 'active' : '' ?>">
            <i class="fas fa-credit-card me-2"></i>Mis Pagos
        </a>
      <a href="<?= BASE_URL ?>/perfil/cambiar-password.php" class="list-group-item list-group-item-action <?= $pagina_actual == 'cambiar-password.php' ? 'active' : '' ?>">
    <i class="fas fa-lock me-2"></i>Cambiar Contraseña
</a>
    </div>
</div>