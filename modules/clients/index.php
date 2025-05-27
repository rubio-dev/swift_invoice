<?php
// Incluye configuración general y protege la vista (solo usuarios autenticados)
require_once '../../config/setup.php';
requireAuth();

$page_title = "Clientes - Swift Invoice";
// Incluye el encabezado HTML estándar
require_once '../../includes/header.php';

// Conexión a la base de datos y obtención de la lista de clientes
$db = new Database();
$conn = $db->connect();

// Consulta todos los clientes ordenados por apellido y nombre
$stmt = $conn->query("SELECT * FROM clients ORDER BY last_name, first_name");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice - Clientes</title>
    <!-- Bootstrap CSS para estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS personalizado para tablas de clientes -->
    <link rel="stylesheet" href="/swift_invoice/assets/css/tableClients.css">
    <!-- DataTables CSS para tablas dinámicas y búsqueda -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Incluye esto antes del cierre de </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
   <!-- Navbar con dropdown de módulos y botón de cerrar sesión -->
<style>
  /* Dropdown oscuro con fondo visible */
  .dropdown-menu-dark-custom {
    background-color: rgba(33, 37, 41, 0.95); /* gris oscuro con opacidad */
    border: none;
  }

  .dropdown-menu-dark-custom .dropdown-item {
    color: white;
  }

  .dropdown-menu-dark-custom .dropdown-item:hover {
    background-color: #0d6efd; /* azul Bootstrap */
    color: white;
  }

  /* Estilo cerrar sesión */
  .logout-link {
    color: #dc3545;
    border: 1px solid #dc3545;
    border-radius: 5px;
    padding: 6px 12px;
    transition: all 0.2s ease-in-out;
  }

  .logout-link:hover {
    background-color: #dc3545;
    color: white;
    text-decoration: none;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-custom py-2 sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand navbar-brand-custom ms-3" href="/swift_invoice">SWIFT INVOICE</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
      <ul class="navbar-nav align-items-center me-3 gap-2">

        <!-- Módulos -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="javascript:void(0);" id="modulosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Módulos
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-custom" aria-labelledby="modulosDropdown">
            <li><a class="dropdown-item" href="/swift_invoice/modules/clients/">Clientes</a></li>
            <li><a class="dropdown-item" href="/swift_invoice/modules/company/">Empresas</a></li>
            <li><a class="dropdown-item" href="/swift_invoice/modules/sales/">Ventas</a></li>
            <li><a class="dropdown-item" href="/swift_invoice/modules/invoices/">Facturas</a></li>
          </ul>
        </li>

        <!-- Cerrar sesión -->
        <li class="nav-item">
          <a class="nav-link logout-link" href="/swift_invoice/auth/logout.php">Cerrar sesión</a>
        </li>

      </ul>
    </div>
  </div>
</nav>



    <!-- Encabezado del listado y botón para agregar -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title">Clientes</h2>
        <a href="create.php" class="btnAgregar">Agregar Cliente</a>
    </div>

    <main>
        <!-- Si no hay clientes, muestra mensaje -->
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
                                        <!-- Botón de detalles -->
                                        <a href="view.php?id=<?php echo $client['id']; ?>" class="btnDetails">Detalles</a>
                                        <!-- Botón de edición -->
                                        <a href="edit.php?id=<?php echo $client['id']; ?>" class="btnEdit">Editar</a>
                                        <!-- Botón de eliminar: llama confirmación JS -->
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
        
        <!-- Botón para volver al inicio -->
        <div class="d-flex justify-content-start mt-4">
            <a href="/swift_invoice/" class="btn btn-secondary">← Volver al inicio</a>
        </div>
    </main>
</body>

<!-- Librerías JS necesarias para DataTables y Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 para confirmaciones bonitas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Inicializa DataTables en la tabla de clientes
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

    // Confirmación de borrado de cliente usando SweetAlert2
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
                // Si confirma, crea y envía un formulario oculto por POST para borrar seguro
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


<?php if (isset($_SESSION['success_message'])): ?>
<script>
    //Alerta de error si quieren eliminar cliente y tiene registro de venta
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: 'error',
            title: 'No se pudo eliminar el cliente',
            text: <?php echo json_encode($_SESSION['success_message']); ?>,
            confirmButtonText: 'Entendido'
        });
    });
</script>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>


<?php
// Incluye el pie de página y scripts generales
require_once '../../includes/footer.php';
?>
