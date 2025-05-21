<?php
require_once '../../config/setup.php';
requireAuth();

// Autoload Composer (importante, solo una vez aquí)
require_once '../../vendor/autoload.php';

if (!isset($_GET['id']) || !isset($_GET['format'])) {
    die("Parámetros inválidos.");
}

$invoice_id = intval($_GET['id']);
$format = strtolower($_GET['format']);

// Buscar factura y su info básica
$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT i.*, s.total, s.sale_date, c.first_name, c.last_name
                        FROM invoices i
                        JOIN sales s ON i.sale_id = s.id
                        JOIN clients c ON s.client_id = c.id
                        WHERE i.id = ?");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die("Factura no encontrada.");
}

// PDF
if ($format === 'pdf') {
    // Usa la versión de composer para FPDF
    $fpdfPath = '../../vendor/setasign/fpdf/fpdf.php';
    if (file_exists($fpdfPath)) {
        require_once $fpdfPath;
    } else {
        die('FPDF no está instalado correctamente');
    }

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,10,'Factura: ' . $invoice['invoice_number']);
    $pdf->Ln(10);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(40,10,'Cliente: ' . $invoice['last_name'] . ' ' . $invoice['first_name']);
    $pdf->Ln(8);
    $pdf->Cell(40,10,'Fecha: ' . $invoice['sale_date']);
    $pdf->Ln(8);
    $pdf->Cell(40,10,'Total: $' . number_format($invoice['total'],2));
    $pdf->Output('D', 'factura_'.$invoice['invoice_number'].'.pdf');
    exit;
}

// EXCEL (.xlsx)
if ($format === 'xlsx') {
    // Usa la versión de composer para phpspreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Factura');
    $sheet->setCellValue('B1', $invoice['invoice_number']);
    $sheet->setCellValue('A2', 'Cliente');
    $sheet->setCellValue('B2', $invoice['last_name'] . ' ' . $invoice['first_name']);
    $sheet->setCellValue('A3', 'Fecha');
    $sheet->setCellValue('B3', $invoice['sale_date']);
    $sheet->setCellValue('A4', 'Total');
    $sheet->setCellValue('B4', $invoice['total']);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="factura_'.$invoice['invoice_number'].'.xlsx"');
    header('Cache-Control: max-age=0');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// XML
if ($format === 'xml') {
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="factura_'.$invoice['invoice_number'].'.xml"');

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<factura>';
    echo '<folio>' . htmlspecialchars($invoice['invoice_number']) . '</folio>';
    echo '<cliente>' . htmlspecialchars($invoice['last_name'].' '.$invoice['first_name']) . '</cliente>';
    echo '<fecha>' . htmlspecialchars($invoice['sale_date']) . '</fecha>';
    echo '<total>' . number_format($invoice['total'],2) . '</total>';
    echo '</factura>';
    exit;
}

// Otro formato (no soportado)
die("Formato no soportado.");
