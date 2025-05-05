<?php
require_once '../../config/setup.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $saleId = $_POST['id'];

    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("DELETE FROM sales WHERE id = :id");
    $stmt->bindParam(':id', $saleId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Venta eliminada correctamente.";
    } else {
        $_SESSION['success_message'] = "Error al eliminar la venta.";
    }
}

header("Location: index.php");
exit;
