<?php
$upload_result = ['success' => false];

// Directorio donde se guardarán los logos
$upload_dir = '../../assets/images/logos/';

// Crear el directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Validar que se haya subido un archivo
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['logo'];
    
    // Validar que sea una imagen
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);
    
    if (!in_array($mime_type, $allowed_types)) {
        $upload_result['error'] = 'Solo se permiten archivos de imagen (JPEG, PNG, GIF)';
        return;
    }
    
    // Validar tamaño del archivo (máximo 2MB)
    if ($file['size'] > 2097152) {
        $upload_result['error'] = 'El archivo es demasiado grande (máximo 2MB)';
        return;
    }
    
    // Generar un nombre único para el archivo
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = 'logo_' . time() . '.' . strtolower($file_ext);
    $file_path = $upload_dir . $file_name;
    
    // Mover el archivo al directorio de logos
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $upload_result['success'] = true;
        $upload_result['path'] = 'assets/images/logos/' . $file_name;
        
        // Si había un logo anterior, eliminarlo
        if (!empty($company_exists)) {
            $stmt = $conn->prepare("SELECT logo_path FROM companies WHERE id = :id");
            $stmt->bindParam(':id', $company_exists['id']);
            $stmt->execute();
            $old_logo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!empty($old_logo['logo_path']) && file_exists('../../' . $old_logo['logo_path'])) {
                unlink('../../' . $old_logo['logo_path']);
            }
        }
    } else {
        $upload_result['error'] = 'Error al subir el archivo';
    }
} elseif (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
    $upload_result['error'] = 'Error al subir el archivo: código ' . $_FILES['logo']['error'];
}
?>