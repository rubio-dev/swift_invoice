<?php
require_once '../../config/setup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($_SESSION['sale_products']) && isset($data['index']) && isset($_SESSION['sale_products'][$data['index']])) {
        array_splice($_SESSION['sale_products'], $data['index'], 1);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>