<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Detalles Compañía - Swift Invoice";
require_once '../../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['company_fetch_error'] = 'ID de compañía inválido.';
    redirect('/swift_invoice/modules/company/');
}

$db = new Database();
$conn = $db->connect();
$company_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT 
        c.*, 
        bt.name AS business_type_name, 
        tr.name AS tax_regime_name
    FROM companies c
    LEFT JOIN business_types bt ON c.business_type_id = bt.id
    LEFT JOIN tax_regimes tr ON c.tax_regime_id = tr.id
    WHERE c.id = :id
");
$stmt->bindParam(':id', $company_id, PDO::PARAM_INT);
$stmt->execute();
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    $_SESSION['company_fetch_error'] = 'Error al consultar los datos de la compañía.';
    redirect('/swift_invoice/modules/company/');
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
            <h2 class="card-title mb-0 fw-bold text-center">Detalles de la Compañía</h2>
        </div>

        <div class="card-body px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Nombre de la Empresa:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['business_name']); ?></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Representante Legal:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['legal_representative'] ?? '-'); ?></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">RFC:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['rfc'] ?? '-'); ?></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Dirección Fiscal:</label>
                        <label class="detailsData d-block" style="white-space: normal;">
                            <?php echo nl2br(htmlspecialchars(trim($company['fiscal_address']))); ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Teléfono:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['phone'] ?? '-'); ?></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Email:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['email'] ?? '-'); ?></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Razón Social:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['business_type_name'] ?? '-'); ?></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="input-title-Details">Régimen Fiscal:</label>
                        <label class="detailsData"><?php echo htmlspecialchars($company['tax_regime_name'] ?? '-'); ?></label>
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
