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
$products = getProducts($conn);

// Inicializar productos en la venta
$sale_products = isset($_SESSION['sale_products']) ? $_SESSION['sale_products'] : [];
$totals = calculateSaleTotals($sale_products);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tus estilos personalizados -->
    <link rel="stylesheet" href="/swift_invoice/assets/css/sales.css">
    <!---->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Nueva Venta</h2>
    </div>
    
    <div class="card-body">
        <form id="sale-form" method="POST" action="process.php">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="client_id">Cliente:</label>
                        <select id="client_id" name="client_id" class="form-control" required>
                            <option value="">Seleccionar cliente</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id']; ?>">
                                    <?php echo htmlspecialchars($client['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sale_date">Fecha:</label>
                        <input type="date" id="sale_date" name="sale_date" class="form-control" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="summary-card">
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
            
            <div class="product-selection">
                <h3>Productos</h3>
                
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="product_id">Producto:</label>
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
                        <div class="form-group">
                            <label for="quantity">Cantidad:</label>
                            <input type="number" id="quantity" class="form-control" min="1" value="1">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" id="add-product" class="btn btn-primary">Agregar</button>
                        </div>
                    </div>
                </div>
                
                <div class="product-list">
                    <table class="table" id="product-table">
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
                                      <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-warning btn-sm edit-product me-1">Editar</button>
                                        <button type="button" class="btn btn-danger btn-sm remove-product">Eliminar</button>
                                     </div>
                                        <input type="hidden" name="products[<?php echo $index; ?>][id]" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="products[<?php echo $index; ?>][name]" value="<?php echo htmlspecialchars($product['name']); ?>">
                                        <input type="hidden" name="products[<?php echo $index; ?>][price]" value="<?php echo $product['price']; ?>">
                                        <input type="hidden" name="products[<?php echo $index; ?>][quantity]" value="<?php echo $product['quantity']; ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <input type="hidden" name="subtotal" value="<?php echo $totals['subtotal']; ?>">
            <input type="hidden" name="tax_percentage" value="<?php echo $totals['tax_percentage']; ?>">
            <input type="hidden" name="tax_amount" value="<?php echo $totals['tax_amount']; ?>">
            <input type="hidden" name="total" value="<?php echo $totals['total']; ?>">
            
            <div class="form-group">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success" <?php echo empty($sale_products) ? 'disabled' : ''; ?>>Guardar Venta</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>

