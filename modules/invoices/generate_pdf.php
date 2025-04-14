<?php
require_once '../../config/setup.php';
requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de factura no válido');
}

require_once '../../vendor/autoload.php';

$db = new Database();
$conn = $db->connect();

// Obtener información de la factura
$stmt = $conn->prepare("
    SELECT i.*, s.sale_date, s.subtotal, s.tax_percentage, s.tax_amount, s.total,
           CONCAT(c.last_name, ' ', c.first_name) AS client_name,
           c.rfc AS client_rfc, c.address AS client_address,
           co.business_name AS company_name, co.rfc AS company_rfc,
           co.fiscal_address AS company_address, co.logo_path,
           co.tax_regime, co.legal_representative
    FROM invoices i
    JOIN sales s ON i.sale_id = s.id
    JOIN clients c ON s.client_id = c.id
    CROSS JOIN companies co
    WHERE i.id = :id
    LIMIT 1
");
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    die('Factura no encontrada');
}

$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener detalles de la venta
$stmt = $conn->prepare("
    SELECT sd.*, p.name AS product_name
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    WHERE sd.sale_id = :sale_id
");
$stmt->bindParam(':sale_id', $invoice['sale_id']);
$stmt->execute();
$sale_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'Letter',
    'default_font_size' => 12,
    'default_font' => 'helvetica',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 40,
    'margin_bottom' => 20,
    'margin_header' => 10,
    'margin_footer' => 10
]);

// Configurar encabezado y pie de página
$mpdf->SetHTMLHeader('
<div style="text-align: right; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
    <strong>' . htmlspecialchars($invoice['company_name']) . '</strong><br>
    ' . htmlspecialchars($invoice['company_rfc']) . '
</div>
');

$mpdf->SetHTMLFooter('
<table width="100%">
    <tr>
        <td width="70%">
            <strong>Notas:</strong><br>
            Esta factura es válida como comprobante fiscal.<br>
            Forma de pago: Pago en una sola exhibición
        </td>
        <td width="30%" style="text-align: right;">
            Página {PAGENO} de {nb}
        </td>
    </tr>
</table>
');

// Contenido del PDF
$html = '
<style>
    .invoice-title {
        text-align: center;
        margin-bottom: 20px;
    }
    .invoice-info {
        margin-bottom: 30px;
    }
    .parties {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .company, .client {
        width: 48%;
    }
    .company {
        border-right: 1px solid #ddd;
        padding-right: 15px;
    }
    .client {
        padding-left: 15px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    th {
        background-color: #f5f5f5;
        text-align: left;
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }
    td {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }
    .text-right {
        text-align: right;
    }
    .total-row {
        font-weight: bold;
        border-top: 2px solid #ddd;
    }
    .logo {
        text-align: center;
        margin-bottom: 20px;
    }
    .logo img {
        max-height: 80px;
    }
</style>

<div class="logo">
    <img src="file://' . realpath('../../' . $invoice['logo_path']) . '" alt="Logo">
</div>

<div class="invoice-title">
    <h1>Factura</h1>
    <p><strong>Número:</strong> ' . $invoice['invoice_number'] . '</p>
    <p><strong>Fecha:</strong> ' . $invoice['invoice_date'] . '</p>
</div>

<div class="parties">
    <div class="company">
        <h3>Emisor</h3>
        <p><strong>' . $invoice['company_name'] . '</strong></p>
        <p><strong>RFC:</strong> ' . $invoice['company_rfc'] . '</p>
        <p><strong>Dirección Fiscal:</strong><br>' . nl2br($invoice['company_address']) . '</p>
        <p><strong>Régimen Fiscal:</strong> ' . $invoice['tax_regime'] . '</p>
        <p><strong>Representante Legal:</strong> ' . $invoice['legal_representative'] . '</p>
    </div>
    
    <div class="client">
        <h3>Receptor</h3>
        <p><strong>' . $invoice['client_name'] . '</strong></p>
        <p><strong>RFC:</strong> ' . ($invoice['client_rfc'] ?? 'N/A') . '</p>
        <p><strong>Dirección:</strong><br>' . nl2br($invoice['client_address'] ?? 'N/A') . '</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th width="10%">Cantidad</th>
            <th width="50%">Descripción</th>
            <th width="20%">Precio Unitario</th>
            <th width="20%">Importe</th>
        </tr>
    </thead>
    <tbody>';

foreach ($sale_details as $detail) {
    $html .= '
        <tr>
            <td>' . $detail['quantity'] . '</td>
            <td>' . $detail['product_name'] . '</td>
            <td>$' . number_format($detail['unit_price'], 2) . '</td>
            <td>$' . number_format($detail['subtotal'], 2) . '</td>
        </tr>';
}

$html .= '
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
            <td>$' . number_format($invoice['subtotal'], 2) . '</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">
                <strong>IVA (' . $invoice['tax_percentage'] . '%):</strong>
            </td>
            <td>$' . number_format($invoice['tax_amount'], 2) . '</td>
        </tr>
        <tr class="total-row">
            <td colspan="3" class="text-right"><strong>Total:</strong></td>
            <td>$' . number_format($invoice['total'], 2) . '</td>
        </tr>
    </tfoot>
</table>

<div style="margin-top: 50px;">
    <div style="float: left; width: 50%; text-align: center;">
        <p>___________________________</p>
        <p>Receptor</p>
    </div>
    <div style="float: right; width: 50%; text-align: center;">
        <p>___________________________</p>
        <p>Emisor</p>
    </div>
</div>
';

$mpdf->WriteHTML($html);

// Generar nombre del archivo
$filename = 'Factura_' . $invoice['invoice_number'] . '.pdf';

// Descargar o mostrar el PDF
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
?>