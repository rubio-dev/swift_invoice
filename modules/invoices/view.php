<?php
require_once '../../config/setup.php';
requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/swift_invoice/modules/invoices/');
}

$page_title = "Detalles de Factura - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener información de la factura
$stmt = $conn->prepare("
    SELECT i.*, s.sale_date, s.subtotal, s.tax_percentage, s.tax_amount, s.total,
           CONCAT(c.last_name, ' ', c.first_name) AS client_name,
           c.rfc AS client_rfc, c.address AS client_address,
           co.business_name AS company_name, co.rfc AS company_rfc,
           co.fiscal_address AS company_address, co.logo_path
    FROM invoices i
    JOIN sales s ON i.sale_id = s.id
    JOIN clients c ON s.client_id = c.id
    CROSS JOIN companies co
    WHERE i.id = :id
    LIMIT 1
");
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    redirect('/swift_invoice/modules/invoices/');
}

$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener detalles de la venta
$stmt = $conn->prepare("
    SELECT sd.*, p.name AS product_name
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    WHERE sd.sale_id = :sale_id
");
$stmt->bindParam(':sale_id', $invoice['sale_id']);
$stmt->execute();
$sale_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Factura #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Volver</a>
            <a href="generate_pdf.php?id=<?php echo $invoice['id']; ?>" 
               class="btn btn-primary" target="_blank">Generar PDF</a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="invoice-container">
            <!-- Encabezado de la factura -->
            <div class="invoice-header">
                <div class="invoice-logo">
                    <?php if (!empty($invoice['logo_path'])): ?>
                        <img src="/swift_invoice/<?php echo htmlspecialchars($invoice['logo_path']); ?>" 
                             alt="Logo de la empresa" class="logo-img">
                    <?php endif; ?>
                </div>
                <div class="invoice-info">
                    <h2>Factura</h2>
                    <p><strong>Número:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($invoice['invoice_date']); ?></p>
                </div>
            </div>
            
            <!-- Información del emisor y receptor -->
            <div class="invoice-parties">
                <div class="invoice-company">
                    <h3>Emisor</h3>
                    <p><strong><?php echo htmlspecialchars($invoice['company_name']); ?></strong></p>
                    <p><strong>RFC:</strong> <?php echo htmlspecialchars($invoice['company_rfc']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($invoice['company_address'])); ?></p>
                </div>
                
                <div class="invoice-client">
                    <h3>Receptor</h3>
                    <p><strong><?php echo htmlspecialchars($invoice['client_name']); ?></strong></p>
                    <p><strong>RFC:</strong> <?php echo htmlspecialchars($invoice['client_rfc'] ?? 'N/A'); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($invoice['client_address'] ?? 'N/A')); ?></p>
                </div>
            </div>
            
            <!-- Detalles de la factura -->
            <div class="invoice-details">
                <h3>Detalles de la Factura</h3>
                
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Descripción</th>
                            <th>Precio Unitario</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sale_details as $detail): ?>
                            <tr>
                                <td><?php echo $detail['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                                <td>$<?php echo number_format($detail['unit_price'], 2); ?></td>
                                <td>$<?php echo number_format($detail['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                            <td>$<?php echo number_format($invoice['subtotal'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">
                                <strong>IVA (<?php echo $invoice['tax_percentage']; ?>%):</strong>
                            </td>
                            <td>$<?php echo number_format($invoice['tax_amount'], 2); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td>$<?php echo number_format($invoice['total'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Información adicional -->
            <div class="invoice-footer">
                <div class="invoice-notes">
                    <h3>Notas</h3>
                    <p>Esta factura es válida como comprobante fiscal.</p>
                    <p>Forma de pago: Pago en una sola exhibición</p>
                </div>
                
                <div class="invoice-stamp">
                    <div class="stamp-placeholder">
                        <p>Sello digital</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>