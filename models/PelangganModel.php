<?php
require_once 'config/database.php';

class PelangganModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getTotalPelanggan() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM pelanggan");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>