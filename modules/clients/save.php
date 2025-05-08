<?php
require_once '../../config/setup.php';
requireAuth();

$db = new Database();
$conn = $db->connect();

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $client_id = isset($_POST['client_id']) ? (int) $_POST['client_id'] : null;
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $mother_last_name = trim($_POST['mother_last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rfc = trim($_POST['rfc'] ?? '');
    $address = trim($_POST['address'] ?? '');
    //
    // Validaciones (igual que antes)
    if (empty($first_name)) {
        $errors['first_name'] = 'El nombre es requerido';
    }

    if (empty($last_name)) {
        $errors['last_name'] = 'El apellido paterno es requerido';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El email no es válido';
    }

    if (!empty($rfc) && (strlen($rfc) < 12 || strlen($rfc) > 13)) {
        $errors['rfc'] = 'El RFC debe tener 12 o 13 caracteres';
    }

    // Si no hay errores, guardar en la base de datos
    if (empty($errors)) {
        try {
            if ($client_id) {
                // Actualizar cliente existente
                $stmt = $conn->prepare("
                    UPDATE clients SET
                    first_name = :first_name,
                    last_name = :last_name,
                    mother_last_name = :mother_last_name,
                    phone = :phone,
                    email = :email,
                    rfc = :rfc,
                    address = :address
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $client_id);
            } else {
                // Insertar nuevo cliente
                $stmt = $conn->prepare("
                    INSERT INTO clients 
                    (first_name, last_name, mother_last_name, phone, email, rfc, address)
                    VALUES 
                    (:first_name, :last_name, :mother_last_name, :phone, :email, :rfc, :address)
                ");
            }

            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':mother_last_name', $mother_last_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':rfc', $rfc);
            $stmt->bindParam(':address', $address);

            if ($stmt->execute()) {
                if ($client_id) {
                    $_SESSION['success_message'] = 'Cliente actualizado correctamente';
                    redirect('/swift_invoice/modules/clients/edit.php?id=' . $client_id);
                } else {
                    $_SESSION['success_message'] = 'Cliente creado correctamente';
                    redirect('/swift_invoice/modules/clients/create.php');
                }
            } else {
                $_SESSION['client_save_error'] = 'Error al guardar el cliente. Intente nuevamente.';
                if ($client_id) {
                    redirect('/swift_invoice/modules/clients/edit.php?id=' . $client_id);
                } else {
                    redirect('/swift_invoice/modules/clients/create.php');
                }
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // Error de duplicado (email o RFC)
                $_SESSION['client_save_error'] = 'El email o RFC ya están registrados para otro cliente';
                if ($client_id) {
                    redirect('/swift_invoice/modules/clients/edit.php?id=' . $client_id);
                } else {
                    redirect('/swift_invoice/modules/clients/create.php');
                }
            } else {
                $errors['general'] = 'Error en la base de datos: ' . $e->getMessage();
            }
        }

    }

    // Si hay errores, mostrar el formulario nuevamente
    if (!empty($errors)) {
        $client = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mother_last_name' => $mother_last_name,
            'phone' => $phone,
            'email' => $email,
            'rfc' => $rfc,
            'address' => $address
        ];

        $_SESSION['client_form_errors'] = $errors;
        $_SESSION['client_form_data'] = $client;

        if ($client_id) {
            redirect('/swift_invoice/modules/clients/edit.php?id=' . $client_id);
        } else {
            redirect('/swift_invoice/modules/clients/create.php');
        }
    }
} else {
    redirect('/swift_invoice/modules/clients/');
}

// que rollo willy
?>

