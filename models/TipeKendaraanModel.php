<?php
require_once 'config/database.php';

class TipeKendaraanModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM tipe_kendaraan ORDER BY nama_tipe");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>