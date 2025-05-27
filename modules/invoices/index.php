<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Facturas - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

$stmt = $conn->query("
    SELECT 
        i.id, 
        i.invoice_number, 
        i.invoice_date, 
        i.created_at, 
        s.total, 
        s.client_type,
        CASE 
            WHEN s.client_type = 'person' THEN CONCAT(c.last_name, ' ', c.first_name)
            WHEN s.client_type = 'company' THEN co.business_name
            ELSE 'Desconocido'
        END AS client_name
    FROM invoices i
    JOIN sales s ON i.sale_id = s.id
    LEFT JOIN clients c ON s.client_type = 'person' AND s.client_id = c.id
    LEFT JOIN companies co ON s.client_type = 'company' AND s.client_id = co.id
    ORDER BY i.invoice_date DESC
");
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas - Swift Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/swift_invoice/assets/css/tableInvoices.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

    <!-- SweetAlert2 para mensajes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<script>
          Swal.fire({
            icon: "success",
            title: "' . addslashes($_SESSION['success_message']) . '",
            timer: 1800,
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
        <h2 class="card-title">Facturas</h2>
    </div>

    <div class="container mt-5">
        <div class="table-responsive">
            <table id="facturasTable" class="styled-table display nowrap">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= htmlspecialchars($invoice['id']) ?></td>
                            <td><?= htmlspecialchars($invoice['client_name']) ?></td>
                            <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                            <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                            <td>$<?= number_format($invoice['total'], 2) ?></td>
                            <td>
                                <a href="generate.php?id=<?= $invoice['id'] ?>&format=pdf&download=1" class="btn btn-sm btn-danger" title="Descargar PDF">
                                    <i class="fa-solid fa-file-pdf"></i> PDF
                                </a>
                                <a href="generate.php?id=<?= $invoice['id'] ?>&format=xlsx&download=1" class="btn btn-sm btn-success" title="Descargar Excel">
                                    <i class="fa-solid fa-file-excel"></i> Excel
                                </a>
                                <a href="generate.php?id=<?= $invoice['id'] ?>&format=xml&download=1" class="btn btn-sm btn-primary" title="Descargar XML">
                                    <i class="fa-solid fa-file-code"></i> XML
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-start mt-4">
            <a href="/swift_invoice/" class="btn btn-secondary">← Volver al inicio</a>
        </div>
    </div>

    <!-- Scripts para DataTables y exportación -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#facturasTable')) {
                $('#facturasTable').DataTable({
                    dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6 text-end"B>>rt<"row mt-3"<"col-sm-6"i><"col-sm-6"p>>',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fa-solid fa-file-excel me-1"></i> Exportar a Excel',
                            className: 'btn btn-success'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fa-solid fa-file-pdf me-1"></i> Exportar a PDF',
                            className: 'btn btn-danger'
                        }
                    ],
                    pageLength: 10,
                    lengthMenu: [5, 10, 20, 50, 100],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });
            }
        });
    </script>
    <?php require_once '../../includes/footer.php'; ?>
</body>
</html>
