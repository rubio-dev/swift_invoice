<?php
require_once '../../config/setup.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $companyId = intval($_POST['id']);

    $db = new Database();
    $conn = $db->connect();

    try {
        $stmt = $conn->prepare("DELETE FROM companies WHERE id = :id");
        $stmt->bindParam(':id', $companyId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Empresa eliminada correctamente.";
        } else {
            $_SESSION['success_message'] = "Error al eliminar la empresa.";
        }
    } catch (PDOException $e) {
        $_SESSION['success_message'] = "Error en la base de datos: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;
