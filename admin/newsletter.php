<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}


if (isset($_GET['descargar'])) {
    $stmt = $pdo->query("SELECT email, fecha_registro, activo FROM newsletter WHERE activo = 1 ORDER BY fecha_registro DESC");
    $suscriptores = $stmt->fetchAll();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=suscriptores_newsletter_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Fecha Registro', 'Estado']);
    
    foreach ($suscriptores as $suscriptor) {
        fputcsv($output, [
            $suscriptor['email'],
            $suscriptor['fecha_registro'],
            $suscriptor['activo'] ? 'Activo' : 'Inactivo'
        ]);
    }
    fclose($output);
    exit();
}


if (isset($_GET['descargar_todos'])) {
    $stmt = $pdo->query("SELECT email, fecha_registro, activo FROM newsletter ORDER BY fecha_registro DESC");
    $suscriptores = $stmt->fetchAll();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=suscriptores_completos_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Fecha Registro', 'Estado']);
    
    foreach ($suscriptores as $suscriptor) {
        fputcsv($output, [
            $suscriptor['email'],
            $suscriptor['fecha_registro'],
            $suscriptor['activo'] ? 'Activo' : 'Inactivo'
        ]);
    }
    fclose($output);
    exit();
}


$total_suscriptores = $pdo->query("SELECT COUNT(*) FROM newsletter")->fetchColumn();
$suscriptores_activos = $pdo->query("SELECT COUNT(*) FROM newsletter WHERE activo = 1")->fetchColumn();
$suscriptores_inactivos = $total_suscriptores - $suscriptores_activos;


$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 20;
$inicio = ($pagina - 1) * $por_pagina;

$stmt = $pdo->prepare("SELECT * FROM newsletter ORDER BY fecha_registro DESC LIMIT :inicio, :por_pagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$suscriptores = $stmt->fetchAll();

$total_paginas = ceil($total_suscriptores / $por_pagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Newsletter - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stats-card {
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .badge-activo {
            background-color: #28a745;
            color: white;
        }
        .badge-inactivo {
            background-color: #6c757d;
            color: white;
        }
        .email-cell {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-newspaper me-2"></i>Suscriptores Newsletter</h2>
                <div class="btn-group">
                    <a href="?descargar=1" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Descargar CSV (activos)
                    </a>
                    <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Opciones</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?descargar=1">
                            <i class="fas fa-user-check me-2"></i>Solo suscriptores activos
                        </a></li>
                        <li><a class="dropdown-item" href="?descargar_todos=1">
                            <i class="fas fa-users me-2"></i>Todos los suscriptores
                        </a></li>
                    </ul>
                </div>
            </div>
            
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stats-card border-primary">
                        <div class="card-body text-center">
                            <h1 class="display-4 text-primary"><?= $total_suscriptores ?></h1>
                            <p class="text-muted mb-0">Total Suscriptores</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card border-success">
                        <div class="card-body text-center">
                            <h1 class="display-4 text-success"><?= $suscriptores_activos ?></h1>
                            <p class="text-muted mb-0">Activos</p>
                            <small><?= $total_suscriptores > 0 ? round(($suscriptores_activos / $total_suscriptores) * 100, 1) : 0 ?>%</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card border-secondary">
                        <div class="card-body text-center">
                            <h1 class="display-4 text-secondary"><?= $suscriptores_inactivos ?></h1>
                            <p class="text-muted mb-0">Inactivos</p>
                            <small><?= $total_suscriptores > 0 ? round(($suscriptores_inactivos / $total_suscriptores) * 100, 1) : 0 ?>%</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Suscriptores</h5>
                    <span class="badge bg-info">Página <?= $pagina ?> de <?= $total_paginas ?></span>
                </div>
                <div class="card-body">
                    <?php if (count($suscriptores) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Email</th>
                                        <th>Fecha Registro</th>
                                        <th>Estado</th>
                                        <th>Última Actualización</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador = $inicio + 1;
                                    foreach ($suscriptores as $s): 
                                    ?>
                                    <tr>
                                        <td><?= $contador++ ?></td>
                                        <td class="email-cell" title="<?= htmlspecialchars($s['email']) ?>">
                                            <?= htmlspecialchars($s['email']) ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($s['fecha_registro'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($s['fecha_registro'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $s['activo'] ? 'badge-activo' : 'badge-inactivo' ?>">
                                                <?= $s['activo'] ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                            <?php if ($s['activo']): ?>
                                                <br><small class="text-success"><i class="fas fa-bell"></i> Recibe noticias</small>
                                            <?php else: ?>
                                                <br><small class="text-muted"><i class="fas fa-bell-slash"></i> No recibe</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                            $fecha_registro = new DateTime($s['fecha_registro']);
                                            $hoy = new DateTime();
                                            $diferencia = $hoy->diff($fecha_registro);
                                            
                                            if ($diferencia->y > 0) {
                                                echo $diferencia->y . ' año(s)';
                                            } elseif ($diferencia->m > 0) {
                                                echo $diferencia->m . ' mes(es)';
                                            } elseif ($diferencia->d > 0) {
                                                echo $diferencia->d . ' día(s)';
                                            } else {
                                                echo 'Hoy';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if ($total_paginas > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagina > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">
                                            <i class="fas fa-chevron-left"></i> Anterior
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php 
                                $inicio_pagina = max(1, $pagina - 2);
                                $fin_pagina = min($total_paginas, $inicio_pagina + 4);
                                
                                if ($inicio_pagina > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=1">1</a>
                                    </li>
                                    <?php if ($inicio_pagina > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $inicio_pagina; $i <= $fin_pagina; $i++): ?>
                                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($fin_pagina < $total_paginas): ?>
                                    <?php if ($fin_pagina < $total_paginas - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?= $total_paginas ?>"><?= $total_paginas ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($pagina < $total_paginas): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">
                                            Siguiente <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-1">No hay suscriptores en la lista</h5>
                                    <p class="mb-0">Todavía no hay suscriptores registrados en el newsletter.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    const emailCells = document.querySelectorAll('.email-cell');
    
    emailCells.forEach(cell => {
        new bootstrap.Tooltip(cell);
    });
});
</script>
</body>
</html>