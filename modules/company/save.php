<?php
require_once '../../config/setup.php';
requireAuth();

$db = new Database();
$conn = $db->connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $company_id = isset($_POST['company_id']) ? (int) $_POST['company_id'] : null;
    $business_name = trim($_POST['business_name']);
    $rfc = trim($_POST['rfc']);
    $fiscal_address = trim($_POST['fiscal_address']);
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $legal_representative = trim($_POST['legal_representative'] ?? '');
    $business_type_id = isset($_POST['business_type_id']) ? (int) $_POST['business_type_id'] : 0;
    $tax_regime_id = isset($_POST['tax_regime_id']) ? (int) $_POST['tax_regime_id'] : 0;

    // Convertir a mayúsculas los campos clave
    $business_name = mb_strtoupper($business_name, 'UTF-8');
    $rfc = mb_strtoupper($rfc, 'UTF-8');
    $fiscal_address = mb_strtoupper($fiscal_address, 'UTF-8');
    $legal_representative = mb_strtoupper($legal_representative, 'UTF-8');

    // Validaciones
    if (empty($business_name)) {
        $errors['business_name'] = 'El nombre de la empresa es requerido';
    }

    if (empty($rfc) || strlen($rfc) < 12 || strlen($rfc) > 13) {
        $errors['rfc'] = 'El RFC debe tener entre 12 y 13 caracteres';
    }

    if (empty($fiscal_address)) {
        $errors['fiscal_address'] = 'La dirección fiscal es requerida';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El correo electrónico no es válido';
    }

    if ($business_type_id <= 0) {
        $errors['business_type_id'] = 'Seleccione una razón social válida';
    }

    if ($tax_regime_id <= 0) {
        $errors['tax_regime_id'] = 'Seleccione un régimen fiscal válido';
    }

    if (empty($legal_representative)) {
        $errors['legal_representative'] = 'El representante legal es requerido';
    }

    // Guardar en base de datos si no hay errores
    if (empty($errors)) {
        try {
            if ($company_id) {
                $stmt = $conn->prepare("
                    UPDATE companies
                       SET business_name         = :business_name,
                           rfc                   = :rfc,
                           fiscal_address        = :fiscal_address,
                           phone                 = :phone,
                           email                 = :email,
                           legal_representative  = :legal_representative,
                           business_type_id      = :business_type_id,
                           tax_regime_id         = :tax_regime_id
                     WHERE id = :id
                ");
                $stmt->bindParam(':id', $company_id);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO companies
                        (business_name, rfc, fiscal_address, phone, email, legal_representative, business_type_id, tax_regime_id)
                    VALUES
                        (:business_name, :rfc, :fiscal_address, :phone, :email, :legal_representative, :business_type_id, :tax_regime_id)
                ");
            }

            // Bind de parámetros
            $stmt->bindParam(':business_name',        $business_name);
            $stmt->bindParam(':rfc',                  $rfc);
            $stmt->bindParam(':fiscal_address',       $fiscal_address);
            $stmt->bindParam(':phone',                $phone);
            $stmt->bindParam(':email',                $email);
            $stmt->bindParam(':legal_representative', $legal_representative);
            $stmt->bindParam(':business_type_id',     $business_type_id);
            $stmt->bindParam(':tax_regime_id',        $tax_regime_id);

            if ($stmt->execute()) {
                // Éxito: establecemos success_message para la alerta
                if ($company_id) {
                    $_SESSION['success_message'] = 'Empresa actualizada correctamente.';
                    redirect('/swift_invoice/modules/company/edit.php?id=' . $company_id);
                } else {
                    // En el caso de CREATE, redirigimos a create.php para disparar tu SweetAlert allí
                    $_SESSION['success_message'] = 'Empresa creada correctamente.';
                    redirect('/swift_invoice/modules/company/create.php');
                }
            } else {
                $_SESSION['company_save_error'] = 'Error al guardar la empresa. Intente nuevamente.';
                if ($company_id) {
                    redirect('/swift_invoice/modules/company/edit.php?id=' . $company_id);
                } else {
                    redirect('/swift_invoice/modules/company/create.php');
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['company_save_error'] = 'El RFC o correo ya están registrados para otra empresa.';
            } else {
                $_SESSION['company_save_error'] = 'Error de base de datos: ' . $e->getMessage();
            }
            if ($company_id) {
                redirect('/swift_invoice/modules/company/edit.php?id=' . $company_id);
            } else {
                redirect('/swift_invoice/modules/company/create.php');
            }
        }
    }

    // Si hay errores de validación, los guardamos en sesión y regresamos al formulario
    if (!empty($errors)) {
        $company = [
            'business_name'        => $business_name,
            'rfc'                  => $rfc,
            'fiscal_address'       => $fiscal_address,
            'phone'                => $phone,
            'email'                => $email,
            'legal_representative' => $legal_representative,
            'business_type_id'     => $business_type_id,
            'tax_regime_id'        => $tax_regime_id
        ];

        $_SESSION['company_form_errors'] = $errors;
        $_SESSION['company_form_data']   = $company;

        if ($company_id) {
            redirect('/swift_invoice/modules/company/edit.php?id=' . $company_id);
        } else {
            redirect('/swift_invoice/modules/company/create.php');
        }
    }
} else {
    // Si no es POST, regresamos al listado de empresas
    redirect('/swift_invoice/modules/company/');
}
?>
