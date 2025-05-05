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
        <a href="/swift_invoice/modules/sales" class="action-card sales">
            <div class="card-icon-container">
                <span class="card-icon">💼</span>
            </div>
            <div class="card-content">
                <h3 class="card-title">Ventas</h3>
                <p class="card-description">Crear factura de venta</p>
                <span class="card-link">Acceder →</span>
            </div>
        </a>
        
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
        
        <a href="/swift_invoice/modules/invoices/" class="action-card invoices">
            <div class="card-icon-container">
                <span class="card-icon">📋</span>
            </div>
            <div class="card-content">
                <h3 class="card-title">Facturas</h3>
                <p class="card-description">Historial de facturación</p>
                <span class="card-link">Consultar →</span>
            </div>
        </a>
        
        <a href="/swift_invoice/modules/company/" class="action-card company">
            <div class="card-icon-container">
                <span class="card-icon">🏛️</span>
            </div>
            <div class="card-content">
                <h3 class="card-title">Compañías</h3>
                <p class="card-description">Gestionar empresas</p>
                <span class="card-link">Administrar →</span>
            </div>
        </a>
    </div>
    
    <div class="dashboard-footer">
        <div class="footer-content">
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>