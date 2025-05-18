<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Editar Empresa - Swift Invoice";
require_once '../../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['company_fetch_error'] = 'ID de empresa inválido.';
    redirect('/swift_invoice/modules/company/');
}

$db = new Database();
$conn = $db->connect();

$company_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM companies WHERE id = :id");
$stmt->bindParam(':id', $company_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['company_fetch_error'] = 'No se encontró la empresa.';
    redirect('/swift_invoice/modules/company/');
}

$company = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener catálogos
$types_stmt = $conn->query("SELECT id, name FROM business_types ORDER BY name");
$business_types = $types_stmt->fetchAll(PDO::FETCH_ASSOC);

$regimes_stmt = $conn->query("SELECT id, name FROM tax_regimes ORDER BY name");
$tax_regimes = $regimes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Errores
$errors = [];
if (isset($_SESSION['company_form_errors'])) {
    $errors = $_SESSION['company_form_errors'];
    unset($_SESSION['company_form_errors']);

    if (isset($_SESSION['company_form_data'])) {
        $company = $_SESSION['company_form_data'];
        unset($_SESSION['company_form_data']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/swift_invoice/assets/css/companies.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <main class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="clients-container" style="max-width: 820px;">
            <div class="card-header rounded-top-4 px-4 py-3">
                <h2 class="card-title mb-0 fw-bold text-center">Agregar Compañia</h2>
            </div>

            <div class="card-body px-2 py-2">
                <form method="POST" action="save.php">
                    <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title"for="business_name">Nombre de la Empresa:</label>
                                <input type="text" id="business_name" name="business_name" class="form-control"
                                    value="<?php echo htmlspecialchars($company['business_name']); ?>" required>
                                <?php if (isset($errors['business_name'])): ?><span
                                        class="error-text"><?php echo $errors['business_name']; ?></span><?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="rfc">RFC:</label>
                                <input type="text" id="rfc" name="rfc" class="form-control"
                                    value="<?php echo htmlspecialchars($company['rfc']); ?>" required>
                                <?php if (isset($errors['rfc'])): ?><span
                                        class="error-text"><?php echo $errors['rfc']; ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="fiscal_address">Dirección Fiscal:</label>
                                <textarea id="fiscal_address" name="fiscal_address" class="form-control" rows="3"
                                    required><?php echo htmlspecialchars($company['fiscal_address']); ?></textarea>
                                <?php if (isset($errors['fiscal_address'])): ?><span
                                        class="error-text"><?php echo $errors['fiscal_address']; ?></span><?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="phone">Teléfono:</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="<?php echo htmlspecialchars($company['phone']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($company['email']); ?>">
                                <?php if (isset($errors['email'])): ?><span
                                        class="error-text"><?php echo $errors['email']; ?></span><?php endif; ?>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="legal_representative">Representante Legal:</label>
                                <input type="text" id="legal_representative" name="legal_representative"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($company['legal_representative']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="business_type_id">Razón Social:</label>
                                <select id="business_type_id" name="business_type_id" class="form-control" required>
                                    <option value="">Seleccione una opción</option>
                                    <?php foreach ($business_types as $type): ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo ($company['business_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="tax_regime_id">Régimen Fiscal:</label>
                                <select id="tax_regime_id" name="tax_regime_id" class="form-control" required>
                                    <option value="">Seleccione una opción</option>
                                    <?php foreach ($tax_regimes as $regime): ?>
                                        <option value="<?php echo $regime['id']; ?>" <?php echo ($company['tax_regime_id'] == $regime['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($regime['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btnback">Volver</a>
                        <button type="submit" class="btn ms-3">Actualizar Compañia</button>
                    </div>


                </form>
            </div>
        </div>
    </main>
</body>

</html>
<?php require_once '../../includes/footer.php'; ?>