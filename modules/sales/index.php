<?php
require_once '../../config/setup.php';
requireAuth();

require_once 'functions.php';

$page_title = "Ventas - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener lista de ventas con info de clientes y empresas
$stmt = $conn->query("
    SELECT 
        sales.*, 
        clients.first_name, clients.last_name, 
        companies.business_name 
    FROM sales 
    LEFT JOIN clients ON (sales.client_type = 'person' AND sales.client_id = clients.id)
    LEFT JOIN companies ON (sales.client_type = 'company' AND sales.client_id = companies.id)
    ORDER BY sale_date DESC
");

$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Swift Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/swift_invoice/assets/css/tableClients.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body>
<nav class="navbar navbar-expand navbar-custom py-2 sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand navbar-brand-custom ms-3" href="/swift_invoice">SWIFT INVOICE</a>
    </div>
</nav>

<div class="card-header d-flex justify-content-between align-items-center">
    <h2 class="card-title">Ventas</h2>
    <a href="create.php" class="btnAgregar">Agregar Venta</a>
</div>

<main class="container mt-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($sales)): ?>
        <p>No hay ventas registradas.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table id="ventasTable" class="styled-table display nowrap">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Subtotal</th>
                        <th>% IVA</th>
                        <th>IVA</th>
                        <th>Total</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td>
                                <?php
                                    if ($sale['client_type'] === 'person') {
                                        echo htmlspecialchars($sale['last_name'] . ' ' . $sale['first_name']);
                                    } elseif ($sale['client_type'] === 'company') {
                                        echo htmlspecialchars($sale['business_name']);
                                    } else {
                                        echo 'Cliente desconocido';
                                    }
                                ?>
                            </td>
                            <td><?php echo ucfirst($sale['client_type']); ?></td>
                            <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                            <td>$<?php echo number_format($sale['subtotal'], 2); ?></td>
                            <td><?php echo $sale['tax_percentage']; ?>%</td>
                            <td>$<?php echo number_format($sale['tax_amount'], 2); ?></td>
                            <td>$<?php echo number_format($sale['total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($sale['created_at']); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="view.php?id=<?php echo $sale['id']; ?>" class="btnDetails">Detalles</a>
                                    <a href="edit.php?id=<?php echo $sale['id']; ?>" class="btnEdit">Editar</a>
                                    <button class="btnDelete" onclick="confirmDelete(<?php echo $sale['id']; ?>)">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#ventasTable').DataTable({
            language: {
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            }
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar Venta?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<?php
require_once '../../includes/footer.php';
?>
