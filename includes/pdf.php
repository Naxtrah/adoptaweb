<?php
require_once __DIR__ . '/tcpdf/tcpdf.php';

//Función principal para generar PDF de factura
function generarPDFFactura($datos, $rutaSalida)
{
    //Crear nueva instancia de TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    //Configurar metadatos del documento
    $pdf->SetCreator('AdoptaWeb');
    $pdf->SetAuthor('AdoptaWeb');
    $pdf->SetTitle('Factura ' . $datos['numero']);
    $pdf->SetSubject('Factura de adopción');
    $pdf->SetKeywords('AdoptaWeb, Factura, Adopción');
    
    //Deshabilitar cabecera y pie de página por defecto
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    //Agregar nueva página
    $pdf->AddPage();
    
    //Construir contenido HTML del PDF
    $html = '
    <style>
        body { font-family: helvetica, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .logo { color: #28a745; font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .titulo { font-size: 18px; font-weight: bold; margin: 10px 0; }
        .info { margin: 15px 0; }
        .cliente { background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .tabla { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .tabla th { background-color: #28a745; color: white; padding: 8px; text-align: left; }
        .tabla td { padding: 8px; border-bottom: 1px solid #ddd; }
        .total { text-align: right; font-size: 16px; font-weight: bold; margin-top: 20px; color: #28a745; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
    
    <!--Encabezado de la factura-->
    <div class="header">
        <div class="logo">AdoptaWeb</div>
        <div class="titulo">FACTURA</div>
        <div style="font-size: 14px;">Número: ' . $datos['numero'] . '</div>
        <div style="font-size: 12px;">Fecha: ' . $datos['fecha'] . '</div>
    </div>
    
    <!--Datos del cliente y concepto-->
    <div class="cliente">
        <strong>Cliente:</strong> ' . $datos['cliente'] . '<br>
        <strong>Concepto:</strong> ' . $datos['concepto'] . '
    </div>
    
    <!--Tabla de detalles de la factura-->
    <table class="tabla">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>';
    
    //Agregar items de vacunas o concepto general
    if (!empty($datos['vacunas'])) {
        foreach ($datos['vacunas'] as $v) {
            $html .= '<tr><td>' . htmlspecialchars($v['nombre']) . '</td><td>' . number_format($v['precio'], 2) . ' €</td></tr>';
        }
    } else {
        $html .= '<tr><td>' . htmlspecialchars($datos['concepto']) . '</td><td>' . $datos['monto'] . ' €</td></tr>';
    }
    
    $html .= '</tbody>
    </table>
    
    <!--Total de la factura-->
    <div class="total">
        TOTAL: ' . $datos['monto'] . ' €
    </div>
    
    <!--Pie de página con información de la empresa-->
    <div class="footer">
        <p>AdoptaWeb - Plataforma de adopción animal</p>
        <p>CIF: B12345678 - Calle Ejemplo 123, 28001 Madrid</p>
        <p>Tel: 910 123 456 - Email: info@adoptaweb.com</p>
        <p>Este documento es una factura generada automáticamente.</p>
    </div>';
    
    //Escribir contenido HTML en el PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    //Guardar PDF en archivo
    $pdf->Output($rutaSalida, 'F');
    
    return true;
}
?>