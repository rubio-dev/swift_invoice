<?php
require_once '../../config/setup.php';
requireAuth();

$db = new Database();
$conn = $db->connect();

if (!isset($_GET['sale_id'])) {
    $_SESSION['error_message'] = 'Venta no especificada.';
    header('Location: ../sales/');
    exit;
}

$sale_id = intval($_GET['sale_id']);

// 1. Verifica que la venta existe
$stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    $_SESSION['error_message'] = 'Venta no encontrada.';
    header('Location: ../sales/');
    exit;
}

// 2. Verifica que NO existe ya una factura para esa venta
$stmt = $conn->prepare("SELECT * FROM invoices WHERE sale_id = ?");
$stmt->execute([$sale_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if ($invoice) {
    $_SESSION['error_message'] = 'Esta venta ya está facturada.';
    header('Location: ../sales/');
    exit;
}

// 3. Genera el siguiente número de factura (básico, puedes mejorarlo según tus reglas)
$stmt = $conn->query("SELECT MAX(id) AS max_id FROM invoices");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$next_id = ($row && $row['max_id']) ? ($row['max_id'] + 1) : 1;
$invoice_number = 'F001-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

// 4. Inserta la factura (ajusta campos si cambias la tabla)
$stmt = $conn->prepare("
    INSERT INTO invoices (sale_id, invoice_number, invoice_date, created_at)
    VALUES (?, ?, CURDATE(), NOW())
");
$stmt->execute([$sale_id, $invoice_number]);

$_SESSION['success_message'] = 'Factura generada correctamente.';

header('Location: ../sales/');
exit;
