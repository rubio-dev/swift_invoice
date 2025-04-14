<?php
require_once '../../config/setup.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $clientId = $_POST['id'];

    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->bindParam(':id', $clientId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cliente eliminado correctamente.";
    } else {
        $_SESSION['success_message'] = "Error al eliminar el cliente.";
    }
}

header("Location: index.php");
exit;
