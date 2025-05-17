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

    <!-- Carrusel de módulos -->
    <div class="carousel">
        <button class="carousel-btn prev">&#10094;</button>
        <div class="carousel-track">
            <a href="/swift_invoice/modules/sales/" class="action-card" style="--order: 1;">
                <div class="card-icon-container"><span class="card-icon">💼</span></div>
                <div class="card-content">
                    <h3 class="card-title">Ventas</h3>
                    <p class="card-description">Crear factura de venta</p>
                    <span class="card-link">Acceder →</span>
                </div>
            </a>
            <a href="/swift_invoice/modules/clients/" class="action-card" style="--order: 2;">
                <div class="card-icon-container"><span class="card-icon">👥</span></div>
                <div class="card-content">
                    <h3 class="card-title">Clientes</h3>
                    <p class="card-description">Administrar lista de clientes</p>
                    <span class="card-link">Ver todos →</span>
                </div>
            </a>
            <a href="/swift_invoice/modules/invoices/" class="action-card" style="--order: 3;">
                <div class="card-icon-container"><span class="card-icon">📋</span></div>
                <div class="card-content">
                    <h3 class="card-title">Facturas</h3>
                    <p class="card-description">Historial de facturación</p>
                    <span class="card-link">Consultar →</span>
                </div>
            </a>
            <a href="/swift_invoice/modules/company/" class="action-card" style="--order: 4;">
                <div class="card-icon-container"><span class="card-icon">🏛️</span></div>
                <div class="card-content">
                    <h3 class="card-title">Compañías</h3>
                    <p class="card-description">Gestionar empresas</p>
                    <span class="card-link">Administrar →</span>
                </div>
            </a>
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
