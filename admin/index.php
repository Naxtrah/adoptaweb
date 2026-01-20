<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
//Stats de la página
$stats = [
    'total_animales' => $pdo->query("SELECT COUNT(*) FROM animales")->fetchColumn(),
    'animales_disponibles' => $pdo->query("SELECT COUNT(*) FROM animales WHERE estado = 'Disponible'")->fetchColumn(),
    'total_usuarios' => $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'usuarios_nuevos_mes' => $pdo->query("SELECT COUNT(*) FROM usuarios WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    'total_adopciones' => $pdo->query("SELECT COUNT(*) FROM adopciones")->fetchColumn(),
    'adopciones_pendientes' => $pdo->query("SELECT COUNT(*) FROM adopciones WHERE estado = 'Pendiente'")->fetchColumn(),
    'adopciones_este_mes' => $pdo->query("SELECT COUNT(*) FROM adopciones WHERE fecha_solicitud >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
    'total_centros' => $pdo->query("SELECT COUNT(*) FROM centros")->fetchColumn()
];
//Contar las adopciones totales en un mes
$adopciones_mes = $pdo->query("
    SELECT DATE_FORMAT(fecha_solicitud, '%Y-%m') as mes, COUNT(*) as total
    FROM adopciones 
    WHERE fecha_solicitud >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(fecha_solicitud, '%Y-%m')
    ORDER BY mes DESC
")->fetchAll();
//Animales pendiendes datos
$pendientes = $pdo->query("
    SELECT a.*, u.nombre as usuario_nombre, u.email, an.nombre as animal_nombre
    FROM adopciones a
    JOIN usuarios u ON a.id_usuario = u.id_usuario
    JOIN animales an ON a.id_animal = an.id_animal
    WHERE a.estado = 'Pendiente'
    ORDER BY a.fecha_solicitud DESC
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - AdoptaWeb</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt me-2"></i>Panel de Control</h2>
                <div class="text-muted"><i class="fas fa-user-shield me-1"></i>Administrador: <?= htmlspecialchars($_SESSION['user_name']) ?></div>
            </div>
            <div class="row mb-4">
                <div class="col-md-3"><div class="card border-success"><div class="card-body"><div class="d-flex justify-content-between"><div><h6 class="text-muted">Animales</h6><h3><?= $stats['total_animales'] ?></h3></div><div class="icon-circle bg-success"><i class="fas fa-paw text-white"></i></div></div><small class="text-muted"><?= $stats['animales_disponibles'] ?> disponibles</small></div></div></div>
                <div class="col-md-3"><div class="card border-primary"><div class="card-body"><div class="d-flex justify-content-between"><div><h6 class="text-muted">Usuarios</h6><h3><?= $stats['total_usuarios'] ?></h3></div><div class="icon-circle bg-primary"><i class="fas fa-users text-white"></i></div></div><small class="text-muted"><?= $stats['usuarios_nuevos_mes'] ?> nuevos este mes</small></div></div></div>
                <div class="col-md-3"><div class="card border-warning"><div class="card-body"><div class="d-flex justify-content-between"><div><h6 class="text-muted">Adopciones</h6><h3><?= $stats['total_adopciones'] ?></h3></div><div class="icon-circle bg-warning"><i class="fas fa-heart text-white"></i></div></div><small class="text-muted"><?= $stats['adopciones_pendientes'] ?> pendientes</small></div></div></div>
                <div class="col-md-3"><div class="card border-info"><div class="card-body"><div class="d-flex justify-content-between"><div><h6 class="text-muted">Centros</h6><h3><?= $stats['total_centros'] ?></h3></div><div class="icon-circle bg-info"><i class="fas fa-home text-white"></i></div></div><small class="text-muted">Colaboradores</small></div></div></div>
            </div>
            <div class="row">
                <div class="col-md-8"><div class="card"><div class="card-header"><h5 class="mb-0">Adopciones últimos 6 meses</h5></div><div class="card-body"><canvas id="adopcionesChart" height="150"></canvas></div></div></div>
                <div class="col-md-4"><div class="card"><div class="card-header bg-warning"><h5 class="mb-0 text-white">Adopciones pendientes</h5></div><div class="card-body p-0"><?php if (count($pendientes) > 0): ?><div class="list-group list-group-flush"><?php foreach ($pendientes as $pendiente): ?><div class="list-group-item"><div class="d-flex justify-content-between align-items-center"><div><h6 class="mb-1"><?= htmlspecialchars($pendiente['animal_nombre']) ?></h6><small class="text-muted"><?= htmlspecialchars($pendiente['usuario_nombre']) ?></small></div><a href="adopciones.php?accion=revisar&id=<?= $pendiente['id_adopcion'] ?>" class="btn btn-sm btn-outline-warning">Revisar</a></div></div><?php endforeach; ?></div><?php else: ?><div class="text-center py-4"><i class="fas fa-check-circle fa-2x text-success mb-3"></i><p class="text-muted mb-0">No hay adopciones pendientes</p></div><?php endif; ?></div></div></div>
            </div>
        </div>
    </div>
</div>
<script>
/*Inserción de todos los datos obtenidos con json*/
const ctx = document.getElementById('adopcionesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($adopciones_mes, 'mes')) ?>,
        datasets: [{
            label: 'Adopciones',
            data: <?= json_encode(array_column($adopciones_mes, 'total')) ?>,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
document.querySelectorAll('.icon-circle').forEach(icon => {
    icon.style.width = '50px';
    icon.style.height = '50px';
    icon.style.borderRadius = '50%';
    icon.style.display = 'flex';
    icon.style.alignItems = 'center';
    icon.style.justifyContent = 'center';
});
</script>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>