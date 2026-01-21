<?php
require_once '../includes/config.php';
//Verificar autenticación y permisos de administrador
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

//Consultar todos los pagos de adopciones con información relacionada
$pagos = $pdo->query("
    SELECT 
        ap.*, 
        u.nombre as usuario_nombre, 
        u.email as usuario_email,
        a.id_adopcion,
        an.id_animal,
        an.nombre as animal_nombre,
        an.especie,
        an.raza,
        c.nombre as centro_nombre,
        f.numero_factura,
        f.pdf_dirr,
        f.fecha_emision,
        p.id_pago,
        p.monto as pago_monto,
        p.concepto as pago_concepto,
        p.fecha_pago as pago_fecha
    FROM adopciones_pagos ap
    JOIN usuarios u ON ap.id_usuario = u.id_usuario
    JOIN adopciones a ON ap.id_adopcion = a.id_adopcion
    JOIN animales an ON a.id_animal = an.id_animal
    JOIN centros c ON an.id_centro = c.id_centro
    LEFT JOIN pagos p ON p.id_usuario = ap.id_usuario 
        AND DATE(p.fecha_pago) = DATE(ap.fecha_pago)
    LEFT JOIN facturas f ON f.id_pago = p.id_pago
    ORDER BY ap.fecha_creacion DESC
")->fetchAll();

//Procesar cada pago para agregar información de vacunas
foreach ($pagos as &$pago) {
    //Consultar vacunas aplicadas al animal
    $stmt = $pdo->prepare("
        SELECT 
            v.nombre, 
            v.descripcion, 
            v.precio,
            av.fecha_aplicacion,
            av.fecha_proxima
        FROM animal_vacunas av
        JOIN vacunas v ON av.id_vacuna = v.id_vacuna
        WHERE av.id_animal = ?
        ORDER BY av.fecha_aplicacion DESC, v.nombre
    ");
    $stmt->execute([$pago['id_animal']]);
    $pago['vacunas'] = $stmt->fetchAll();
    
    //Calcular total directo de las vacunas
    $calculo_directo = 0;
    foreach ($pago['vacunas'] as $vacuna) {
        $calculo_directo += $vacuna['precio'];
    }
    
    //Almacenar cálculos para verificación
    $pago['calculo_directo'] = $calculo_directo;
    $pago['monto_correcto'] = number_format($calculo_directo, 2);
    $pago['discrepancia'] = abs($calculo_directo - $pago['monto']) > 0.01;
}
unset($pago); //Limpiar referencia del último elemento
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos - Admin</title>
    <!--Enlaces a estilos externos-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
    <style>
        .badge-pendiente {
            background-color: #ffc107; color: #212529; 
        }
        .badge-pagado { 
            background-color: #28a745; 
        }
        .badge-cancelado { 
            background-color: #dc3545; 
        }
        .vacunas-list { 
            max-height: 200px; 
            overflow-y: auto; 
            border: 1px solid #dee2e6; 
            padding: 10px; 
            border-radius: 5px; 
            background-color: #f8f9fa; 
        }
        .vacuna-item { 
            padding: 8px 0; 
            border-bottom: 1px solid #e9ecef; 
        }
        .vacuna-item:last-child { 
            border-bottom: none; 
        }
        .collapse-icon { 
            transition: transform 0.3s; 
        }
        .collapsed .collapse-icon { 
            transform: rotate(-90deg); 
        }
        .factura-btn { 
            min-width: 120px; 
        }
        .discrepancia-warning { 
            background-color: #fff3cd; 
            border-left: 4px solid #ffc107; 
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <!--Contenido principal-->
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-credit-card me-2"></i>Gestión de Pagos</h2>
            
            <!--Tarjeta principal de pagos-->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pagos de Adopciones</h5>
                    <span class="badge bg-info"><?= count($pagos) ?> pagos</span>
                </div>
                <div class="card-body">
                    <?php if (count($pagos) > 0): ?>
                    <div class="table-responsive">
                        <!--Tabla de pagos-->
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Animal / Centro</th>
                                    <th>Vacunas incluidas</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Factura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagos as $p): ?>
                                <tr class="<?= $p['discrepancia'] ? 'discrepancia-warning' : '' ?>">
                                    <!--ID del pago y adopción-->
                                    <td>
                                        <strong>#<?= $p['id'] ?></strong><br>
                                        <small class="text-muted">Adopción #<?= $p['id_adopcion'] ?></small>
                                    </td>
                                    
                                    <!--Información del usuario-->
                                    <td>
                                        <strong><?= htmlspecialchars($p['usuario_nombre']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($p['usuario_email']) ?></small>
                                    </td>
                                    
                                    <!--Información del animal y centro-->
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($p['animal_nombre']) ?></strong><br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($p['especie']) ?> - 
                                                <?= htmlspecialchars($p['raza']) ?><br>
                                                Centro: <?= htmlspecialchars($p['centro_nombre']) ?>
                                            </small>
                                        </div>
                                    </td>
                                    
                                    <!--Sección de vacunas-->
                                    <td>
                                        <button class="btn btn-sm btn-outline-info mb-2" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#vacunas<?= $p['id'] ?>"
                                                aria-expanded="false" 
                                                aria-controls="vacunas<?= $p['id'] ?>">
                                            <i class="fas fa-syringe collapse-icon"></i>
                                            <?= count($p['vacunas']) ?> vacuna(s)
                                        </button>
                                        
                                        <!--Lista colapsable de vacunas-->
                                        <div class="collapse" id="vacunas<?= $p['id'] ?>">
                                            <div class="vacunas-list">
                                                <?php if (count($p['vacunas']) > 0): ?>
                                                    <?php foreach ($p['vacunas'] as $vacuna): ?>
                                                    <div class="vacuna-item">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="w-75">
                                                                <strong><?= htmlspecialchars($vacuna['nombre']) ?></strong><br>
                                                                <small class="text-muted">
                                                                    <?= htmlspecialchars(substr($vacuna['descripcion'], 0, 50)) ?>...
                                                                </small>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="text-primary fw-bold"><?= number_format($vacuna['precio'], 2) ?> €</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                    <!--Total calculado de vacunas-->
                                                    <div class="vacuna-item pt-2 mt-2 border-top">
                                                        <div class="d-flex justify-content-between fw-bold">
                                                            <span>Total calculado:</span>
                                                            <span class="text-success"><?= $p['monto_correcto'] ?> €</span>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!--Mensaje si no hay vacunas-->
                                                    <div class="text-center text-muted py-3">
                                                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                                        No hay vacunas registradas
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!--Monto del pago con verificación-->
                                    <td>
                                        <div class="text-end">
                                            <span class="h5"><?= number_format($p['monto'], 2) ?> €</span><br>
                                            <small class="text-muted">
                                                <?php if ($p['discrepancia']): ?>
                                                <!--Advertencia de discrepancia-->
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> Monto incorrecto<br>
                                                    Debería ser: <?= $p['monto_correcto'] ?> €
                                                </span>
                                                <?php else: ?>
                                                <!--Confirmación de monto correcto-->
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle"></i> Monto correcto
                                                </span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </td>
                                    
                                    <!--Estado del pago-->
                                    <td>
                                        <span class="badge badge-<?= strtolower($p['estado']) ?>">
                                            <?= $p['estado'] ?>
                                        </span><br>
                                        <small class="text-muted">
                                            <?php if ($p['fecha_pago']): ?>
                                                Pagado: <?= date('d/m/Y', strtotime($p['fecha_pago'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    
                                    <!--Fechas del pago-->
                                    <td>
                                        <small>
                                            Creación:<br>
                                            <?= date('d/m/Y H:i', strtotime($p['fecha_creacion'])) ?>
                                            <?php if ($p['fecha_pago']): ?>
                                                <br>Pago:<br>
                                                <?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    
                                    <!--Información de factura-->
                                    <td>
                                        <?php if ($p['pdf_dirr']): ?>
                                            <!--Enlace para descargar factura-->
                                            <a href="<?= BASE_URL ?>/facturas/descargar.php?f=<?= urlencode($p['pdf_dirr']) ?>" 
                                               class="btn btn-sm btn-success factura-btn" 
                                               title="Descargar factura #<?= htmlspecialchars($p['numero_factura']) ?>">
                                                <i class="fas fa-download me-1"></i> Descargar
                                            </a>
                                            <small class="d-block text-muted mt-1">
                                                <?= htmlspecialchars($p['numero_factura']) ?><br>
                                                <?= date('d/m/Y', strtotime($p['fecha_emision'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Sin factura</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <!--Mensaje cuando no hay pagos-->
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay pagos registrados</h4>
                            <p class="text-muted">No se han realizado pagos de adopciones aún.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Scripts de Bootstrap y animación para iconos colapsables-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const collapseButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    //Configurar transición para iconos de colapso
    collapseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('.collapse-icon');
            if (icon) {
                icon.style.transition = 'transform 0.3s';
            }
        });
    });
});
</script>
</body>
</html>