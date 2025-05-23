<?php
require_once '../../config/setup.php';
requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/swift_invoice/modules/sales/');
}

$page_title = "Detalles de Venta - Swift Invoice";
require_once '../../includes/header.php';

$db   = new Database();
$conn = $db->connect();

// Obtener información de la venta y del cliente
$stmt = $conn->prepare("
    SELECT 
      s.*,
      CASE 
        WHEN s.client_type = 'person'  THEN CONCAT(c.first_name, ' ', c.last_name)
        WHEN s.client_type = 'company' THEN co.business_name
        ELSE ''
      END AS client_name,
      CASE 
        WHEN s.client_type = 'person'  THEN c.rfc 
        WHEN s.client_type = 'company' THEN co.rfc 
        ELSE ''
      END AS client_rfc,
      CASE 
        WHEN s.client_type = 'person'  THEN c.address 
        WHEN s.client_type = 'company' THEN co.fiscal_address 
        ELSE ''
      END AS client_address
    FROM sales AS s
    LEFT JOIN clients   AS c  ON (s.client_type = 'person'  AND s.client_id = c.id)
    LEFT JOIN companies AS co ON (s.client_type = 'company' AND s.client_id = co.id)
    WHERE s.id = :id
");
$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    redirect('/swift_invoice/modules/sales/');
}

$sale = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener detalles de la venta
$detailStmt = $conn->prepare("
    SELECT sd.*, p.name AS product_name
    FROM sale_details AS sd
    JOIN products AS p ON sd.product_id = p.id
    WHERE sd.sale_id = :sale_id
");
$detailStmt->bindParam(':sale_id', $_GET['id'], PDO::PARAM_INT);
$detailStmt->execute();
$sale_details = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles de Venta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/swift_invoice/assets/css/tableClients.css">
</head>

<body>
  <nav class="navbar navbar-expand navbar-custom py-2 sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand navbar-brand-custom ms-3" href="/swift_invoice">Inicio</a>
    </div>
  </nav>

  <div class="container my-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">Venta #<?php echo htmlspecialchars($sale['id']); ?></h2>
        <a href="index.php" class="btn btn-secondary">Volver</a>
      </div>

      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-6">
            <h5>Información del Cliente</h5>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($sale['client_name'] ?: 'N/A'); ?></p>
            <p><strong>RFC:</strong> <?php echo htmlspecialchars($sale['client_rfc'] ?: 'N/A'); ?></p>
            <p><strong>Dirección:</strong> <?php echo nl2br(htmlspecialchars($sale['client_address'] ?: 'N/A')); ?></p>
          </div>
          <div class="col-md-6">
            <h5>Información de la Venta</h5>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($sale['sale_date']); ?></p>
            <p><strong>Subtotal:</strong> $<?php echo number_format($sale['subtotal'], 2); ?></p>
            <p><strong>Impuestos:</strong> $<?php echo number_format($sale['tax_amount'], 2); ?></p>
            <p><strong>Total:</strong> $<?php echo number_format($sale['total'], 2); ?></p>
          </div>
        </div>

        <h5>Productos / Servicios</h5>
        <?php if (empty($sale_details)): ?>
          <p>No hay productos registrados para esta venta.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th>Producto / Servicio</th>
                  <th>Precio Unitario</th>
                  <th>Cantidad</th>
                  <th>Impuesto</th>
                  <th>Subtotal</th>
                  <th>Total con Impuesto</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sale_details as $detail): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                    <td>$<?php echo number_format($detail['unit_price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
                    <td><?php echo number_format($detail['tax_rate'] ?? 0, 2); ?>%</td>
                    <td>$<?php echo number_format($detail['subtotal'], 2); ?></td>
                    <td>
                      $<?php
                        $line_total = $detail['subtotal'] + ($detail['subtotal'] * (($detail['tax_rate'] ?? 0) / 100));
                        echo number_format($line_total, 2);
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php require_once '../../includes/footer.php'; ?>
</body>
</html>
