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
    
    <div class="quick-actions">
        <!-- Ventas -->
        <a href="/swift_invoice/modules/sales/" class="action-card sales">
            <div class="card-icon-container">
                <span class="card-icon">💼</span>
            </div>
            <div class="card-content">
                <h3 class="card-title">Ventas</h3>
                <p class="card-description">Crear y consultar ventas</p>
                <span class="card-link">Ir al módulo →</span>
            </div>
        </a>

        <!-- Clientes -->
        <a href="/swift_invoice/modules/clients/" class="action-card clients">
            <div class="card-icon-container">
                <span class="card-icon">👥</span>
            </div>
            <div class="card-content">
                <h3 class="card-title">Clientes</h3>
                <p class="card-description">Administrar lista de clientes</p>
                <span class="card-link">Ver todos →</span>
            </div>
        </a>

        <!-- Empresas -->
        <a href="/swift_invoice/modules/company/" class="action-card company">
            <div class="card-icon-container">
                <span class="card-icon">🏛️</span>
            </div>
            <div class="card-content">
                <h3 class="card-title">Empresas</h3>
                <p class="card-description">Administrar lista de empresas</p>
                <span class="card-link">Administrar →</span>
            </div>
        </a>
    </div>

    <div class="dashboard-footer">
        <div class="footer-content">
            <!-- Puedes personalizar este espacio -->
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
