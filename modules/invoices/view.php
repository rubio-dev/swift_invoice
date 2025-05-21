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

// 1. Obtener la factura con su venta asociada
$stmt = $conn->prepare("
    SELECT i.*, s.sale_date, s.subtotal, s.tax_percentage, s.tax_amount, s.total,
           s.client_type, s.client_id,
           co.business_name AS company_name, co.rfc AS company_rfc, co.fiscal_address AS company_address, co.logo_path
    FROM invoices i
    JOIN sales s ON i.sale_id = s.id
    LEFT JOIN companies co ON co.id = 1 -- Ajusta si tienes más de una empresa emisora
    WHERE i.id = :id
    LIMIT 1
");
$stmt->bindParam(':id', $_GET['id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    redirect('/swift_invoice/modules/invoices/');
}
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Obtener el cliente o empresa receptor
$receptor = [];
if ($invoice['client_type'] === 'person') {
    $stmt = $conn->prepare("SELECT CONCAT(last_name, ' ', first_name) AS name, rfc, address FROM clients WHERE id = :id");
    $stmt->bindParam(':id', $invoice['client_id']);
    $stmt->execute();
    $receptor = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($invoice['client_type'] === 'company') {
    $stmt = $conn->prepare("SELECT business_name AS name, rfc, fiscal_address AS address FROM companies WHERE id = :id");
    $stmt->bindParam(':id', $invoice['client_id']);
    $stmt->execute();
    $receptor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 3. Detalles de la venta
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title">Factura #<?= htmlspecialchars($invoice['invoice_number']); ?></h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Volver</a>
            <a href="generate.php?id=<?= $invoice['id']; ?>" class="btn btn-danger" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <a href="generate.php?id=<?= $invoice['id']; ?>" class="btn btn-success" target="_blank">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
            <a href="generate.php?id=<?= $invoice['id']; ?>" class="btn btn-primary" target="_blank">
                <i class="fa-solid fa-file-code"></i> XML
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="invoice-container">
            <!-- Encabezado -->
            <div class="invoice-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <?php if (!empty($invoice['logo_path'])): ?>
                        <img src="/swift_invoice/<?= htmlspecialchars($invoice['logo_path']); ?>" alt="Logo" class="logo-img" style="max-height: 70px;">
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="mb-1">Factura</h3>
                    <p><strong>Número:</strong> <?= htmlspecialchars($invoice['invoice_number']); ?></p>
                    <p><strong>Fecha:</strong> <?= htmlspecialchars($invoice['invoice_date']); ?></p>
                </div>
            </div>
            <!-- Emisor y Receptor -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Emisor</h4>
                    <p><strong><?= htmlspecialchars($invoice['company_name']); ?></strong></p>
                    <p><strong>RFC:</strong> <?= htmlspecialchars($invoice['company_rfc']); ?></p>
                    <p><?= nl2br(htmlspecialchars($invoice['company_address'])); ?></p>
                </div>
                <div class="col-md-6">
                    <h4>Receptor</h4>
                    <p><strong><?= htmlspecialchars($receptor['name'] ?? ''); ?></strong></p>
                    <p><strong>RFC:</strong> <?= htmlspecialchars($receptor['rfc'] ?? 'N/A'); ?></p>
                    <p><?= nl2br(htmlspecialchars($receptor['address'] ?? 'N/A')); ?></p>
                </div>
            </div>
            <!-- Detalles -->
            <div>
                <h4>Conceptos</h4>
                <table class="table table-bordered">
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
                                <td><?= $detail['quantity']; ?></td>
                                <td><?= htmlspecialchars($detail['product_name']); ?></td>
                                <td>$<?= number_format($detail['unit_price'], 2); ?></td>
                                <td>$<?= number_format($detail['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                            <td>$<?= number_format($invoice['subtotal'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">
                                <strong>IVA (<?= $invoice['tax_percentage']; ?>%):</strong>
                            </td>
                            <td>$<?= number_format($invoice['tax_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td>$<?= number_format($invoice['total'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Notas y sello -->
            <div class="mt-4 row">
                <div class="col-md-8">
                    <h5>Notas</h5>
                    <p>Esta factura es una simulación. No válida para efectos fiscales.</p>
                    <p>Forma de pago: Pago en una sola exhibición</p>
                </div>
                <div class="col-md-4">
                    <div class="border rounded text-center p-3">
                        <p>Sello digital simulado</p>
                        <small>(Aquí podría ir un QR o sello real en implementación)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
