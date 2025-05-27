<?php
// Incluye configuración general y protege la vista (solo usuarios autenticados)
require_once '../../config/setup.php';
requireAuth();

$page_title = "Editar Cliente - Swift Invoice";
// Incluye el encabezado HTML estándar
require_once '../../includes/header.php';

// Valida que el parámetro 'id' venga y sea numérico, de lo contrario, redirige
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['client_fetch_error'] = 'ID de cliente inválido.';
    redirect('/swift_invoice/modules/clients/');
}

// Conexión a la base de datos
$db = new Database();
$conn = $db->connect();

$client_id = (int) $_GET['id'];

// Obtiene los datos del cliente con el ID proporcionado
$stmt = $conn->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
$stmt->execute();

// Si no se encuentra el cliente, redirige y muestra error
if ($stmt->rowCount() === 0) {
    $_SESSION['client_fetch_error'] = 'Error al consultar los datos del cliente.';
    redirect('/swift_invoice/modules/clients/');
}

// Carga datos actuales del cliente (o los que vengan de un error previo en el guardado)
$client = $stmt->fetch(PDO::FETCH_ASSOC);

// Carga errores y datos previos si existen (por validaciones fallidas)
$errors = [];
if (isset($_SESSION['client_form_errors'])) {
    $errors = $_SESSION['client_form_errors'];
    unset($_SESSION['client_form_errors']);
    if (isset($_SESSION['client_form_data'])) {
        $client = $_SESSION['client_form_data'];
        unset($_SESSION['client_form_data']);
    }
}

// Carga catálogo de regímenes fiscales para el select
$stmt2 = $conn->prepare("SELECT id, name FROM tax_regimes ORDER BY id");
$stmt2->execute();
$taxRegimes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS para estilos responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Estilos personalizados del módulo de clientes -->
    <link rel="stylesheet" href="/swift_invoice/assets/css/clients.css" />
    <!-- SweetAlert2 para mensajes bonitos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <main class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="clients-container" style="max-width: 820px;">
            <!-- Encabezado de la tarjeta/formulario -->
            <div class="card-header rounded-top-4 px-4 py-3">
                <h2 class="card-title mb-0 fw-bold text-center">Editar Cliente</h2>
            </div>
            <div class="card-body px-2 py-2">
                <!-- Muestra error general si aplica -->
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>

                <!-- Formulario principal. POST hacia save.php -->
                <form method="POST" action="save.php">
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

                    <div class="row">
                        <!-- Campo: Nombre(s) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="first_name">Nombre(s):</label>
                                <input type="text" id="first_name" name="first_name" class="form-control"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                                    value="<?php echo htmlspecialchars($client['first_name']); ?>" oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" required>
                                <?php if (isset($errors['first_name'])): ?>
                                    <span class="error-text"><?php echo $errors['first_name']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Campo: Apellido paterno -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="last_name">Apellido Paterno:</label>
                                <input type="text" id="last_name" name="last_name" class="form-control"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                                    value="<?php echo htmlspecialchars($client['last_name']); ?>" oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" required>
                                <?php if (isset($errors['last_name'])): ?>
                                    <span class="error-text"><?php echo $errors['last_name']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo: Apellido materno -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="mother_last_name">Apellido Materno:</label>
                                <input type="text" id="mother_last_name" name="mother_last_name" class="form-control"
                                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                                    value="<?php echo htmlspecialchars($client['mother_last_name']); ?>" oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" required>
                            </div>
                        </div>
                        <!-- Campo: Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="phone">Teléfono:</label>
                                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}"
                                    title="Ingrese un teléfono válido de 10 dígitos"
                                    value="<?php echo htmlspecialchars($client['phone']); ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo: Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($client['email']); ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <span class="error-text"><?php echo $errors['email']; ?></span>
                                <?php endif; ?>
                                <small class="text-danger" id="emailError" style="display:none;">Ingrese un email válido
                                    (mínimo 10 caracteres).</small>
                            </div>
                        </div>

                        <!-- Campo: Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="address">Dirección:</label>
                                <textarea id="address" name="address" class="form-control"
                                    rows="3"><?php echo htmlspecialchars($client['address']); ?></textarea>
                                <small class="text-danger" id="addressError" style="display:none;">Ingrese una dirección válida (mínimo 20 caracteres).</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo: Régimen Fiscal -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="tax_regime_id">Régimen Fiscal:</label>
                                <select name="tax_regime_id" id="tax_regime_id" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($taxRegimes as $opt):
                                        $sel = (isset($client['tax_regime_id'])
                                            && $client['tax_regime_id'] == $opt['id'])
                                            ? 'selected' : '';
                                        ?>
                                        <option value="<?= htmlspecialchars($opt['id']) ?>" <?= $sel ?>>
                                            <?= htmlspecialchars($opt['id'] . ' – ' . $opt['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['tax_regime_id'])): ?>
                                    <span class="error-text"><?= htmlspecialchars($errors['tax_regime_id']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Campo: RFC -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-title" for="rfc">RFC:</label>
                                <input type="text" id="rfc" name="rfc" class="form-control"
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

                    <!-- Botones para volver o guardar -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btnback">Volver</a>
                        <button type="submit" class="btn ms-3">Actualizar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Validaciones JS para email, dirección y RFC antes de enviar -->
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            let email = document.getElementById('email'),
                address = document.getElementById('address'),
                rfc = document.getElementById('rfc'),
                emailError = document.getElementById('emailError'),
                addressError = document.getElementById('addressError'),
                rfcError = document.getElementById('rfcError'),
                valid = true;

            // Validar email (mínimo 10 caracteres)
            if (email.value.trim().length < 10) {
                emailError.style.display = 'block'; valid = false;
            } else emailError.style.display = 'none';

            // Validar dirección (mínimo 20 caracteres)
            if (address.value.trim().length < 20) {
                addressError.style.display = 'block'; valid = false;
            } else addressError.style.display = 'none';

            // Validar RFC (formato oficial)
            const rfcRegex = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{2,3}$/,
                rfcValue = rfc.value.trim();
            if (!rfcRegex.test(rfcValue)) {
                rfcError.style.display = 'block'; valid = false;
            } else rfcError.style.display = 'none';

            // Si hay errores, evita envío
            if (!valid) e.preventDefault();
        });

        // Convierte RFC a mayúsculas automáticamente al escribir
        document.getElementById('rfc').addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    </script>

    <?php
    // SweetAlert2 para mensajes tras guardar/errores (se limpia la sesión después)
    if (isset($_SESSION['success_message'])) {
        echo '<script>
      Swal.fire({
        icon: "success",
        title: "¡Éxito!",
        text: "' . $_SESSION['success_message'] . '",
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = "/swift_invoice/modules/clients/";
      });
    </script>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['client_fetch_error'])) {
        echo '<script>
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "' . $_SESSION['client_fetch_error'] . '",
        confirmButtonText: "OK"
      });
    </script>';
        unset($_SESSION['client_fetch_error']);
    }
    if (isset($_SESSION['client_save_error'])) {
        echo '<script>
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "' . $_SESSION['client_save_error'] . '",
        confirmButtonText: "OK"
      });
    </script>';
        unset($_SESSION['client_save_error']);
    }

    // Incluye el pie de página y scripts globales
    require_once '../../includes/footer.php';
    ?>
</body>
</html>
