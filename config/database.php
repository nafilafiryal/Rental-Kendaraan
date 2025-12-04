<?php
//coba
class Database {
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'rental_kendaraan1';
    private $username = 'postgres';
    private $password = 'password123';
    private $pdo;
    
    public function connect() {
        if ($this->pdo == null) {
            try {
                $this->pdo = new PDO(
                    "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}", 
                    $this->username, 
                    $this->password
                );
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }
}
?>