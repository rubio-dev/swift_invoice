<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Detalles Cliente - Swift Invoice";
require_once '../../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['client_fetch_error'] = 'ID de cliente inválido.';
    redirect('/swift_invoice/modules/clients/');
}

$db = new Database();
$conn = $db->connect();
$client_id = (int) $_GET['id'];

// Traer datos del cliente junto con su régimen fiscal
$stmt = $conn->prepare("
    SELECT
      c.*,
      t.id   AS tax_regime_id,
      t.name AS tax_regime_name
    FROM clients c
    LEFT JOIN tax_regimes t
      ON c.tax_regime_id = t.id
    WHERE c.id = :id
");
$stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
$stmt->execute();
$client = $stmt->fetch(PDO::FETCH_ASSOC);
if (! $client) {
    $_SESSION['client_fetch_error'] = 'Error al consultar los datos del cliente.';
    redirect('/swift_invoice/modules/clients/');
}

require_once '../../includes/footer.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/swift_invoice/assets/css/clients.css" />
</head>

<body>
    <main class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="clients-container" style="max-width: 820px;">

            <div class="card-header rounded-top-4 px-4 py-3">
                <h2 class="card-title mb-0 fw-bold text-center">Detalles del Cliente</h2>
            </div>

            <div class="card-body px-4 py-3">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Nombre(s):</label>
                            <label class="detailsData"><?php echo htmlspecialchars($client['first_name']); ?></label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Apellido Paterno:</label>
                            <label class="detailsData"><?php echo htmlspecialchars($client['last_name']); ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Apellido Materno:</label>
                            <label
                                class="detailsData"><?php echo htmlspecialchars($client['mother_last_name']); ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Teléfono:</label>
                            <label class="detailsData"><?php echo htmlspecialchars($client['phone'] ?? '-'); ?></label>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Email:</label>
                            <label class="detailsData"><?php echo htmlspecialchars($client['email'] ?? '-'); ?></label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Dirección:</label>
                            <label class="detailsData d-block"
                                style="white-space: pre-wrap;"><?php echo nl2br(htmlspecialchars(trim($client['address']))); ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">RFC:</label>
                            <label class="detailsData"><?php echo htmlspecialchars($client['rfc'] ?? '-'); ?></label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="input-title-Details">Régimen Fiscal:</label>
                            <label class="detailsData">
      <?php
        $code = $client['tax_regime_id'] ?? '-';
        $name = $client['tax_regime_name'] ?? '-';
        echo htmlspecialchars("$code – $name");
      ?>
    </label>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="index.php" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>