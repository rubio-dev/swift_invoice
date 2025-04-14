<?php
require_once '../../config/setup.php';
requireAuth();

$db = new Database();
$conn = $db->connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $business_name = trim($_POST['business_name']);
    $rfc = trim($_POST['rfc']);
    $fiscal_address = trim($_POST['fiscal_address']);
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tax_regime = trim($_POST['tax_regime'] ?? '');
    $legal_representative = trim($_POST['legal_representative'] ?? '');

    // Validaciones
    if (empty($business_name)) {
        $errors['business_name'] = 'La razón social es requerida';
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

    // Si no hay errores, guardar en la base de datos
    if (empty($errors)) {
        try {
            if ($company_exists) {
                // Actualizar empresa existente
                $stmt = $conn->prepare("
                    UPDATE companies SET
                    business_name = :business_name,
                    rfc = :rfc,
                    fiscal_address = :fiscal_address,
                    phone = :phone,
                    email = :email,
                    tax_regime = :tax_regime,
                    legal_representative = :legal_representative
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $company_exists['id']);
            } else {
                // Insertar nueva empresa
                $stmt = $conn->prepare("
                    INSERT INTO companies 
                    (business_name, rfc, fiscal_address, phone, email, tax_regime, legal_representative)
                    VALUES 
                    (:business_name, :rfc, :fiscal_address, :phone, :email, :tax_regime, :legal_representative)
                ");
            }
            
            $stmt->bindParam(':business_name', $business_name);
            $stmt->bindParam(':rfc', $rfc);
            $stmt->bindParam(':fiscal_address', $fiscal_address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':tax_regime', $tax_regime);
            $stmt->bindParam(':legal_representative', $legal_representative);
            
            if ($stmt->execute()) {
                // Manejar la subida del logo si se proporcionó
                if (!empty($_FILES['logo']['name'])) {
                    require_once 'upload_logo.php';
                    
                    if (isset($upload_result) && $upload_result['success']) {
                        // Actualizar la ruta del logo en la base de datos
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
    
    // Si hay errores, mostrar el formulario nuevamente
    if (!empty($errors)) {
        $company = [
            'business_name' => $business_name,
            'rfc' => $rfc,
            'fiscal_address' => $fiscal_address,
            'phone' => $phone,
            'email' => $email,
            'tax_regime' => $tax_regime,
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