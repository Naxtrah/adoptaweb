<?php
require_once '../includes/config.php';
//Verificar autenticación y permisos de administrador
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

//Procesar archivo de mensaje de contacto
if (isset($_GET['archivar']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("UPDATE contactos SET estado = 'Archivado' WHERE id_contacto = ?")->execute([$id]);
    header("Location: contactos.php");
    exit();
}

//Consultar todos los mensajes de contacto ordenados por fecha
$stmt = $pdo->query("SELECT * FROM contactos ORDER BY fecha_contacto DESC");
$mensajes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contactos - Admin</title>
    <!--Enlaces a estilos externos-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <!--Contenido principal de gestión de contactos-->
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-envelope me-2"></i>Mensajes de Contacto</h2>
            <!--Tabla de mensajes de contacto-->
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mensajes as $m): ?>
                    <tr>
                        <!--Nombre del remitente-->
                        <td><?= htmlspecialchars($m['nombre']) ?></td>
                        
                        <!--Email del remitente-->
                        <td><?= htmlspecialchars($m['email']) ?></td>
                        
                        <!--Asunto del mensaje-->
                        <td><?= htmlspecialchars($m['asunto']) ?></td>
                        
                        <!--Fecha y hora del mensaje-->
                        <td><?= date('d/m/Y H:i', strtotime($m['fecha_contacto'])) ?></td>
                        
                        <!--Estado del mensaje con indicador visual-->
                        <td>
                            <span class="badge bg-<?= $m['estado'] == 'Pendiente' ? 'warning' : 'secondary' ?>">
                                <?= $m['estado'] ?>
                            </span>
                        </td>
                        
                        <!--Acciones disponibles para cada mensaje-->
                        <td>
                            <!--Enlace para ver detalles del mensaje-->
                            <a href="contactos_ver.php?id=<?= $m['id_contacto'] ?>" class="btn btn-sm btn-info">Ver</a>
                            
                            <!--Botón de archivar solo para mensajes pendientes-->
                            <?php if ($m['estado'] == 'Pendiente'): ?>
                            <a href="?archivar=1&id=<?= $m['id_contacto'] ?>" class="btn btn-sm btn-secondary">Archivar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>