<?php
require_once 'config/database.php';

class KendaraanModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getAll($search = '', $limit = 10, $offset = 0) {
        $where = '';
        $params = [];
        
        if ($search) {
            $where = "WHERE k.no_plat ILIKE ? OR k.merk ILIKE ?";
            $params = ["%$search%", "%$search%"];
        }
        
        $stmt = $this->db->prepare("
            SELECT k.*, t.nama_tipe 
            FROM kendaraan k 
            LEFT JOIN tipe_kendaraan t ON k.id_tipe = t.id_tipe 
            $where 
            ORDER BY k.id_kendaraan DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function count($search = '') {
        $where = '';
        $params = [];
        
        if ($search) {
            $where = "WHERE no_plat ILIKE ? OR merk ILIKE ?";
            $params = ["%$search%", "%$search%"];
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM kendaraan $where");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM kendaraan WHERE id_kendaraan = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO kendaraan (no_plat, merk, tahun, id_tipe, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['no_plat'], 
            $data['merk'], 
            $data['tahun'], 
            $data['id_tipe'], 
            $data['status']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE kendaraan 
            SET no_plat=?, merk=?, tahun=?, id_tipe=?, status=? 
            WHERE id_kendaraan=?
        ");
        return $stmt->execute([
            $data['no_plat'], 
            $data['merk'], 
            $data['tahun'], 
            $data['id_tipe'], 
            $data['status'], 
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM kendaraan WHERE id_kendaraan = ?");
        return $stmt->execute([$id]);
    }
    

    
    public function getTotalKendaraan() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM kendaraan");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getKendaraanTersedia() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM kendaraan WHERE status = 'tersedia'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getKendaraanDisewa() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM kendaraan WHERE status = 'disewa'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getKendaraanPopuler($limit = 5) {
        
        $stmt = $this->db->query("
            SELECT k.merk, k.no_plat, k.tahun, 
                   COUNT(r.id_rental) as jumlah_rental
            FROM kendaraan k
            LEFT JOIN rental r ON k.id_kendaraan = r.id_kendaraan
            GROUP BY k.id_kendaraan, k.merk, k.no_plat, k.tahun
            ORDER BY jumlah_rental DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKendaraanPerbaikan() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM kendaraan WHERE status = 'perbaikan'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>