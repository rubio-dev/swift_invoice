<?php
// Incluye configuración y protege la acción (solo usuarios autenticados)
require_once '../../config/setup.php';
requireAuth();

// Este archivo espera recibir la petición por POST con el ID del cliente a eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $clientId = $_POST['id'];

    // Conexión a la base de datos
    $db = new Database();
    $conn = $db->connect();

    // Prepara y ejecuta el borrado seguro usando prepared statements
    $stmt = $conn->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->bindParam(':id', $clientId, PDO::PARAM_INT);

    // Ejecuta el DELETE y guarda un mensaje en la sesión según el resultado
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cliente eliminado correctamente.";
    } else {
        // ¡Ojo! Aquí podrías diferenciar errores lógicos de base de datos (por ejemplo, claves foráneas)
        $_SESSION['success_message'] = "Error al eliminar el cliente.";
    }
}

// Después de la operación, redirige siempre al listado de clientes
header("Location: index.php");
exit;
