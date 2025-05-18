<?php
require_once '../../config/setup.php';
requireAuth();

$db   = new Database();
$conn = $db->connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $client_id         = isset($_POST['client_id']) ? (int) $_POST['client_id'] : null;
    $first_name        = mb_strtoupper(trim($_POST['first_name'] ?? ''), 'UTF-8');
    $last_name         = mb_strtoupper(trim($_POST['last_name'] ?? ''), 'UTF-8');
    $mother_last_name  = mb_strtoupper(trim($_POST['mother_last_name'] ?? ''), 'UTF-8');
    $phone             = trim($_POST['phone'] ?? '');
    $email             = trim($_POST['email'] ?? '');
    $rfc               = mb_strtoupper(trim($_POST['rfc'] ?? ''), 'UTF-8');
    $address           = mb_strtoupper(trim($_POST['address'] ?? ''), 'UTF-8');
    $regimen_fiscal    = trim($_POST['regimen_fiscal'] ?? '');

    // Validaciones
    if ($first_name === '') {
        $errors['first_name'] = 'El nombre es requerido';
    }
    if ($last_name === '') {
        $errors['last_name'] = 'El apellido paterno es requerido';
    }
    if ($regimen_fiscal === '') {
        $errors['regimen_fiscal'] = 'El régimen fiscal es requerido';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El email no es válido';
    }
    if ($rfc !== '' && (strlen($rfc) < 12 || strlen($rfc) > 13)) {
        $errors['rfc'] = 'El RFC debe tener 12 o 13 caracteres';
    }
    // Verificar que el régimen exista
    if (empty($errors)) {
        $val = $conn->prepare("SELECT 1 FROM sat_regimen_fiscal WHERE codigo = ?");
        $val->execute([$regimen_fiscal]);
        if (!$val->fetch()) {
            $errors['regimen_fiscal'] = 'Régimen fiscal inválido';
        }
    }

    if (empty($errors)) {
        try {
            if ($client_id) {
                // Actualizar cliente
                $stmt = $conn->prepare(
                    "UPDATE clients SET
                        first_name       = :first_name,
                        last_name        = :last_name,
                        mother_last_name = :mother_last_name,
                        phone            = :phone,
                        email            = :email,
                        rfc              = :rfc,
                        address          = :address,
                        regimen_fiscal   = :regimen_fiscal
                     WHERE id = :id"
                );
                $stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
            } else {
                // Insertar nuevo cliente
                $stmt = $conn->prepare(
                    "INSERT INTO clients
                        (first_name, last_name, mother_last_name, phone, email, rfc, address, regimen_fiscal)
                     VALUES
                        (:first_name, :last_name, :mother_last_name, :phone, :email, :rfc, :address, :regimen_fiscal)"
                );
            }
            // Bindeo común
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':mother_last_name', $mother_last_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':rfc', $rfc);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':regimen_fiscal', $regimen_fiscal);

            if ($stmt->execute()) {
                if ($client_id) {
                    $_SESSION['success_message'] = 'Cliente actualizado correctamente';
                    redirect('/swift_invoice/modules/clients/edit.php?id=' . $client_id);
                } else {
                    $_SESSION['success_message'] = 'Cliente creado correctamente';
                    redirect('/swift_invoice/modules/clients/create.php');
                }
            } else {
                throw new Exception('Error al guardar el cliente. Intente nuevamente.');
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['client_save_error'] = 'El email, RFC o datos duplicados ya están registrados';
            } else {
                $_SESSION['client_save_error'] = 'Error en la base de datos: ' . $e->getMessage();
            }
            redirect($client_id 
                ? '/swift_invoice/modules/clients/edit.php?id=' . $client_id
                : '/swift_invoice/modules/clients/create.php'
            );
        } catch (Exception $e) {
            $_SESSION['client_save_error'] = $e->getMessage();
            redirect($client_id 
                ? '/swift_invoice/modules/clients/edit.php?id=' . $client_id
                : '/swift_invoice/modules/clients/create.php'
            );
        }
    }

    // Si hay errores, guardarlos en sesión y redirigir
    if (!empty($errors)) {
        $_SESSION['client_form_errors'] = $errors;
        $_SESSION['client_form_data'] = [
            'first_name'       => $first_name,
            'last_name'        => $last_name,
            'mother_last_name' => $mother_last_name,
            'phone'            => $phone,
            'email'            => $email,
            'rfc'              => $rfc,
            'address'          => $address,
            'regimen_fiscal'   => $regimen_fiscal
        ];
        redirect($client_id 
            ? '/swift_invoice/modules/clients/edit.php?id=' . $client_id
            : '/swift_invoice/modules/clients/create.php'
        );
    }
} else {
    redirect('/swift_invoice/modules/clients/');
}