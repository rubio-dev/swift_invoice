<?php
// Incluye la configuración y la función de autenticación de usuario
require_once 'config/setup.php';
requireAuth(); // Evita el acceso de usuarios no autenticados

// Define el título de la página y agrega la cabecera HTML
$page_title = "Inicio - Swift Invoice";
require_once 'includes/header.php';
?>

<div class="dashboard-container">
    <!-- Botón flotante para cerrar sesión -->
    <div class="logout-floating">
        <a href="/swift_invoice/auth/logout.php" class="logout-btn" title="Cerrar sesión">
            <span class="logout-icon">🚪 </span>
            <span class="logout-text">Cerrar Sesión</span>
        </a>
    </div>

    <!-- Banner de bienvenida con el nombre del usuario -->
    <div class="welcome-banner">
        <h1 class="welcome-heading">
            Bienvenido, 
            <span class="username">
                <?php echo htmlspecialchars($_SESSION['first_name']); ?>
            </span>
        </h1>
        <p class="welcome-message">¿En qué podemos ayudarte hoy?</p>
    </div>

    <!-- Carrusel estático de módulos (puede enlazarse a funcionalidades del sistema) -->
    <div class="carousel">
        <button class="carousel-btn prev">&#10094;</button>
        <div class="carousel-track">
            <div class="action-card prev"></div>
            <div class="action-card active"></div>
            <div class="action-card next"></div>
        </div>
        <button class="carousel-btn next">&#10095;</button>
    </div>

    <!-- Pie de página del dashboard -->
    <div class="dashboard-footer">
        <div class="footer-content">
            <!-- Aquí podrías agregar copyright o información adicional -->
        </div>
    </div>
</div>

<!-- Vinculación del JS para la funcionalidad del carrusel -->
<script src="/swift_invoice/assets/js/dashboard.js"></script>

<?php
// Agrega el pie de página común al sistema
require_once 'includes/footer.php';
?>
