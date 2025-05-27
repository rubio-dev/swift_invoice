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
            <h2 class="card-title mb-0 fw-bold text-center">Editar Empresa</h2>
        </div>

        <div class="card-body px-2 py-2">
            <form method="POST" action="save.php">
                <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-title" for="business_name">Nombre de la Empresa:</label>
                            <input type="text" id="business_name" name="business_name" class="form-control"
                                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                                value="<?php echo htmlspecialchars($company['business_name']); ?>" required>
                            <?php if (isset($errors['business_name'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['business_name']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-title" for="rfc">RFC:</label>
                            <input type="text" id="rfc" name="rfc" class="form-control" maxlength="13" minlength="12"
                                value="<?php echo htmlspecialchars($company['rfc']); ?>">
                            <?php if (isset($errors['rfc'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['rfc']; ?></span>
                            <?php endif; ?>
                            <small class="text-danger" id="rfcError" style="display:none;">
                                Ingrese un RFC válido (12-13 caracteres, solo letras mayúsculas y números).
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-title" for="fiscal_address">Dirección Fiscal:</label>
                            <textarea id="fiscal_address" name="fiscal_address" class="form-control" rows="3"><?php echo htmlspecialchars($company['fiscal_address']); ?></textarea>
                            <?php if (isset($errors['fiscal_address'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['fiscal_address']; ?></span>
                            <?php endif; ?>
                            <small class="text-danger" id="fiscal_addressError" style="display:none;">Ingrese una dirección válida (mínimo 20 caracteres).</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-title" for="phone">Teléfono:</label>
                            <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" inputmode="numeric"
                                title="Ingrese un teléfono válido de 10 dígitos"
                                value="<?php echo htmlspecialchars($company['phone']); ?>" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                            <small class="text-danger" id="phoneError" style="display:none;">Ingrese un teléfono válido de 10 dígitos.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-title" for="email">Email:</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?php echo htmlspecialchars($company['email']); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['email']; ?></span>
                            <?php endif; ?>
                            <small class="text-danger" id="emailError" style="display:none;">Ingrese un email válido (mínimo 10 caracteres).</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-title" for="legal_representative">Representante Legal:</label>
                            <input type="text" id="legal_representative" name="legal_representative" class="form-control"
                                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                                value="<?php echo htmlspecialchars($company['legal_representative']); ?>" oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" required>
                            <?php if (isset($errors['legal_representative'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['legal_representative']; ?></span>
                            <?php endif; ?>
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
                            <?php if (isset($errors['business_type_id'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['business_type_id']; ?></span>
                            <?php endif; ?>
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
                            <?php if (isset($errors['tax_regime_id'])): ?>
                                <span class="error-text text-danger"><?php echo $errors['tax_regime_id']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btnback">Volver</a>
                    <button type="submit" class="btn ms-3">Actualizar Compañía</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.querySelector('form').addEventListener('submit', function (e) {
    let email = document.getElementById('email');
    let fiscal_address = document.getElementById('fiscal_address');
    let rfc = document.getElementById('rfc');
    let phone = document.getElementById('phone');
    let valid = true;

    if (email.value.trim().length < 10) {
        document.getElementById('emailError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('emailError').style.display = 'none';
    }

    if (fiscal_address.value.trim().length < 20) {
        document.getElementById('fiscal_addressError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('fiscal_addressError').style.display = 'none';
    }

    if (!/^\d{10}$/.test(phone.value.trim())) {
        document.getElementById('phoneError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('phoneError').style.display = 'none';
    }

    const rfcRegex = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{2,3}$/;
    if (rfc.value.trim() === '' || !rfcRegex.test(rfc.value.trim())) {
        document.getElementById('rfcError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('rfcError').style.display = 'none';
    }

    if (!valid) e.preventDefault();
});

document.getElementById('rfc').addEventListener('input', function () {
    this.value = this.value.toUpperCase();
});
</script>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<script>
        Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: "' . $_SESSION['success_message'] . '",
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "/swift_invoice/modules/company/";
        });
    </script>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['company_fetch_error'])) {
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "' . $_SESSION['company_fetch_error'] . '",
            confirmButtonText: "OK"
        });
    </script>';
    unset($_SESSION['company_fetch_error']);
}

if (isset($_SESSION['company_save_error'])) {
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "' . $_SESSION['company_save_error'] . '",
            confirmButtonText: "OK"
        });
    </script>';
    unset($_SESSION['company_save_error']);
}

require_once '../../includes/footer.php';
?>
