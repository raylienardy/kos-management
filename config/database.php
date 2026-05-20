<?php
class Database {
    private $host = "localhost";
    private $db_name = "db_kos";
    private $username = "root";
    private $password = "";          // sesuaikan jika ada password
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Koneksi gagal: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>