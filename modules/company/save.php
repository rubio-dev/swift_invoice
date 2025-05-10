<?php
require_once '../../config/setup.php';
requireAuth();

$db = new Database();
$conn = $db->connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $business_name = trim($_POST['business_name']);
    $business_type_id = intval($_POST['business_type_id'] ?? 0);
    $tax_regime_id = intval($_POST['tax_regime_id'] ?? 0);
    $rfc = trim($_POST['rfc']);
    $fiscal_address = trim($_POST['fiscal_address']);
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $legal_representative = trim($_POST['legal_representative'] ?? '');

    // Validaciones
    if (empty($business_name)) {
        $errors['business_name'] = 'El nombre de la empresa es requerido';
    }

    if ($business_type_id <= 0) {
        $errors['business_type_id'] = 'Debe seleccionar una razón social válida';
    }

    if ($tax_regime_id <= 0) {
        $errors['tax_regime_id'] = 'Debe seleccionar un régimen fiscal válido';
    }

    if (empty($rfc)) {
        $errors['rfc'] = 'El RFC es requerido';
    } elseif (strlen($rfc) < 12 || strlen($rfc) > 13) {
        $errors['rfc'] = 'El RFC debe tener 12 o 13 caracteres';
    }

    if (empty($fiscal_address)) {
        $errors['fiscal_address'] = 'La dirección fiscal es requerida';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El email no es válido';
    }

    // Verificar si ya existe una empresa registrada
    $stmt = $conn->query("SELECT id FROM companies LIMIT 1");
    $company_exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($errors)) {
        try {
            if ($company_exists) {
                // Actualizar empresa existente
                $stmt = $conn->prepare("
                    UPDATE companies SET
                        business_name = :business_name,
                        business_type_id = :business_type_id,
                        tax_regime_id = :tax_regime_id,
                        rfc = :rfc,
                        fiscal_address = :fiscal_address,
                        phone = :phone,
                        email = :email,
                        legal_representative = :legal_representative
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $company_exists['id']);
            } else {
                // Insertar nueva empresa
                $stmt = $conn->prepare("
                    INSERT INTO companies 
                    (business_name, business_type_id, tax_regime_id, rfc, fiscal_address, phone, email, legal_representative)
                    VALUES 
                    (:business_name, :business_type_id, :tax_regime_id, :rfc, :fiscal_address, :phone, :email, :legal_representative)
                ");
            }

            // Bind comunes
            $stmt->bindParam(':business_name', $business_name);
            $stmt->bindParam(':business_type_id', $business_type_id);
            $stmt->bindParam(':tax_regime_id', $tax_regime_id);
            $stmt->bindParam(':rfc', $rfc);
            $stmt->bindParam(':fiscal_address', $fiscal_address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':legal_representative', $legal_representative);

            if ($stmt->execute()) {
                // Manejar logo si se subió
                if (!empty($_FILES['logo']['name'])) {
                    require_once 'upload_logo.php';

                    if (isset($upload_result) && $upload_result['success']) {
                        $update_logo = $conn->prepare("
                            UPDATE companies SET logo_path = :logo_path 
                            WHERE id = :id
                        ");
                        $id_to_update = $company_exists ? $company_exists['id'] : $conn->lastInsertId();
                        $update_logo->bindParam(':logo_path', $upload_result['path']);
                        $update_logo->bindParam(':id', $id_to_update);
                        $update_logo->execute();
                    } elseif (isset($upload_result['error'])) {
                        $errors['logo'] = $upload_result['error'];
                    }
                }

                if (empty($errors)) {
                    $_SESSION['success_message'] = 'Datos de la empresa guardados correctamente';
                    redirect('/swift_invoice/modules/company/');
                }
            } else {
                $errors['general'] = 'Error al guardar los datos. Intente nuevamente.';
            }

        } catch (PDOException $e) {
            $errors['general'] = 'Error en la base de datos: ' . $e->getMessage();
        }
    }

    // Si hay errores, redirigir con datos y errores
    if (!empty($errors)) {
        $company = [
            'business_name' => $business_name,
            'business_type_id' => $business_type_id,
            'tax_regime_id' => $tax_regime_id,
            'rfc' => $rfc,
            'fiscal_address' => $fiscal_address,
            'phone' => $phone,
            'email' => $email,
            'legal_representative' => $legal_representative
        ];

        $_SESSION['company_form_errors'] = $errors;
        $_SESSION['company_form_data'] = $company;
        redirect('/swift_invoice/modules/company/');
    }

} else {
    redirect('/swift_invoice/modules/company/');
}
?>
