<?php
require_once '../../config/setup.php';
requireAuth();

$page_title = "Clientes - Swift Invoice";
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->connect();

// Obtener lista de clientes
$stmt = $conn->query("SELECT * FROM clients ORDER BY last_name, first_name");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Clientes</h2>
        <a href="create.php" class="btn btn-success">Agregar Cliente</a>
    </div>
    
    <div class="card-body">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (empty($clients)): ?>
            <p>No hay clientes registrados.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>RFC</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['last_name'] . ' ' . $client['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($client['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($client['email'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($client['rfc'] ?? '-'); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $client['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>

                                    <form action="delete.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>
