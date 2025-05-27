<?php
require_once '../../config/setup.php';
requireAuth();
require_once 'functions.php';

$page_title = "Ventas - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Consulta con join para saber si la venta está facturada
$stmt = $conn->query("
    SELECT 
      s.*,
      c.first_name, 
      c.last_name,
      co.business_name,
      i.id AS invoice_id,
      i.invoice_number
    FROM sales AS s
    LEFT JOIN clients   AS c  ON (s.client_type = 'person'  AND s.client_id = c.id)
    LEFT JOIN companies AS co ON (s.client_type = 'company' AND s.client_id = co.id)
    LEFT JOIN invoices  AS i  ON (i.sale_id = s.id)
    ORDER BY s.sale_date DESC
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>


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

    <!-- Mensajes de éxito o error (SweetAlert2) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<script>
          Swal.fire({
            icon: "success",
            title: "' . addslashes($_SESSION['success_message']) . '",
            text: "Redirigiendo al listado de ventas...",
            timer: 2000,
            showConfirmButton: false
          });
        </script>';
        unset($_SESSION['success_message']);
    }
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

    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title">Ventas</h2>
        <a href="create.php" class="btnAgregar">Agregar Venta</a>
    </div>

    <main class="">
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
                                <td>
  <?php
    echo $sale['client_type'] === 'person' ? 'Persona' :
         ($sale['client_type'] === 'company' ? 'Compañía' : 'Desconocido');
  ?>
</td>
                                <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                                <td>$<?php echo number_format($sale['total'], 2); ?></td>
                                <td><?php echo htmlspecialchars($sale['created_at']); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="view.php?id=<?php echo $sale['id']; ?>" class="btnDetails">Detalles</a>
                                        <?php if ($sale['invoice_id']): ?>
                                            <!-- SI ESTA FACTURADA SOLO DEJA DETALLES Y MUESTRA BADGE -->
                                            <span class="badge bg-success ms-1">Facturada</span>
                                        <?php else: ?>
                                            <!-- SOLO SI NO ESTA FACTURADA, DEJA EDITAR/ELIMINAR/GENERAR FACTURA -->
                                            <a href="edit.php?id=<?php echo $sale['id']; ?>" class="btnEdit">Editar</a>
                                            <button class="btnDelete" onclick="confirmDelete(<?php echo $sale['id']; ?>)">Eliminar</button>
                                            <a href="/swift_invoice/modules/invoices/invoice.php?sale_id=<?= $sale['id']; ?>" 
                                               class="btn btn-sm btn-primary ms-1">
                                               Generar factura
                                            </a>
                                        <?php endif; ?>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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

    <?php require_once '../../includes/footer.php'; ?>
</body>
</html>
