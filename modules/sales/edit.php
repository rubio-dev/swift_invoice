<?php
require_once '../../config/setup.php';
requireAuth();
require_once 'functions.php';

// 0) Validar ID de la venta a editar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('/swift_invoice/modules/sales/');
}
$sale_id = (int)$_GET['id'];

// 1) Conectar y cargar datos de la venta
$db   = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT client_id, client_type, sale_date, subtotal, tax_percentage, tax_amount, total
    FROM sales
    WHERE id = :id
");
$stmt->execute([':id' => $sale_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sale) {
    redirect('/swift_invoice/modules/sales/');
}

// 2) Cargar detalles y totales
$detailStmt = $conn->prepare("
    SELECT sd.product_id AS id, p.name, sd.unit_price AS price, sd.quantity
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    WHERE sd.sale_id = :sale_id
");
$detailStmt->execute([':sale_id' => $sale_id]);
$sale_products = $detailStmt->fetchAll(PDO::FETCH_ASSOC);

$totals = calculateSaleTotals($sale_products);

// 3) Listas maestras
$clients   = getClients($conn);
$companies = getCompanies($conn);
$products  = getProducts($conn);

// Serializar para JS
$jsClients   = json_encode($clients);
$jsCompanies = json_encode($companies);
$initialType = $sale['client_type'];
$initialId   = $sale['client_id'];

$page_title = "Editar Venta #{$sale_id}";
require_once '../../includes/header.php';
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
// Al volver de process.php, muestra alerta de éxito y redirige al listado de ventas
if (isset($_SESSION['success_message'])) {
    echo '<script>
      Swal.fire({
        icon: "success",
        title: "' . addslashes($_SESSION['success_message']) . '",
        text: "Redirigiendo al listado de ventas...",
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = "/swift_invoice/modules/sales/";
      });
    </script>';
    unset($_SESSION['success_message']);
}

// Si hubo error, muestra alerta y permanece en esta página
if (isset($_SESSION['error_message'])) {
    echo '<script>
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "' . addslashes($_SESSION['error_message']) . '",
        confirmButtonText: "OK",
        didOpen: () => { document.body.style.paddingRight = "0px"; }
      });
    </script>';
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $page_title; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="/swift_invoice/assets/css/sales.css"/>
</head>
<body>

<div class="sales-container" style="max-width:820px;">
  <div class="card-header rounded-top-4 text-center">
    <h2 class="card-title">Editar Venta</h2>
  </div>
  <div class="card-body">
      <form id="sale-form" method="POST" action="process.php?id=<?php echo $sale_id; ?>">
      <div class="row mb-4 justify-content-start">
        <div class="col-md-6">
          <!-- Tipo de cliente -->
          <div class="mb-3">
            <label for="client_type" class="form-label input-title">Tipo de Cliente:</label>
            <select id="client_type" name="client_type" class="form-control" required>
              <option value="">Seleccionar tipo</option>
              <option value="person"<?php echo $initialType==='person'?' selected':'';?>>Persona</option>
              <option value="company"<?php echo $initialType==='company'?' selected':'';?>>Empresa</option>
            </select>
          </div>
          <!-- Cliente o Empresa -->
          <div class="mb-3">
            <label for="client_id" class="form-label input-title">Cliente o Empresa:</label>
            <select id="client_id" name="client_id" class="form-control" required>
              <!-- Se puebla con JS -->
            </select>
          </div>
          <!-- Fecha -->
          <div class="mb-3">
            <label for="sale_date" class="form-label input-title">Fecha:</label>
            <input type="date" id="sale_date" name="sale_date" class="form-control"
                   value="<?php echo htmlspecialchars($sale['sale_date']); ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="summary-card p-3 border rounded">
            <h4>Resumen de Venta</h4>
            <div class="summary-row"><span>Subtotal:</span> <span id="subtotal">$<?php echo number_format($totals['subtotal'],2);?></span></div>
            <div class="summary-row"><span>IVA (<?php echo $totals['tax_percentage']; ?>%):</span> <span id="tax-amount">$<?php echo number_format($totals['tax_amount'],2);?></span></div>
            <div class="summary-row total"><span>Total:</span> <span id="total">$<?php echo number_format($totals['total'],2);?></span></div>
          </div>
        </div>
      </div>

      <!-- Productos -->
      <h3 class="pt-3 text-start">Productos</h3>
      <div class="row align-items-end mb-3">
        <div class="col-md-6">
          <label for="product_id" class="form- input-title">Producto:</label>
          <select id="product_id" class="form-control">
            <option value="">Seleccionar producto</option>
            <?php foreach ($products as $p): ?>
              <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>">
                <?php echo htmlspecialchars($p['name']); ?> ($<?php echo number_format($p['price'],2); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label for="quantity" class="form-label input-title">Cantidad:</label>
          <input type="number" id="quantity" class="form-control" min="1" value="1"/>
        </div>
        <div class="col-md-3">
          <button type="button" id="add-product" class="btncss">Agregar</button>
        </div>
      </div>

      <div class="table-responsive mb-4">
        <table id="product-table" class="styled-table">
          <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php foreach ($sale_products as $i => $prod): ?>
              <tr data-index="<?php echo $i; ?>">
                <td><?php echo htmlspecialchars($prod['name']); ?></td>
                <td>$<?php echo number_format($prod['price'],2); ?></td>
                <td><?php echo $prod['quantity']; ?></td>
                <td>$<?php echo number_format($prod['price']*$prod['quantity'],2); ?></td>
                <td>
                  <button type="button" class="remove-product DeleteBtn">Eliminar</button>
                  <input type="hidden" name="products[<?php echo $i;?>][id]" value="<?php echo $prod['id'];?>">
                  <input type="hidden" name="products[<?php echo $i;?>][name]" value="<?php echo htmlspecialchars($prod['name']);?>">
                  <input type="hidden" name="products[<?php echo $i;?>][price]" value="<?php echo $prod['price'];?>">
                  <input type="hidden" name="products[<?php echo $i;?>][quantity]" value="<?php echo $prod['quantity'];?>">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-center gap-5 mb-4">
        <a href="index.php" class="btnback">Cancelar</a>
        <button type="submit" class="btncss">Actualizar Venta</button>
      </div>

      <input type="hidden" name="subtotal"       value="<?php echo $totals['subtotal']; ?>">
      <input type="hidden" name="tax_percentage" value="<?php echo $totals['tax_percentage']; ?>">
      <input type="hidden" name="tax_amount"     value="<?php echo $totals['tax_amount']; ?>">
      <input type="hidden" name="total"          value="<?php echo $totals['total']; ?>">
    </form>
  </div>
</div>

<!-- Variables JS -->
<script>
  const clients     = <?php echo $jsClients; ?>;
  const companies   = <?php echo $jsCompanies; ?>;
  const initialType = "<?php echo $initialType; ?>";
  const initialId   = <?php echo $initialId; ?>;
</script>
<script src="/swift_invoice/assets/js/sales-edit.js"></script>
<?php require_once '../../includes/footer.php'; ?>
