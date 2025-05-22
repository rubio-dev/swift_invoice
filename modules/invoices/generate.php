<?php
require_once '../../config/setup.php';
requireAuth();
require_once '../../vendor/autoload.php';

// QUITA ACENTOS Y PONE MAYUSCULAS
function sin_acentos_mayus($str) {
    $str = mb_strtoupper($str, 'UTF-8');
    $unwanted = ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N','Ü'=>'U',
                 'á'=>'A','é'=>'E','í'=>'I','ó'=>'O','ú'=>'U','ñ'=>'N','ü'=>'U'];
    return strtr($str, $unwanted);
}

if (!isset($_GET['id']) || !isset($_GET['format'])) {
    die("PARAMETROS INVALIDOS.");
}

$invoice_id = intval($_GET['id']);
$format = strtolower($_GET['format']);

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT 
        i.*, 
        s.total, s.sale_date, s.tax_percentage, s.tax_amount, s.subtotal,
        c.first_name, c.last_name, c.mother_last_name, c.rfc, c.phone, c.email, c.address
    FROM invoices i
    JOIN sales s ON i.sale_id = s.id
    JOIN clients c ON s.client_id = c.id
    WHERE i.id = ?
");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) die("FACTURA NO ENCONTRADA.");

$stmt = $conn->prepare("
    SELECT p.name, sd.quantity, sd.unit_price, sd.tax_rate, sd.subtotal
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    WHERE sd.sale_id = ?
");
$stmt->execute([$invoice['sale_id']]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === PDF ===
if ($format === 'pdf') {
    $fpdfPath = '../../vendor/setasign/fpdf/fpdf.php';
    if (file_exists($fpdfPath)) require_once $fpdfPath;
    else die('FPDF NO ESTA INSTALADO CORRECTAMENTE');

    $pdf = new FPDF();
    $pdf->AddPage();

    // ENCABEZADO AZUL
    $pdf->SetFillColor(46,112,232);
    $pdf->Rect(20,20,170,30,'F');
    $pdf->SetXY(20,28);
    $pdf->SetFont('Arial','B',28);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(170,10,'FACTURA',0,1,'C');
    $pdf->Ln(12);

    // Info tamaño uniforme
    $pdf->SetTextColor(30,30,30);
    $pdf->SetFont('Arial','',13);
    $bold = 'B'; $normal = '';
    $espacio = 9; $startX = 20;

    // Primera fila
    $pdf->SetX($startX);
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(18,$espacio,'FOLIO:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->Cell(45,$espacio,sin_acentos_mayus($invoice['invoice_number']),0,0,'L');
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(16,$espacio,'FECHA:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->Cell(40,$espacio,sin_acentos_mayus($invoice['sale_date']),0,1,'L');
    // RFC
    $pdf->SetX($startX);
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(13,$espacio,'RFC:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->Cell(0,$espacio,sin_acentos_mayus($invoice['rfc']),0,1,'L');
    // Cliente
    $pdf->SetX($startX);
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(22,$espacio,'CLIENTE:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->Cell(0,$espacio,sin_acentos_mayus(trim($invoice['last_name'].' '.$invoice['mother_last_name'].' '.$invoice['first_name'])),0,1,'L');
    // Telefono
    $pdf->SetX($startX);
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(24,$espacio,'TELEFONO:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->Cell(0,$espacio,sin_acentos_mayus($invoice['phone']),0,1,'L');
    // Correo
    $pdf->SetX($startX);
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(21,$espacio,'CORREO:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->MultiCell(0,$espacio,sin_acentos_mayus($invoice['email']),0,'L');
    // Direccion
    $pdf->SetX($startX);
    $pdf->SetFont('Arial',$bold,13); $pdf->Cell(27,$espacio,'DIRECCION:',0,0,'L');
    $pdf->SetFont('Arial',$normal,13); $pdf->MultiCell(0,$espacio,sin_acentos_mayus($invoice['address']),0,'L');
    $pdf->Ln(3);

    // TABLA PRODUCTOS
    $pdf->SetFont('Arial','B',13);
    $pdf->SetFillColor(18,26,46);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(70,12,'PRODUCTO',1,0,'C',true);
    $pdf->Cell(25,12,'CANTIDAD',1,0,'C',true);
    $pdf->Cell(35,12,'PRECIO UNIT.',1,0,'C',true);
    $pdf->Cell(20,12,'IVA',1,0,'C',true);
    $pdf->Cell(35,12,'SUBTOTAL',1,1,'C',true);

    $pdf->SetFont('Arial','',13);
    $pdf->SetTextColor(30,30,30);

    foreach ($detalles as $d) {
        $pdf->Cell(70,10,sin_acentos_mayus($d['name']),1,0,'L');
        $pdf->Cell(25,10,$d['quantity'],1,0,'C');
        $pdf->Cell(35,10,'$'.number_format($d['unit_price'],2),1,0,'R');
        $pdf->Cell(20,10,number_format($d['tax_rate'],2).'%',1,0,'C');
        $pdf->Cell(35,10,'$'.number_format($d['subtotal'],2),1,1,'R');
    }

    // TOTALES
    $pdf->SetFont('Arial','B',13);
    $pdf->SetFillColor(255,255,255);

    $pdf->Cell(150,12,'SUBTOTAL',1,0,'R');
    $pdf->SetTextColor(18,26,46); 
    $pdf->Cell(35,12,'$'.number_format($invoice['subtotal'],2),1,1,'R');
    $pdf->SetTextColor(30,30,30);

    $pdf->Cell(150,12,'IVA ('.number_format($invoice['tax_percentage'],2).'%)',1,0,'R');
    $pdf->SetTextColor(46,112,232);
    $pdf->Cell(35,12,'$'.number_format($invoice['tax_amount'],2),1,1,'R');
    $pdf->SetTextColor(30,30,30);

    $pdf->SetFont('Arial','B',13);
    $pdf->SetFillColor(255,255,255);
    $pdf->Cell(150,13,'TOTAL',1,0,'R');
    $pdf->SetTextColor(0,102,204);
    $pdf->Cell(35,13,'$'.number_format($invoice['total'],2),1,1,'R');
    $pdf->SetTextColor(30,30,30);

    // FOOTER
    $pdf->Ln(10);
    $pdf->SetFont('Arial','I',11);
    $pdf->SetTextColor(180,180,180);
    $pdf->Cell(0,8,utf8_decode('Hecho por Swift Invoice'),0,1,'C');

    $pdf->Output('D', 'factura_'.$invoice['invoice_number'].'.pdf');
    exit;
}

// === EXCEL (.xlsx) ===
if ($format === 'xlsx') {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'FACTURA');
    $sheet->setCellValue('B1', sin_acentos_mayus($invoice['invoice_number']));
    $sheet->setCellValue('A2', 'CLIENTE');
    $sheet->setCellValue('B2', sin_acentos_mayus(trim($invoice['last_name'].' '.$invoice['mother_last_name'].' '.$invoice['first_name'])));
    $sheet->setCellValue('A3', 'RFC');
    $sheet->setCellValue('B3', sin_acentos_mayus($invoice['rfc']));
    $sheet->setCellValue('A4', 'DIRECCION');
    $sheet->setCellValue('B4', sin_acentos_mayus($invoice['address']));
    $sheet->setCellValue('A5', 'TELEFONO');
    $sheet->setCellValue('B5', sin_acentos_mayus($invoice['phone']));
    $sheet->setCellValue('A6', 'CORREO');
    $sheet->setCellValue('B6', sin_acentos_mayus($invoice['email']));
    $sheet->setCellValue('A7', 'FECHA');
    $sheet->setCellValue('B7', sin_acentos_mayus($invoice['sale_date']));
    $sheet->setCellValue('A8', 'TOTAL');
    $sheet->setCellValue('B8', $invoice['total']);
    // Tabla de productos
    $row = 10;
    $sheet->setCellValue('A'.$row, 'PRODUCTO');
    $sheet->setCellValue('B'.$row, 'CANTIDAD');
    $sheet->setCellValue('C'.$row, 'PRECIO UNITARIO');
    $sheet->setCellValue('D'.$row, 'IVA');
    $sheet->setCellValue('E'.$row, 'SUBTOTAL');
    $row++;
    foreach ($detalles as $d) {
        $sheet->setCellValue('A'.$row, sin_acentos_mayus($d['name']));
        $sheet->setCellValue('B'.$row, $d['quantity']);
        $sheet->setCellValue('C'.$row, $d['unit_price']);
        $sheet->setCellValue('D'.$row, $d['tax_rate']);
        $sheet->setCellValue('E'.$row, $d['subtotal']);
        $row++;
    }
    $sheet->setCellValue('D'.($row+1), 'SUBTOTAL');
    $sheet->setCellValue('E'.($row+1), $invoice['subtotal']);
    $sheet->setCellValue('D'.($row+2), 'IVA ('.$invoice['tax_percentage'].'%)');
    $sheet->setCellValue('E'.($row+2), $invoice['tax_amount']);
    $sheet->setCellValue('D'.($row+3), 'TOTAL');
    $sheet->setCellValue('E'.($row+3), $invoice['total']);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="factura_'.$invoice['invoice_number'].'.xlsx"');
    header('Cache-Control: max-age=0');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// === XML ===
if ($format === 'xml') {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<factura>';
    $xml .= '<folio>' . sin_acentos_mayus($invoice['invoice_number']) . '</folio>';
    $xml .= '<cliente>' . sin_acentos_mayus($invoice['last_name'].' '.$invoice['mother_last_name'].' '.$invoice['first_name']) . '</cliente>';
    $xml .= '<rfc>' . sin_acentos_mayus($invoice['rfc']) . '</rfc>';
    $xml .= '<direccion>' . sin_acentos_mayus($invoice['address']) . '</direccion>';
    $xml .= '<telefono>' . sin_acentos_mayus($invoice['phone']) . '</telefono>';
    $xml .= '<correo>' . sin_acentos_mayus($invoice['email']) . '</correo>';
    $xml .= '<fecha>' . sin_acentos_mayus($invoice['sale_date']) . '</fecha>';
    $xml .= '<total>' . number_format($invoice['total'],2) . '</total>';
    $xml .= '<productos>';
    foreach ($detalles as $d) {
        $xml .= '<producto>';
        $xml .= '<nombre>' . sin_acentos_mayus($d['name']) . '</nombre>';
        $xml .= '<cantidad>' . $d['quantity'] . '</cantidad>';
        $xml .= '<precio_unitario>' . number_format($d['unit_price'],2) . '</precio_unitario>';
        $xml .= '<iva>' . number_format($d['tax_rate'],2) . '</iva>';
        $xml .= '<subtotal>' . number_format($d['subtotal'],2) . '</subtotal>';
        $xml .= '</producto>';
    }
    $xml .= '</productos>';
    $xml .= '</factura>';
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="factura_'.$invoice['invoice_number'].'.xml"');
    echo $xml;
    exit;
}

die("FORMATO NO SOPORTADO.");
?>
