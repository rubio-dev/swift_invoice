<?php
session_start();

// Carga el archivo que define la clase Database
// Donde esta dios
require_once __DIR__ . '/database.php';


// Función para redireccionar
function redirect($location) {
    header("Location: $location");
    exit();
}

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Proteger rutas que requieren autenticación
function requireAuth() {
    if (!isLoggedIn()) {
        redirect('/swift_invoice/auth/login.php');
    }
}
?>