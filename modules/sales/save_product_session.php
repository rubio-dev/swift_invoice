<?php
require_once '../../config/setup.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar campos esperados
    if (isset($data['id'], $data['name'], $data['price'], $data['quantity'], $data['tax_rate'])) {
        if (!isset($_SESSION['sale_products'])) {
            $_SESSION['sale_products'] = [];
        }

        // Solo guarda los campos necesarios
        $_SESSION['sale_products'][] = [
            'id'       => $data['id'],
            'name'     => $data['name'],
            'price'    => $data['price'],
            'quantity' => $data['quantity'],
            'tax_rate' => $data['tax_rate']
        ];

        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos necesarios']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>
