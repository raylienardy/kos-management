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
// ====== Tambahan: deteksi BASE_URL otomatis ======
// Dapatkan path absolut folder proyek (root, bukan config)
$projectRoot = dirname(__FILE__);         // .../kos-management/config
$projectRoot = dirname($projectRoot);     // .../kos-management
$projectRoot = realpath($projectRoot);    // normalisasi path

$docRoot = realpath($_SERVER['DOCUMENT_ROOT']);

// Hitung path relatif dari document root ke proyek
if (strpos($projectRoot, $docRoot) === 0) {
    $relativePath = substr($projectRoot, strlen($docRoot));
} else {
    // Fallback jika di luar document root (jarang)
    $relativePath = '/kos-management';   // ganti manual jika perlu
}
$relativePath = str_replace('\\', '/', $relativePath);
$relativePath = '/' . trim($relativePath, '/');

define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . $relativePath . '/');
?>