<?php
require_once '../../config/setup.php';
requireAuth();
require_once '../../vendor/autoload.php';

// Quita acentos y pone mayúsculas
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

    // -- ESTILOS --
    $color_hero_bg = [30,58,138];       // azul hero
    $color_card_bg = [243,244,246];     // gris claro card
    $color_table_head = [55,65,81];     // gris tabla
    $color_table_alt = [229,231,235];   // gris alternado
    $color_table_row = [255,255,255];   // blanco fila normal

    // HERO - CABECERA AZUL
    $pdf->SetFillColor(...$color_hero_bg);
    $pdf->Rect(0,0,210,40,'F');
    $pdf->SetFont('Arial','B',12); // Tamaño uniforme
    $pdf->SetTextColor(255,255,255);
    $pdf->SetXY(0,18);
    $pdf->Cell(210,10,'FACTURA',0,1,'C');

    // Sombra simulada abajo del hero (línea sutil)
    $pdf->SetDrawColor(200,200,255);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10,40,200,40);

    // -- BLOQUE DATOS PRINCIPALES (tipo card) --
    $pdf->SetXY(10,45);
    $pdf->SetFillColor(...$color_card_bg);
    $pdf->SetDrawColor(235,235,235);
    $pdf->Rect(10,45,190,34,'F');
    $pdf->SetFont('Arial','B',12);
    $pdf->SetTextColor(30,58,138);

    $pdf->SetXY(15,50);
    $pdf->Cell(32,8,'FOLIO:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(35,8,sin_acentos_mayus($invoice['invoice_number']),0,0,'L');

    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->Cell(24,8,'FECHA:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(0,8,sin_acentos_mayus($invoice['sale_date']),0,1,'L');

    $pdf->SetXY(15,57);
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->Cell(32,8,'RFC:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(50,8,sin_acentos_mayus($invoice['rfc']),0,0,'L');

    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->Cell(26,8,'CLIENTE:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(0,8,sin_acentos_mayus(trim($invoice['last_name'].' '.$invoice['mother_last_name'].' '.$invoice['first_name'])),0,1,'L');

    // -- BLOQUE INFO CONTACTO --
    $pdf->SetXY(10,82);
    $pdf->SetFillColor(...$color_card_bg);
    $pdf->Rect(10,82,190,22,'F');

    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->SetXY(15,86); $pdf->Cell(30,8,'TELEFONO:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(45,8,sin_acentos_mayus($invoice['phone']),0,0,'L');

    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->Cell(17,8,'EMAIL:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(0,8,sin_acentos_mayus($invoice['email']),0,1,'L');

    // DIRECCION EN CARD PROPIA
    $pdf->SetXY(10,110);
    $pdf->SetFillColor(...$color_card_bg);
    $pdf->Rect(10,110,190,16,'F');
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->SetXY(15,114); $pdf->Cell(28,8,'DIRECCION:',0,0,'L');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(60,60,60);
    $pdf->Cell(0,8,sin_acentos_mayus($invoice['address']),0,1,'L');

    // --- TABLA DE PRODUCTOS ---
    $tableY = 135;
    $pdf->SetXY(10, $tableY);
    $pdf->SetFont('Arial','B',12);
    $pdf->SetFillColor(...$color_table_head);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(60,10,'PRODUCTO',1,0,'C',true);
    $pdf->Cell(25,10,'CANT.',1,0,'C',true);
    $pdf->Cell(35,10,'P. UNIT.',1,0,'C',true);
    $pdf->Cell(20,10,'IVA',1,0,'C',true);
    $pdf->Cell(40,10,'SUBTOTAL',1,1,'C',true);

    // Tabla con filas alternadas
    $pdf->SetFont('Arial','',12);
    $i = 0;
    foreach ($detalles as $d) {
        if ($i % 2 == 0) {
            $pdf->SetFillColor(...$color_table_alt);
        } else {
            $pdf->SetFillColor(...$color_table_row);
        }
        $pdf->SetTextColor(45,45,45);
        $pdf->Cell(60,10,sin_acentos_mayus($d['name']),1,0,'L',true);
        $pdf->Cell(25,10,$d['quantity'],1,0,'C',true);
        $pdf->Cell(35,10,'$'.number_format($d['unit_price'],2),1,0,'R',true);
        $pdf->Cell(20,10,number_format($d['tax_rate'],2).'%',1,0,'C',true);
        $pdf->Cell(40,10,'$'.number_format($d['subtotal'],2),1,1,'R',true);
        $i++;
    }

    // --- TOTALES (bloques tipo resumen de web) ---
    $totY = $pdf->GetY() + 7;
    $pdf->SetXY(85, $totY);
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(120,120,120);
    $pdf->Cell(65,10,'SUBTOTAL:',0,0,'R');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(30,58,138);
    $pdf->Cell(40,10,'$'.number_format($invoice['subtotal'],2),0,1,'R');

    $pdf->SetXY(85, $pdf->GetY());
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(120,120,120);
    $pdf->Cell(65,10,'IVA ('.number_format($invoice['tax_percentage'],2).'%)',0,0,'R');
    $pdf->SetFont('Arial','',12); $pdf->SetTextColor(46,112,232);
    $pdf->Cell(40,10,'$'.number_format($invoice['tax_amount'],2),0,1,'R');

    $pdf->SetXY(85, $pdf->GetY()+1);
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(30,58,138);
    $pdf->Cell(65,10,'TOTAL:',0,0,'R');
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor(22,163,74); // verde
    $pdf->Cell(40,10,'$'.number_format($invoice['total'],2),0,1,'R');

    // --- Pie de página ---
    $pdf->SetY(-22);
    $pdf->SetDrawColor(220,220,220);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->SetFont('Arial','I',10); // Footer pequeño, cambia a 12 si lo prefieres
    $pdf->SetTextColor(160,160,160);
    $pdf->Cell(0,9,utf8_decode('Gracias por su preferencia. Generado con Swift Invoice.'),0,1,'C');

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
