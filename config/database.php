<?php
// Clase para manejar la conexión a la base de datos del sistema
class Database {
    // Datos de conexión (pueden cambiarse según ambiente)
    private $host = 'localhost'; // Host del servidor de base de datos
    private $port = '3306';      // Puerto MySQL, por defecto 3306 (XAMPP/MAMP)
    private $db_name = 'swift_invoice_db'; // Nombre de la base de datos
    private $username = 'root';  // Usuario de la base de datos
    private $password = '';      // Contraseña del usuario
    private $conn;              // Variable para almacenar la conexión

    // Método para obtener la conexión PDO
    public function connect() {
        $this->conn = null;

        try {
            // Crea la conexión PDO usando los datos configurados
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}", 
                $this->username, 
                $this->password
            );
            // Habilita excepciones para manejo de errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Imprime el error en pantalla (solo recomendado para desarrollo)
            echo "Connection error: " . $e->getMessage();
        }

        // Devuelve el objeto de conexión PDO o null si falló
        return $this->conn;
    }
}
?>
