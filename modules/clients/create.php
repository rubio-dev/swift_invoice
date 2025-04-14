<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Agregar Cliente - Swift Invoice";
require_once '../../includes/header.php';

$errors = [];
$client = [
    'first_name' => '',
    'last_name' => '',
    'mother_last_name' => '',
    'phone' => '',
    'email' => '',
    'rfc' => '',
    'address' => ''
];
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Agregar Nuevo Cliente</h2>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </div>
    
    <div class="card-body">
        <form method="POST" action="save.php">
            <div class="form-group">
                <label for="first_name">Nombre(s):</label>
                <input type="text" id="first_name" name="first_name" class="form-control" 
                       value="<?php echo htmlspecialchars($client['first_name']); ?>" required>
                <?php if (isset($errors['first_name'])): ?>
                    <span class="error-text"><?php echo $errors['first_name']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="last_name">Apellido Paterno:</label>
                <input type="text" id="last_name" name="last_name" class="form-control" 
                       value="<?php echo htmlspecialchars($client['last_name']); ?>" required>
                <?php if (isset($errors['last_name'])): ?>
                    <span class="error-text"><?php echo $errors['last_name']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="mother_last_name">Apellido Materno:</label>
                <input type="text" id="mother_last_name" name="mother_last_name" class="form-control" 
                       value="<?php echo htmlspecialchars($client['mother_last_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Teléfono:</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       value="<?php echo htmlspecialchars($client['phone']); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($client['email']); ?>">
                <?php if (isset($errors['email'])): ?>
                    <span class="error-text"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" class="form-control" 
                       value="<?php echo htmlspecialchars($client['rfc']); ?>">
                <?php if (isset($errors['rfc'])): ?>
                    <span class="error-text"><?php echo $errors['rfc']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="address">Dirección:</label>
                <textarea id="address" name="address" class="form-control" 
                          rows="3"><?php echo htmlspecialchars($client['address']); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">Guardar Cliente</button>
        </form>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>