<?php
// Archivo: modules/sales/process.php
require_once '../../config/setup.php';
requireAuth();
require_once 'functions.php';

$db   = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Acceso no permitido';
    redirect('/swift_invoice/modules/sales/create.php');
}

// Validación básica
if (
    empty($_POST['client_id']) ||
    empty($_POST['client_type']) ||
    empty($_POST['sale_date']) ||
    empty($_POST['products'])
) {
    $_SESSION['error_message'] = 'Por favor completa todos los campos';
    // Si venía con id, mantenemos en edit
    if (isset($_GET['id'])) {
        redirect("/swift_invoice/modules/sales/edit.php?id=" . (int)$_GET['id']);
    }
    redirect('/swift_invoice/modules/sales/create.php');
}

// Recoger datos
$client_id      = $_POST['client_id'];
$client_type    = $_POST['client_type'];
$sale_date      = $_POST['sale_date'];
$subtotal       = $_POST['subtotal'];
$tax_percentage = $_POST['tax_percentage'];
$tax_amount     = $_POST['tax_amount'];
$total          = $_POST['total'];

try {
    $conn->beginTransaction();

    if (isset($_GET['id'])) {
        // ===== UPDATE EXISTENTE =====
        $sale_id = (int)$_GET['id'];

        // 1) Actualizar cabecera
        $stmt = $conn->prepare("
            UPDATE sales
               SET client_id      = :client_id,
                   client_type    = :client_type,
                   sale_date      = :sale_date,
                   subtotal       = :subtotal,
                   tax_percentage = :tax_percentage,
                   tax_amount     = :tax_amount,
                   total          = :total
             WHERE id = :sale_id
        ");
        $stmt->execute([
            ':client_id'      => $client_id,
            ':client_type'    => $client_type,
            ':sale_date'      => $sale_date,
            ':subtotal'       => $subtotal,
            ':tax_percentage' => $tax_percentage,
            ':tax_amount'     => $tax_amount,
            ':total'          => $total,
            ':sale_id'        => $sale_id
        ]);

        // 2) Borrar detalles anteriores
        $conn->prepare("DELETE FROM sale_details WHERE sale_id = :sale_id")
             ->execute([':sale_id' => $sale_id]);

    } else {
        // ===== NUEVA VENTA =====
        $insert = $conn->prepare("
            INSERT INTO sales
                (client_id, client_type, sale_date, subtotal, tax_percentage, tax_amount, total)
            VALUES
                (:client_id, :client_type, :sale_date, :subtotal, :tax_percentage, :tax_amount, :total)
        ");
        $insert->execute([
            ':client_id'      => $client_id,
            ':client_type'    => $client_type,
            ':sale_date'      => $sale_date,
            ':subtotal'       => $subtotal,
            ':tax_percentage' => $tax_percentage,
            ':tax_amount'     => $tax_amount,
            ':total'          => $total
        ]);
        $sale_id = $conn->lastInsertId();
    }

    // 3) Insertar detalles nuevos (incluyendo tax_rate)
    $detailStmt = $conn->prepare("
        INSERT INTO sale_details
            (sale_id, product_id, quantity, unit_price, subtotal, tax_rate)
        VALUES
            (:sale_id, :product_id, :quantity, :unit_price, :subtotal, :tax_rate)
    ");
    foreach ($_POST['products'] as $p) {
        $detailStmt->execute([
            ':sale_id'     => $sale_id,
            ':product_id'  => $p['id'],
            ':quantity'    => $p['quantity'],
            ':unit_price'  => $p['price'],
            ':subtotal'    => $p['price'] * $p['quantity'],
            ':tax_rate'    => isset($p['tax_rate']) ? $p['tax_rate'] : 0.00
        ]);
    }

    $conn->commit();
    unset($_SESSION['sale_products']);

    // Mensaje de éxito
    $_SESSION['success_message'] = isset($_GET['id'])
        ? 'Venta actualizada correctamente'
        : 'Venta registrada correctamente';

    // Redirigir según sea create o edit
    if (isset($_GET['id'])) {
        redirect("/swift_invoice/modules/sales/edit.php?id={$sale_id}");
    } else {
        redirect('/swift_invoice/modules/sales/create.php');
    }

} catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION['error_message'] = 'Error al procesar la venta: ' . $e->getMessage();
    if (isset($_GET['id'])) {
        redirect("/swift_invoice/modules/sales/edit.php?id=" . (int)$_GET['id']);
    }
    redirect('/swift_invoice/modules/sales/create.php');
}
?>
