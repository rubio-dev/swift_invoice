<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Agregar Empresa - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener catálogos
$types_stmt = $conn->query("SELECT id, name FROM business_types ORDER BY name");
$business_types = $types_stmt->fetchAll(PDO::FETCH_ASSOC);

$regimes_stmt = $conn->query("SELECT id, name FROM tax_regimes ORDER BY name");
$tax_regimes = $regimes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar datos
$company = [
    'business_name' => '',
    'rfc' => '',
    'fiscal_address' => '',
    'phone' => '',
    'email' => '',
    'legal_representative' => '',
    'business_type_id' => '',
    'tax_regime_id' => ''
];

if (isset($_SESSION['company_form_data'])) {
    $company = $_SESSION['company_form_data'];
    unset($_SESSION['company_form_data']);
}

$errors = [];
if (isset($_SESSION['company_form_errors'])) {
    $errors = $_SESSION['company_form_errors'];
    unset($_SESSION['company_form_errors']);
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/swift_invoice/assets/css/companies.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

 <?php
    if (isset($_SESSION['success_message'])) {
      echo '<script>
        Swal.fire({
          icon: "success",
          title: "' . $_SESSION['success_message'] . '",
          text: "Redirigiendo al listado...",
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          window.location.href = "/swift_invoice/modules/company/";
        });
      </script>';
      unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['company_save_error'])) {
      echo '<script>
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "' . $_SESSION['company_save_error'] . '",
          confirmButtonText: "OK",
          didOpen: () => { document.body.style.paddingRight = "0px"; }
        });
      </script>';
      unset($_SESSION['company_save_error']);
    }
  ?> 

   <main class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="clients-container" style="max-width: 820px;">
      <!-- Encabezado de la tarjeta/formulario -->
      <div class="card-header rounded-top-4 px-4 py-3">
        <h2 class="card-title mb-0 fw-bold text-center">Agregar Empresa</h2>
      </div>

      <div class="card-body px-2 py-2">
        <!-- Formulario principal. POST hacia save.php -->
        <form method="POST" action="save.php">
          <div class="row">    
          <!-- Campo: Nombre de la Empresa -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="business_name">Nombre de la Empresa:</label>
                <input type="text" id="business_name" name="business_name" class="form-control"
                  pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                  value="<?php echo htmlspecialchars($company['business_name']); ?>" required>
                <?php if (isset($errors['business_name'])): ?>
                  <span class="error-text"><?php echo $errors['business_name']; ?></span>
                <?php endif; ?>
              </div>
            </div>

             <!-- Campo: RFC -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" class="form-control" maxlength="13" minlength="12"
                  value="<?php echo htmlspecialchars($company['rfc']); ?>">
                <?php if (isset($errors['rfc'])): ?>
                  <span class="error-text"><?php echo $errors['rfc']; ?></span>
                <?php endif; ?>
                <small class="text-danger" id="rfcError" style="display:none;">
                  Ingrese un RFC válido (12-13 caracteres, solo letras mayúsculas y números).
                </small>
              </div>
            </div>
          
          </div>

          <div class="row">
            <!-- Campo: Dirección -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="fiscal_address">Dirección:</label>
                <textarea id="fiscal_address" name="fiscal_address" class="form-control"
                  rows="3"><?php echo htmlspecialchars($company['fiscal_address']); ?></textarea>
                <small class="text-danger" id="fiscal_addressError" style="display:none;">Ingrese una dirección válida (mínimo 20 caracteres).</small>
              </div>
            </div>

            <!-- Campo: Teléfono -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="phone">Teléfono:</label>
                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" inputmode="numeric"
                  title="Ingrese un teléfono válido de 10 dígitos"
                  value="<?php echo htmlspecialchars($company['phone']); ?>" 
                  oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              </div>
            </div>
          </div>

   <div class="row">
            <!-- Campo: Email -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control"
                  value="<?php echo htmlspecialchars($company['email']); ?>">
                <?php if (isset($errors['email'])): ?>
                  <span class="error-text"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
                <small class="text-danger" id="emailError" style="display:none;">Ingrese un email válido (mínimo 10 caracteres).</small>
              </div>
            </div>
          
              <!-- Campo: Representante -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="legal_representative">Representante Legal:</label>
                <input type="text" id="legal_representative" name="legal_representative" class="form-control"
                   pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}"  title="Solo letras y espacios"
                  value="<?php echo htmlspecialchars($company['legal_representative']); ?>" required>
                <?php if (isset($errors['legal_representative'])): ?>
                  <span class="error-text"><?php echo $errors['legal_representative']; ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>

        <div class="row">
  <!-- Campo: Razón Social -->
  <div class="col-md-6">
    <div class="form-group">
      <label class="input-title" for="business_type_id">Razón Social:</label>
      <select id="business_type_id" name="business_type_id" class="form-control" required>
        <option value="">Seleccione una opción</option>
        <?php foreach ($business_types as $type): ?>
          <option value="<?php echo $type['id']; ?>"
            <?php echo ($company['business_type_id'] == $type['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($type['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['business_type_id'])): ?>
        <span class="text-danger"><?php echo $errors['business_type_id']; ?></span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Campo: Régimen Fiscal -->
  <div class="col-md-6">
    <div class="form-group">
      <label class="input-title" for="tax_regime_id">Régimen Fiscal:</label>
      <select id="tax_regime_id" name="tax_regime_id" class="form-control" required>
        <option value="">Seleccione una opción</option>
        <?php foreach ($tax_regimes as $regime): ?>
          <option value="<?php echo $regime['id']; ?>"
            <?php echo ($company['tax_regime_id'] == $regime['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($regime['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['tax_regime_id'])): ?>
        <span class="text-danger"><?php echo $errors['tax_regime_id']; ?></span>
      <?php endif; ?>
    </div>
  </div>
</div>


          <!-- Botones para volver o guardar -->
          <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btnback">Volver</a>
            <button type="submit" class="btn ms-3">Guardar Cliente</button>
          </div>
        </form>
      </div>
    </div>
</main>

  <!-- Validación JS para email, dirección y RFC antes de enviar -->
  <script>
    document.querySelector('form').addEventListener('submit', function (e) {
      let email = document.getElementById('email');
      let fiscal_address = document.getElementById('fiscal_address');
      let rfc = document.getElementById('rfc');
      let valid = true;

      // Validar email (mínimo 10 caracteres)
      if (email.value.trim().length < 10) {
        document.getElementById('emailError').style.display = 'block';
        valid = false;
      } else {
        document.getElementById('emailError').style.display = 'none';
      }

      // Validar dirección (mínimo 20 caracteres)
      if (fiscal_address.value.trim().length < 20) {
        document.getElementById('fiscal_addressError').style.display = 'block';
        valid = false;
      } else {
        document.getElementById('fiscal_addressError').style.display = 'none';
      }

      // Validar RFC (no vacío y formato correcto)
      const rfcRegex = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{2,3}$/;
      const rfcValue = rfc.value.trim();
      if (rfcValue === '' || !rfcRegex.test(rfcValue)) {
        document.getElementById('rfcError').style.display = 'block';
        valid = false;
      } else {
        document.getElementById('rfcError').style.display = 'none';
      }

      // Si hay errores, evitar envío del formulario
      if (!valid) e.preventDefault();
    });

    // Convertir RFC a mayúsculas automáticamente mientras se escribe
    document.getElementById('rfc').addEventListener('input', function () {
      this.value = this.value.toUpperCase();
    });
  </script>


</body>
</html>



<?php require_once '../../includes/footer.php'; ?>
