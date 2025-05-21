<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Agregar Cliente - Swift Invoice";
require_once '../../includes/header.php';

$errors = [];
$client = [
  'first_name' => '',
  'last_name' => '',
  'mother_last_name' => '',
  'phone' => '',
  'email' => '',
  'rfc' => '',
  'address' => ''
];

// Conexión a la base de datos y carga de regímenes fiscales
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare(
  "SELECT codigo, descripcion FROM sat_regimen_fiscal ORDER BY codigo"
);
$stmt->execute();
$regimenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Verifica si se creó o editó correctamente
if (isset($_SESSION['success_message'])) {
  echo '<script>
        Swal.fire({
            icon: "success",
            title: "' . $_SESSION['success_message'] . '",
            text: "Redirigiendo al listado...",
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "/swift_invoice/modules/clients/";
        });
    </script>';
  unset($_SESSION['success_message']);
}

// Verifica si hubo error general
if (isset($_SESSION['client_save_error'])) {
  echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "' . $_SESSION['client_save_error'] . '",
            confirmButtonText: "OK",
            didOpen: () => {
              document.body.style.paddingRight = "0px";
            }
        });
    </script>';
  unset($_SESSION['client_save_error']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Swift Invoice - Agregar Cliente</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tus estilos personalizados -->
  <link rel="stylesheet" href="/swift_invoice/assets/css/clients.css">
</head>

<body>
  <main class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="clients-container" style="max-width: 820px;">
      <div class="card-header rounded-top-4 px-4 py-3">
        <h2 class="card-title mb-0 fw-bold text-center">Agregar Nuevo Cliente</h2>
      </div>

      <div class="card-body px-2 py-2">
        <form method="POST" action="save.php">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="first_name">Nombre(s):</label>
                <input type="text" id="first_name" name="first_name" class="form-control"
                  pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                  value="<?php echo htmlspecialchars($client['first_name']); ?>" required>
                <?php if (isset($errors['first_name'])): ?>
                  <span class="error-text"><?php echo $errors['first_name']; ?></span>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="last_name">Apellido Paterno:</label>
                <input type="text" id="last_name" name="last_name" class="form-control"
                  pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                  value="<?php echo htmlspecialchars($client['last_name']); ?>" required>
                <?php if (isset($errors['last_name'])): ?>
                  <span class="error-text"><?php echo $errors['last_name']; ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="mother_last_name">Apellido Materno:</label>
                <input type="text" id="mother_last_name" name="mother_last_name" class="form-control"
                  pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                  value="<?php echo htmlspecialchars($client['mother_last_name']); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="phone">Teléfono:</label>
                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}"
                  inputmode="numeric"
                  title="Ingrese un teléfono válido de 10 dígitos"
                  value="<?php echo htmlspecialchars($client['phone']); ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control"
                  value="<?php echo htmlspecialchars($client['email']); ?>">
                <?php if (isset($errors['email'])): ?>
                  <span class="error-text"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
                <small class="text-danger" id="emailError" style="display:none;">Ingrese un email válido (mínimo 10
                  caracteres).</small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="address">Dirección:</label>
                <textarea id="address" name="address" class="form-control"
                  rows="3"><?php echo htmlspecialchars($client['address']); ?></textarea>
                <small class="text-danger" id="addressError" style="display:none;">Ingrese una dirección válida (mínimo
                  20 caracteres).</small>
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="regimen_fiscal">Régimen Fiscal:</label>
                <select name="regimen_fiscal" id="regimen_fiscal" class="form-control" required>
                  <option value="">Seleccione...</option>
                  <?php foreach ($regimenes as $opt):
                    $sel = (isset($client['regimen_fiscal']) && $client['regimen_fiscal'] === $opt['codigo']) ? 'selected' : '';
                    ?>
                    <option value="<?= htmlspecialchars($opt['codigo']) ?>" <?= $sel ?>>
                      <?= htmlspecialchars($opt['codigo'] . ' – ' . $opt['descripcion']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($errors['regimen_fiscal'])): ?>
                  <span class="error-text"><?php echo $errors['regimen_fiscal']; ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="input-title" for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" class="form-control" required maxlength="13" minlength="12"
                  value="<?php echo htmlspecialchars($client['rfc']); ?>">
                <?php if (isset($errors['rfc'])): ?>
                  <span class="error-text"><?php echo $errors['rfc']; ?></span>
                <?php endif; ?>
                <small class="text-danger" id="rfcError" style="display:none;">
                  Ingrese un RFC válido (12-13 caracteres, solo letras mayúsculas y números).
                </small>
              </div>
            </div>


          </div>

          <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btnback">Volver</a>
            <button type="submit" class="btn ms-3">Guardar Cliente</button>
          </div>
        </form>

      </div>
    </div>
  </main>

  <script>
    document.querySelector('form').addEventListener('submit', function (e) {
      let email = document.getElementById('email');
      let address = document.getElementById('address');
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
      if (address.value.trim().length < 20) {
        document.getElementById('addressError').style.display = 'block';
        valid = false;
      } else {
        document.getElementById('addressError').style.display = 'none';
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

  <?php
  require_once '../../includes/footer.php';
  ?>
