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

$client_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->bindParam(':id', $client_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    $_SESSION['client_fetch_error'] = 'Error al consultar los datos del cliente.';
    redirect('/swift_invoice/modules/clients/');
}

$client = $stmt->fetch(PDO::FETCH_ASSOC);

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
        <div class="clients-container" style="max-width: 650px;">

            <div class="card-header rounded-top-4 px-4 py-3">
                <h2 class="card-title mb-0 fw-bold text-center">Detalles del Cliente</h2>
            </div>

            <div class="card-body px-4 py-3">
                <div class="form-group mb-3">
                    <label class="input-title-Details">Nombre(s):</label>
                    <label class="detailsData"><?php echo htmlspecialchars($client['first_name']); ?></label>
                </div>
                <div class="form-group mb-3">
                    <label class="input-title-Details">Apellido Paterno:</label>
                    <label class="detailsData"><?php echo htmlspecialchars($client['last_name']); ?></label>
                </div>
                <div class="form-group mb-3">
                    <label class="input-title-Details">Apellido Materno:</label>
                    <label class="detailsData"><?php echo htmlspecialchars($client['mother_last_name']); ?></label>
                </div>
                <div class="form-group mb-3">
                    <label class="input-title-Details">Teléfono:</label>
                    <label class="detailsData"><?php echo htmlspecialchars($client['phone']); ?></label>
                </div>
                <div class="form-group mb-3">
                    <label class="input-title-Details">Email:</label>
                    <label class="detailsData"><?php echo htmlspecialchars($client['email']); ?></label>
                </div>
                <div class="form-group mb-3">
                    <label class="input-title-Details">RFC:</label>
                    <label class="detailsData"><?php echo htmlspecialchars($client['rfc']); ?></label>
                </div>
                <div class="form-group mb-3">
                    <label class="input-title-Details">Dirección:</label>
                    <label class="detailsData d-block" style="white-space: pre-wrap;"><?php echo htmlspecialchars($client['address']); ?></label>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="index.php" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>