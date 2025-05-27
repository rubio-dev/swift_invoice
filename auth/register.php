<?php
require_once '../config/setup.php';

$db = new Database();
$conn = $db->connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    $first_name = mb_strtoupper($first_name, 'UTF-8');
    $last_name = mb_strtoupper($last_name, 'UTF-8');

    // Validaciones
    if (empty($username)) {
        $errors['username'] = 'El usuario es requerido';
    } elseif (strlen($username) < 4) {
        $errors['username'] = 'El usuario debe tener al menos 4 caracteres';
    }

    if (empty($password)) {
        $errors['password'] = 'La contraseña es requerida';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Las contraseñas no coinciden';
    }

    if (empty($first_name)) {
        $errors['first_name'] = 'El nombre es requerido';
    }

    if (empty($email)) {
        $errors['email'] = 'El email es requerido';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'El email no es válido';
    }

    // Verificar si el usuario o email ya existen
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $errors['general'] = 'El usuario o email ya están registrados';
        }
    }

    // Registrar usuario si no hay errores
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("
            INSERT INTO users (username, password, first_name, last_name, phone, email)
            VALUES (:username, :password, :first_name, :last_name, :phone, :email)
        ");
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Registro exitoso. Por favor inicie sesión.';
            redirect('login.php');
        } else {
            $errors['general'] = 'Error al registrar. Intente nuevamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice - Registro</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h1>Swift Invoice</h1>
        <h2>Registro de Usuario</h2>
        
        <?php if (isset($errors['general'])): ?>
            <div class="alert error"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
                <?php if (isset($errors['username'])): ?>
                    <span class="error-text"><?php echo $errors['username']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-text"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error-text"><?php echo $errors['confirm_password']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="first_name">Nombre:</label>
                <input type="text" id="first_name" name="first_name"  oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" required>
                <?php if (isset($errors['first_name'])): ?>
                    <span class="error-text"><?php echo $errors['first_name']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="last_name">Apellido:</label>
                <input type="text" id="last_name" name="last_name"  oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Teléfono:</label>
                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" inputmode="numeric"
                  title="Ingrese un teléfono válido de 10 dígitos"
                  
                  oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                  <?php if (isset($errors['email'])): ?>
                    <span class="error-text"><?php echo $errors['phone']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-text"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        
        <div class="auth-links">
            <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>
</body>
</html>