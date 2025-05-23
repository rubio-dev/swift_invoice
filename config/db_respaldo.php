<?php
// Clase para manejar la conexión a la base de datos del sistema
class Database {
    // Datos de conexión (pueden cambiarse según ambiente)
    private $host = 'localhost';            // Host del servidor de base de datos (generalmente localhost)
    private $port = '3306';                 // Puerto MySQL, por defecto 3306 (usado en XAMPP/MAMP)
    private $db_name = 'swift_invoice_db';  // Nombre de la base de datos
    private $username = 'root';             // Usuario de la base de datos
    private $password = '';                 // Contraseña del usuario (vacío en instalaciones locales por defecto)
    private $conn;                          // Variable para almacenar la conexión PDO

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
            // Configura para lanzar excepciones ante errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Muestra el error en pantalla (solo para desarrollo)
            echo "Connection error: " . $e->getMessage();
        }

        // Devuelve el objeto de conexión PDO (o null si falló)
        return $this->conn;
    }
}
?>
