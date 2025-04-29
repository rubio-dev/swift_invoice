<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Swift Invoice'; ?></title>
    
    <!-- CSS Principal (usa ruta relativa) -->
    <link rel="stylesheet" href="assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
    
    <!-- CSS específico del dashboard -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?php echo filemtime('assets/css/dashboard.css'); ?>">
    <?php endif; ?>
</head>
<body>