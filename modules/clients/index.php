<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Clientes - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener lista de clientes
$stmt = $conn->query("SELECT * FROM clients ORDER BY last_name, first_name");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tus estilos personalizados -->
    <link rel="stylesheet" href="/swift_invoice/assets/css/tableClients.css">
    <!---->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <nav class="navbar navbar-expand navbar-custom py-2 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand navbar-brand-custom ms-3" href="/swift_invoice">SWIFT INVOICE</a>
        </div>
    </nav>

    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title">Clientes</h2>
        <a href="create.php" class="btnAgregar">Agregar Cliente</a>
    </div>

    <main>
        <?php if (empty($clients)): ?>
            <p>No hay clientes registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table id="clientesTable" class="styled-table display nowrap">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>RFC</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['last_name'] . ' ' . $client['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($client['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($client['email'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($client['rfc'] ?? '-'); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="view.php?id=<?php echo $client['id']; ?>" class="btnDetails">Detalles</a>
                                        <a href="edit.php?id=<?php echo $client['id']; ?>" class="btnEdit">Editar</a>
                                        <button class="btnDelete"
                                            onclick="confirmDelete(<?php echo $client['id']; ?>)">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-start mt-4">
            <a href="/swift_invoice/" class="btn btn-secondary">← Volver al inicio</a>
        </div>
    </main>
</body>

<!-- jQuery + DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#clientesTable').DataTable({
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

    function confirmDelete(clientId) {
        Swal.fire({
            title: '¿Eliminar cliente?',
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
                input.value = clientId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

</html>

<?php
require_once '../../includes/footer.php';
?>