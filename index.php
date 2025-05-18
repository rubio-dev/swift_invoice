<?php
require_once 'config/setup.php';
requireAuth();

$page_title = "Inicio - Swift Invoice";
require_once 'includes/header.php';
?>

<div class="dashboard-container">
    <!-- Botón flotante de cierre -->
    <div class="logout-floating">
        <a href="/swift_invoice/auth/logout.php" class="logout-btn" title="Cerrar sesión">
            <span class="logout-icon">🚪</span>
            <span class="logout-text">Salir</span>
        </a>
    </div>

    <div class="welcome-banner">
        <h1 class="welcome-heading">Bienvenido, <span class="username"><?php echo htmlspecialchars($_SESSION['first_name']); ?></span></h1>
        <p class="welcome-message">¿En qué podemos ayudarte hoy?</p>
    </div>

    <!-- Carrusel de módulos estático -->
    <div class="carousel">
        <button class="carousel-btn prev">&#10094;</button>
        <div class="carousel-track">
            <div class="action-card prev"></div>
            <div class="action-card active"></div>
            <div class="action-card next"></div>
        </div>
        <button class="carousel-btn next">&#10095;</button>
    </div>

    <div class="dashboard-footer">
        <div class="footer-content">
            <!-- Aquí podrías agregar un copyright -->
        </div>
    </div>
</div>

<!-- Vincula el JS del carrusel -->
<script src="/swift_invoice/assets/js/dashboard.js"></script>

<?php
require_once 'includes/footer.php';
?>
