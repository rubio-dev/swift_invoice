<?php
require_once '../../config/setup.php';
requireAuth();

require_once 'functions.php';

$page_title = "Nueva Venta - Swift Invoice";
$custom_js = '/swift_invoice/assets/js/sales.js';
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

$clients = getClients($conn);
$companies = getCompanies($conn);
$products = getProducts($conn);

// Productos en la sesión
$sale_products = $_SESSION['sale_products'] ?? [];
$totals = calculateSaleTotals($sale_products);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nueva Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/swift_invoice/assets/css/sales.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
</head>

<body>

    <div class="sales-container" style="max-width: 820px;">
        <div class="card-header rounded-top-4">
            <h2 class="card-title mb-0 fw-bold text-center">Agregar Nueva Venta</h2>
        </div>

        <div class="card-body">
            <form id="sale-form" method="POST" action="process.php">
                <div class="row align-items-center">
                    <div class="col-md-6">

                        <!-- Tipo de cliente -->
                        <div class="form-group">
                            <label for="client_type" class="input-title">Tipo de Cliente:</label>
                            <select id="client_type" name="client_type" class="form-control" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="person">Persona</option>
                                <option value="company">Empresa</option>
                            </select>
                        </div>

                        <!-- Cliente o empresa -->
                        <div class="form-group">
                            <label for="client_id" class="input-title">Cliente o Empresa:</label>
                            <select id="client_id" name="client_id" class="form-control" required>
                                <option value="">Seleccionar cliente</option>

                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['id']; ?>" data-type="person" style="display: none;">
                                        <?php echo htmlspecialchars($client['name']); ?>
                                    </option>
                                <?php endforeach; ?>

                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo $company['id']; ?>" data-type="company"
                                        style="display: none;">
                                        <?php echo htmlspecialchars($company['name']); ?> (Empresa)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sale_date" class="input-title">Fecha:</label>
                            <input type="date" id="sale_date" name="sale_date" class="form-control"
                                value="<?php echo date('Y-m-d'); ?>" required />
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="summary-card p-3 border rounded">
                            <h4>Resumen de Venta</h4>
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">$<?php echo number_format($totals['subtotal'], 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Impuestos (<?php echo $totals['tax_percentage']; ?>%):</span>
                                <span id="tax-amount">$<?php echo number_format($totals['tax_amount'], 2); ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="total">$<?php echo number_format($totals['total'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selección de productos -->
                <h3 class="text-start pt-3">Productos</h3>

                <div class="row align-items-end mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="product_id" class="input-title">Producto:</label>
                            <select id="product_id" class="form-control">
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>"
                                        data-price="<?php echo $product['price']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                        ($<?php echo number_format($product['price'], 2); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="quantity" class="input-title">Cantidad:</label>
                            <input type="number" id="quantity" class="form-control" min="1" value="1" />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0"></div>
                        <button type="button" id="add-product" class="btncss">Agregar</button>
                    </div>
                </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="styled-table" id="product-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sale_products as $index => $product): ?>
                        <tr data-index="<?php echo $index; ?>">
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
                            <td>
                                 <button type="button" class="DeleteBtn remove-product">Eliminar</button>
                                <input type="hidden" name="products[<?php echo $index; ?>][id]"
                                    value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="products[<?php echo $index; ?>][name]"
                                    value="<?php echo htmlspecialchars($product['name']); ?>">
                                <input type="hidden" name="products[<?php echo $index; ?>][price]"
                                    value="<?php echo $product['price']; ?>">
                                <input type="hidden" name="products[<?php echo $index; ?>][quantity]"
                                    value="<?php echo $product['quantity']; ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center gap-5">

            <a href="index.php" class="btnback">Cancelar</a>
            <button type="submit" class="btncss" <?php echo empty($sale_products) ? 'disabled' : ''; ?>>Guardar
                Venta</button>
        </div>
    </div>

    <!-- Totales -->
    <input type="hidden" name="subtotal" value="<?php echo $totals['subtotal']; ?>">
    <input type="hidden" name="tax_percentage" value="<?php echo $totals['tax_percentage']; ?>">
    <input type="hidden" name="tax_amount" value="<?php echo $totals['tax_amount']; ?>">
    <input type="hidden" name="total" value="<?php echo $totals['total']; ?>">

    </form>
    </div>
    </div>

    <script>
        document.getElementById('client_type').addEventListener('change', function () {
            const selectedType = this.value;
            const clientSelect = document.getElementById('client_id');

            Array.from(clientSelect.options).forEach(option => {
                const type = option.getAttribute('data-type');
                if (!type) return;

                option.style.display = (type === selectedType) ? 'block' : 'none';
            });

            clientSelect.value = '';
        });
    </script>

</body>

</html>

<?php require_once '../../includes/footer.php'; ?>