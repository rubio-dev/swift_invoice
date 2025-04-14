<?php
require_once 'config/setup.php';
requireAuth();

$page_title = "Inicio - Swift Invoice";
require_once 'includes/header.php';
?>

<style>
    .dashboard-buttons {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 30px;
    }
    
    .dashboard-btn {
        padding: 30px 15px;
        border-radius: 12px;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 180px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        color: white;
    }
    
    .dashboard-btn i {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .dashboard-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-sales {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .btn-clients {
        background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
    }
    
    .btn-invoices {
        background: linear-gradient(135deg, #0f4c81 0%, #1a2980 100%);
    }
    
    .btn-company {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
</style>

<div class="container">
    <div class="text-center mb-5">
        <h1 class="display-4">Bienvenido</h1>
        <p class="lead">Selecciona una opción para comenzar</p>
    </div>
    
    <div class="dashboard-buttons">
        <a href="/swift_invoice/modules/sales/create.php" class="dashboard-btn btn-sales">
            <i class="fas fa-cash-register"></i>
            Nueva Venta
        </a>
        
        <a href="/swift_invoice/modules/clients/" class="dashboard-btn btn-clients">
            <i class="fas fa-users"></i>
            Gestión de Clientes
        </a>
        
        <a href="/swift_invoice/modules/invoices/" class="dashboard-btn btn-invoices">
            <i class="fas fa-file-invoice-dollar"></i>
            Ver Facturas
        </a>
        
        <a href="/swift_invoice/modules/company/" class="dashboard-btn btn-company">
            <i class="fas fa-chart-pie"></i>
            Compañías
        </a>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>