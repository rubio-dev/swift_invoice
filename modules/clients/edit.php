<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Editar Cliente - Swift Invoice";
require_once '../../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['client_fetch_error'] = 'ID de cliente inválido.';
    redirect('/swift_invoice/modules/clients/');
}

$db = new Database();
$conn = $db->connect();

$client_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->bindParam(':id', $client_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['client_fetch_error'] = 'Error al consultar los datos del cliente.';
    redirect('/swift_invoice/modules/clients/');
}

$client = $stmt->fetch(PDO::FETCH_ASSOC);

// Mostrar errores si existen
$errors = [];
if (isset($_SESSION['client_form_errors'])) {
    $errors = $_SESSION['client_form_errors'];
    unset($_SESSION['client_form_errors']);

    if (isset($_SESSION['client_form_data'])) {
        $client = $_SESSION['client_form_data'];
        unset($_SESSION['client_form_data']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/swift_invoice/assets/css/clients.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <main class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="clients-container" style="max-width: 650px;">

            <div class="card-header rounded-top-4 px-4 py-3">
                <h2 class="card-title mb-0 fw-bold text-center">Editar Cliente</h2>
            </div>

            <div class="card-body px-2 py-2">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>

                <form method="POST" action="save.php">
                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

                    <div class="form-group">
                        <label class="input-title" for="first_name">Nombre(s):</label>
                        <input type="text" id="first_name" name="first_name" class="form-control"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                            value="<?php echo htmlspecialchars($client['first_name']); ?>" required />
                        <?php if (isset($errors['first_name'])): ?>
                            <span class="error-text"><?php echo $errors['first_name']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="input-title" for="last_name">Apellido Paterno:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                            value="<?php echo htmlspecialchars($client['last_name']); ?>" required />
                        <?php if (isset($errors['last_name'])): ?>
                            <span class="error-text"><?php echo $errors['last_name']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="input-title" for="mother_last_name">Apellido Materno:</label>
                        <input type="text" id="mother_last_name" name="mother_last_name" class="form-control"
                            pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{1,50}" title="Solo letras y espacios"
                            value="<?php echo htmlspecialchars($client['mother_last_name']); ?>" />
                    </div>

                    <div class="form-group">
                        <label class="input-title" for="phone">Teléfono:</label>
                        <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}"
                            title="Ingrese un teléfono válido de 10 dígitos"
                            value="<?php echo htmlspecialchars($client['phone']); ?>" />
                    </div>

                    <div class="form-group">
                        <label class="input-title" for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($client['email']); ?>" />
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-text"><?php echo $errors['email']; ?></span>
                        <?php endif; ?>
                        <small class="text-danger" id="emailError" style="display:none;">Ingrese un email válido (mínimo
                            10
                            caracteres).</small>
                    </div>

                    <div class="form-group">
                        <label class="input-title" for="rfc">RFC:</label>
                        <input type="text" id="rfc" name="rfc" class="form-control"
                            value="<?php echo htmlspecialchars($client['rfc']); ?>" />
                        <?php if (isset($errors['rfc'])): ?>
                            <span class="error-text"><?php echo $errors['rfc']; ?></span>
                        <?php endif; ?>
                        <small class="text-danger" id="rfcError" style="display:none;">
                            Ingrese un RFC válido (12-13 caracteres, solo letras mayúsculas y números).
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="input-title" for="address">Dirección:</label>
                        <textarea id="address" name="address" class="form-control"
                            rows="3"><?php echo htmlspecialchars($client['address']); ?></textarea>
                        <small class="text-danger" id="addressError" style="display:none;">Ingrese una dirección válida
                            (mínimo 20
                            caracteres).</small>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btnback">Volver</a>
                        <button type="submit" class="btn ms-3">Actualizar Cliente</button>
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

            let emailError = document.getElementById('emailError');
            let addressError = document.getElementById('addressError');
            let rfcError = document.getElementById('rfcError');

            let valid = true;

            // Validar email (mínimo 10 caracteres)
            if (email.value.trim().length < 10) {
                if (emailError) emailError.style.display = 'block';
                valid = false;
            } else {
                if (emailError) emailError.style.display = 'none';
            }

            // Validar dirección (mínimo 20 caracteres)
            if (address.value.trim().length < 20) {
                if (addressError) addressError.style.display = 'block';
                valid = false;
            } else {
                if (addressError) addressError.style.display = 'none';
            }

            // Validar RFC (formato correcto)
            const rfcRegex = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{2,3}$/;
            const rfcValue = rfc.value.trim();
            if (rfcValue === '' || !rfcRegex.test(rfcValue)) {
                if (rfcError) rfcError.style.display = 'block';
                valid = false;
            } else {
                if (rfcError) rfcError.style.display = 'none';
            }

            // Si hay errores, prevenir el envío
            if (!valid) e.preventDefault();
        });

        // RFC en mayúsculas automáticamente
        document.getElementById('rfc').addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    </script>

    <?php
    // Mensaje de éxito al actualizar cliente
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

    // Mostrar error al consultar cliente
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

    // Mostrar error al guardar cliente
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

    require_once '../../includes/footer.php';
    ?>
</body>

</html>