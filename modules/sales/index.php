<?php
require_once '../../config/setup.php';
requireAuth();

require_once 'functions.php';

$page_title = "Ventas - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener lista de ventas
$stmt = $conn->query("
    SELECT s.id, s.sale_date, s.total, 
           CONCAT(c.last_name, ' ', c.first_name) AS client_name
    FROM sales s
    JOIN clients c ON s.client_id = c.id
    ORDER BY s.sale_date DESC
");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Ventas</h2>
        <a href="create.php" class="btn btn-success">Nueva Venta</a>
    </div>
    
    <div class="card-body">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (empty($sales)): ?>
            <p>No hay ventas registradas.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                                <td><?php echo htmlspecialchars($sale['client_name']); ?></td>
                                <td>$<?php echo number_format($sale['total'], 2); ?></td>
                                <td>
                                    <a href="view.php?id=<?php echo $sale['id']; ?>" class="btn btn-secondary">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>