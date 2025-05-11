<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Facturas - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener lista de facturas
$stmt = $conn->query("
    SELECT i.id, i.invoice_number, i.invoice_date, i.created_at,
           s.total, CONCAT(c.last_name, ' ', c.first_name) AS client_name
    FROM invoices i
    JOIN sales s ON i.sale_id = s.id
    JOIN clients c ON s.client_id = c.id
    ORDER BY i.invoice_date DESC
");
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Facturas</h2>
    </div>
    
    <div class="card-body">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (empty($invoices)): ?>
            <p>No hay facturas generadas.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['invoice_date']); ?></td>
                                <td><?php echo htmlspecialchars($invoice['client_name']); ?></td>
                                <td>$<?php echo number_format($invoice['total'], 2); ?></td>
                                <td>
                                    <a href="view.php?id=<?php echo $invoice['id']; ?>" class="btn btn-secondary btn-sm">Ver</a>
                                    <a href="generate_pdf.php?id=<?php echo $invoice['id']; ?>" class="btn btn-primary btn-sm" target="_blank">PDF</a>
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