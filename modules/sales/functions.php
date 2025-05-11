<?php
require_once '../../config/setup.php';

function getClients($conn) {
    $stmt = $conn->query("SELECT id, CONCAT(last_name, ' ', first_name) AS name FROM clients ORDER BY last_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCompanies($conn) {
    $stmt = $conn->query("SELECT id, business_name AS name FROM companies ORDER BY business_name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProducts($conn) {
    $stmt = $conn->query("SELECT id, name, price FROM products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateSaleTotals($products) {
    $subtotal = 0;

    foreach ($products as $product) {
        $subtotal += $product['quantity'] * $product['price'];
    }

    $tax_percentage = 16; // IVA por defecto 16%
    $tax_amount = $subtotal * ($tax_percentage / 100);
    $total = $subtotal + $tax_amount;

    return [
        'subtotal' => $subtotal,
        'tax_percentage' => $tax_percentage,
        'tax_amount' => $tax_amount,
        'total' => $total
    ];
}
?>
