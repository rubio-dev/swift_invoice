<?php
require_once '../../config/setup.php';
requireAuth();

require_once 'functions.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos básicos
    if (empty($_POST['client_id']) || empty($_POST['sale_date']) || empty($_POST['products'])) {
        $_SESSION['error_message'] = 'Datos incompletos para procesar la venta';
        redirect('/swift_invoice/modules/sales/create.php');
    }
    
    try {
        $conn->beginTransaction();
        
        // Insertar la venta
        $stmt = $conn->prepare("
            INSERT INTO sales 
            (client_id, sale_date, subtotal, tax_percentage, tax_amount, total)
            VALUES 
            (:client_id, :sale_date, :subtotal, :tax_percentage, :tax_amount, :total)
        ");
        
        $stmt->bindParam(':client_id', $_POST['client_id']);
        $stmt->bindParam(':sale_date', $_POST['sale_date']);
        $stmt->bindParam(':subtotal', $_POST['subtotal']);
        $stmt->bindParam(':tax_percentage', $_POST['tax_percentage']);
        $stmt->bindParam(':tax_amount', $_POST['tax_amount']);
        $stmt->bindParam(':total', $_POST['total']);
        
        $stmt->execute();
        $sale_id = $conn->lastInsertId();
        
        // Insertar los detalles de la venta
        foreach ($_POST['products'] as $product) {
            $stmt = $conn->prepare("
                INSERT INTO sale_details 
                (sale_id, product_id, quantity, unit_price, subtotal)
                VALUES 
                (:sale_id, :product_id, :quantity, :unit_price, :subtotal)
            ");
            
            $subtotal = $product['price'] * $product['quantity'];
            
            $stmt->bindParam(':sale_id', $sale_id);
            $stmt->bindParam(':product_id', $product['id']);
            $stmt->bindParam(':quantity', $product['quantity']);
            $stmt->bindParam(':unit_price', $product['price']);
            $stmt->bindParam(':subtotal', $subtotal);
            
            $stmt->execute();
        }
        
        $conn->commit();
        
        // Limpiar productos de la sesión
        unset($_SESSION['sale_products']);
        
        $_SESSION['success_message'] = 'Venta registrada correctamente';
        redirect('/swift_invoice/modules/sales/view.php?id=' . $sale_id);
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = 'Error al procesar la venta: ' . $e->getMessage();
        redirect('/swift_invoice/modules/sales/create.php');
    }
} else {
    redirect('/swift_invoice/modules/sales/create.php');
}
?>