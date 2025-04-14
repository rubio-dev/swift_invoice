<?php
require_once '../../config/setup.php';
requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/swift_invoice/modules/sales/');
}

$page_title = "Detalles de Venta - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener información de la venta
$stmt = $conn->prepare("
    SELECT s.*, 
           CONCAT(c.last_name, ' ', c.first_name) AS client_name,
           c.rfc AS client_rfc,
           c.address AS client_address
    FROM sales s
    JOIN clients c ON s.client_id = c.id
    WHERE s.id = :id
");
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    redirect('/swift_invoice/modules/sales/');
}

$sale = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener detalles de la venta
$stmt = $conn->prepare("
    SELECT sd.*, p.name AS product_name
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    WHERE sd.sale_id = :sale_id
");
$stmt->bindParam(':sale_id', $_GET['id']);
$stmt->execute();
$sale_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Detalles de Venta #<?php echo $sale['id']; ?></h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Volver</a>
            <a href="../invoices/generate_pdf.php?sale_id=<?php echo $sale['id']; ?>" 
               class="btn btn-primary" target="_blank">Generar PDF</a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <h4>Información del Cliente</h4>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($sale['client_name']); ?></p>
                    <p><strong>RFC:</strong> <?php echo htmlspecialchars($sale['client_rfc'] ?? 'N/A'); ?></p>
                    <p><strong>Dirección:</strong> <?php echo nl2br(htmlspecialchars($sale['client_address'] ?? 'N/A')); ?></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-group">
                    <h4>Información de la Venta</h4>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($sale['sale_date']); ?></p>
                    <p><strong>Subtotal:</strong> $<?php echo number_format($sale['subtotal'], 2); ?></p>
                    <p><strong>Impuestos (<?php echo $sale['tax_percentage']; ?>%):</strong> $<?php echo number_format($sale['tax_amount'], 2); ?></p>
                    <p><strong>Total:</strong> $<?php echo number_format($sale['total'], 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="product-details mt-4">
            <h4>Productos</h4>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sale_details as $detail): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                            <td>$<?php echo number_format($detail['unit_price'], 2); ?></td>
                            <td><?php echo $detail['quantity']; ?></td>
                            <td>$<?php echo number_format($detail['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>