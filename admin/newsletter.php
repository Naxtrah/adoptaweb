<<<<<<< HEAD
<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}
//Código para descargar la newsletter
if (isset($_GET['descargar'])) {
    $stmt = $pdo->query("SELECT email, fecha_registro FROM newsletter WHERE activo = 1 ORDER BY fecha_registro DESC");
    $emails = $stmt->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Fecha de Registro']);
    foreach ($emails as $e) fputcsv($output, [$e['email'], $e['fecha_registro']]);
    fclose($output);
    exit();
}
$stmt = $pdo->query("SELECT email, fecha_registro, activo FROM newsletter ORDER BY fecha_registro DESC");
$suscriptores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Newsletter - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-newspaper me-2"></i>Suscriptores Newsletter</h2>
            <a href="?descargar=1" class="btn btn-success mb-3">Descargar CSV</a>
            <table class="table table-bordered">
                <thead class="table-light"><tr><th>Email</th><th>Fecha de Registro</th><th>Activo</th></tr></thead>
                <tbody>
                    <?php foreach ($suscriptores as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['fecha_registro'])) ?></td>
                        <td><?= $s['activo'] ? 'Sí' : 'No' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
=======
<?php
require_once '../includes/config.php';
if (!estaLogueado() || !esAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

if (isset($_GET['descargar'])) {
    $stmt = $pdo->query("SELECT email, fecha_registro FROM newsletter WHERE activo = 1 ORDER BY fecha_registro DESC");
    $emails = $stmt->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Fecha de Registro']);
    foreach ($emails as $e) fputcsv($output, [$e['email'], $e['fecha_registro']]);
    fclose($output);
    exit();
}

$stmt = $pdo->query("SELECT email, fecha_registro, activo FROM newsletter ORDER BY fecha_registro DESC");
$suscriptores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Newsletter - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 p-4">
            <h2><i class="fas fa-newspaper me-2"></i>Suscriptores Newsletter</h2>
            <a href="?descargar=1" class="btn btn-success mb-3">Descargar CSV</a>
            <table class="table table-bordered">
                <thead class="table-light"><tr><th>Email</th><th>Fecha de Registro</th><th>Activo</th></tr></thead>
                <tbody>
                    <?php foreach ($suscriptores as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($s['fecha_registro'])) ?></td>
                        <td><?= $s['activo'] ? 'Sí' : 'No' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
</html>