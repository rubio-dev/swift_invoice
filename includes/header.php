<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Título dinámico: se usa $page_title si está definido -->
    <title><?php echo isset($page_title) ? $page_title : 'Swift Invoice'; ?></title>
    
    <!-- CSS principal del sistema, con control de caché (cache busting) -->
    <link rel="stylesheet" href="assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
    
    <!-- CSS adicional solo para el dashboard (index.php) -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?php echo filemtime('assets/css/dashboard.css'); ?>">
    <?php endif; ?>

    <!-- Bootstrap CSS desde CDN (útil para componentes y grid responsivo) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
