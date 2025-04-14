<?php
require_once '../../config/setup.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_SESSION['sale_products'])) {
        $_SESSION['sale_products'] = [];
    }
    
    $_SESSION['sale_products'][] = $data;
    
    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>