<?php
function generarPDFFactura($datos, $rutaSalida)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Factura ' . $datos['numero'] . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .header { text-align: center; margin-bottom: 30px; }
            .info { margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .total { font-weight: bold; font-size: 1.2em; }
            .footer { margin-top: 50px; text-align: center; font-size: 0.8em; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>FACTURA</h1>
            <h2>AdoptaWeb</h2>
        </div>
        
        <div class="info">
            <p><strong>Número:</strong> ' . $datos['numero'] . '</p>
            <p><strong>Fecha:</strong> ' . $datos['fecha'] . '</p>
            <p><strong>Cliente:</strong> ' . $datos['cliente'] . '</p>
            <p><strong>Concepto:</strong> ' . $datos['concepto'] . '</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Vacuna</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($datos['vacunas'] as $v) {
        $html .= '<tr><td>' . $v['nombre'] . '</td><td>' . $v['precio'] . ' €</td></tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="total" style="text-align: right; margin-top: 20px;">
            TOTAL: ' . $datos['monto'] . ' €
        </div>
        
        <div class="footer">
            <p>AdoptaWeb - Plataforma de adopción animal</p>
            <p>Este documento es una factura generada automáticamente</p>
        </div>
    </body>
    </html>';
    
    file_put_contents($rutaSalida . '.html', $html);
    
    return true;
}
?>