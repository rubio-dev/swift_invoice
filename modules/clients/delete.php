<?php
require_once '../../config/setup.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $clientId = $_POST['id'];

    $db = new Database();
    $conn = $db->connect();

    // Verifica si hay ventas relacionadas con este cliente
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM sales WHERE client_id = :id");
    $checkStmt->bindParam(':id', $clientId, PDO::PARAM_INT);
    $checkStmt->execute();
    $relatedSales = $checkStmt->fetchColumn();

    if ($relatedSales > 0) {
      $_SESSION['success_message'] = "No se puede eliminar este cliente porque tiene ventas registradas. Elimine o reasigne las ventas antes de continuar.";
    } else {
        // Si no hay ventas, ahora sí procede a eliminar
        $stmt = $conn->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->bindParam(':id', $clientId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Cliente eliminado correctamente.";
        } else {
            $_SESSION['success_message'] = "Error al eliminar el cliente.";
        }
    }
}

header("Location: index.php");
exit;
