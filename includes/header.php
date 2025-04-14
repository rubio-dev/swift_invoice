<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Swift Invoice'; ?></title>
    <link rel="stylesheet" href="/swift_invoice/assets/css/style.css">
    <?php if (isset($custom_css)): ?>
        <link rel="stylesheet" href="<?php echo $custom_css; ?>">
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">Swift Invoice</div>
            <?php if (isLoggedIn()): ?>
                <div class="user-info">
                    <span>Hola, <?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                    <a href="/swift_invoice/auth/logout.php" class="btn btn-danger">Salir</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <?php if (isLoggedIn()): ?>
        <nav>
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="/swift_invoice/index.php">Inicio</a></li>
                    <li><a href="/swift_invoice/modules/sales/">Ventas</a></li>
                    <li><a href="/swift_invoice/modules/clients/">Clientes</a></li>
                    <li><a href="/swift_invoice/modules/company/">Empresa</a></li>
                    <li><a href="/swift_invoice/modules/invoices/">Facturas</a></li>
                </ul>
            </div>
        </nav>
    <?php endif; ?>

    <main class="main-content container"></main>