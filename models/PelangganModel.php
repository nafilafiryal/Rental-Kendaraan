<?php
require_once 'config/database.php';

class PelangganModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getAll($search = '', $limit = 10, $offset = 0) {
        $where = '';
        $params = [];
        
        if ($search) {
            $where = "WHERE nama ILIKE ? OR no_ktp ILIKE ? OR no_hp ILIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM pelanggan 
            $where 
            ORDER BY id_pelanggan DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function count($search = '') {
        $where = '';
        $params = [];
        
        if ($search) {
            $where = "WHERE nama ILIKE ? OR no_ktp ILIKE ? OR no_hp ILIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pelanggan $where");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE pelanggan 
            SET nama=?, alamat=?, no_hp=?, no_ktp=?, email=? 
            WHERE id_pelanggan=?
        ");
        return $stmt->execute([
            $data['nama'], 
            $data['alamat'], 
            $data['no_hp'], 
            $data['no_ktp'],
            $data['email'], 
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
        return $stmt->execute([$id]);
    }
    
    public function getTotalPelanggan() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM pelanggan");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function checkKTPExists($no_ktp, $exclude_id = null) {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM pelanggan WHERE no_ktp = ? AND id_pelanggan != ?");
            $stmt->execute([$no_ktp, $exclude_id]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM pelanggan WHERE no_ktp = ?");
            $stmt->execute([$no_ktp]);
        }
        return $stmt->fetchColumn() > 0;
    }
}
?>