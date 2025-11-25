<?php
require_once 'config/database.php';

class SopirModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getAll() {
        
        $stmt = $this->db->query("SELECT * FROM sopir ORDER BY nama ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSopirAvailable() {
        
        return $this->getAll();
    }
}
?>