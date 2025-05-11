<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Empresas - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener lista de empresas con su razón social
$stmt = $conn->query("SELECT c.id, c.business_name, c.rfc, c.email, bt.name AS business_type, c.legal_representative FROM companies c JOIN business_types bt ON c.business_type_id = bt.id ORDER BY c.business_name");
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas - Swift Invoice</title>
    <!-- Bootstrap + DataTables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/swift_invoice/assets/css/tableCompanies.css">
</head>

<body>
    <nav class="navbar navbar-expand navbar-custom py-2 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand navbar-brand-custom ms-3" href="/swift_invoice">SWIFT INVOICE</a>
        </div>
    </nav>

    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title">Empresas</h2>
        <a href="create.php" class="btnAgregar">Agregar Empresa</a>
    </div>

    <main class="container mt-4">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($companies)): ?>
            <p>No hay empresas registradas.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table id="empresasTable" class="styled-table display nowrap">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Email</th>
                            <th>Razón Social</th>
                            <th>Representante Legal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($company['business_name']); ?></td>
                                <td><?php echo htmlspecialchars($company['rfc']); ?></td>
                                <td><?php echo htmlspecialchars($company['email']); ?></td>
                                <td><?php echo htmlspecialchars($company['business_type']); ?></td>
                                <td><?php echo htmlspecialchars($company['legal_representative']); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit.php?id=<?php echo $company['id']; ?>" class="btnEdit">Editar</a>
                                        <button class="btnDelete" onclick="confirmDelete(<?php echo $company['id']; ?>)">Eliminar</button>
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

    <!-- JS de jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#empresasTable').DataTable({
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

        function confirmDelete(companyId) {
            Swal.fire({
                title: '¿Eliminar empresa?',
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
                    input.value = companyId;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>

<?php
require_once '../../includes/footer.php';
?>
