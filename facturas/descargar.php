<?php
require_once '../includes/config.php';
//Verificar autenticación del usuario
if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

//Obtener nombre de archivo de factura desde parámetro GET
$f = basename($_GET['f'] ?? '');
if (!$f) {
    $_SESSION['error'] = "Factura no especificada";
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

//Extraer número de factura del nombre de archivo
$numero_factura = pathinfo($f, PATHINFO_FILENAME);

//Consultar información de la factura desde base de datos
$stmt = $pdo->prepare("
    SELECT 
        f.*,
        p.id_usuario,
        p.concepto,
        p.monto,
        p.fecha_pago,
        u.nombre AS usuario_nombre,
        u.apellido AS usuario_apellido,
        pd.id_animal,
        an.nombre AS animal_nombre
    FROM facturas f
    JOIN pagos p ON f.id_pago = p.id_pago
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    LEFT JOIN pagos_detalle pd ON p.id_pago = pd.id_pago
    LEFT JOIN animales an ON pd.id_animal = an.id_animal
    WHERE f.numero_factura = ?
    LIMIT 1
");
$stmt->execute([$numero_factura]);
$factura = $stmt->fetch();

//Validar existencia de la factura
if (!$factura) {
    $_SESSION['error'] = "Factura no encontrada";
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

//Verificar permisos: usuario propietario o administrador
if ($factura['id_usuario'] != $_SESSION['user_id'] && !esAdmin()) {
    $_SESSION['error'] = "No tienes permiso para acceder a esta factura";
    header('Location: ' . BASE_URL . '/perfil/');
    exit();
}

//Ruta del archivo PDF
$pdfDir = __DIR__ . '/';
$pdfFile = $pdfDir . $f;

//Generar PDF si no existe
if (!file_exists($pdfFile)) {
    //Consultar detalles de vacunas si es factura de adopción
    $stmtVacunas = $pdo->prepare("
        SELECT 
            v.nombre,
            v.precio
        FROM pagos_detalle pd
        JOIN vacunas v ON pd.id_vacuna = v.id_vacuna
        WHERE pd.id_pago = ?
    ");
    $stmtVacunas->execute([$factura['id_pago']]);
    $vacunas_lista = $stmtVacunas->fetchAll();

    //Usar concepto general si no hay vacunas específicas
    if (empty($vacunas_lista)) {
        $vacunas_lista = [['nombre' => $factura['concepto'], 'precio' => $factura['monto']]];
    }

    //Incluir librería para generar PDF
    require_once __DIR__ . '/../includes/pdf.php';
    
    //Preparar datos para la factura
    $datos = [
        'numero' => $factura['numero_factura'],
        'fecha' => date('d/m/Y', strtotime($factura['fecha_emision'])),
        'cliente' => trim($factura['usuario_nombre'] . ' ' . $factura['usuario_apellido']),
        'concepto' => $factura['concepto'] . ($factura['animal_nombre'] ? ' - ' . $factura['animal_nombre'] : ''),
        'monto' => number_format($factura['monto'], 2),
        'vacunas' => $vacunas_lista
    ];
    
    //Generar PDF y manejar errores
    if (!generarPDFFactura($datos, $pdfFile)) {
        $_SESSION['error'] = "Error al generar la factura";
        header('Location: ' . BASE_URL . '/perfil/');
        exit();
    }
}

//Enviar archivo PDF al navegador para descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $f . '"');
header('Content-Length: ' . filesize($pdfFile));
readfile($pdfFile);
exit;
?>