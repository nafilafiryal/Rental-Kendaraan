<?php
require_once 'config/database.php';

class RentalModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getAll($search = '', $limit = 10, $offset = 0) {
        $where = '';
        $params = [];
        
        if ($search) {
            $where = "WHERE no_plat ILIKE ? OR nama_pelanggan ILIKE ?";
            $params = ["%$search%", "%$search%"];
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM view_rental_lengkap 
            $where
            ORDER BY tgl_sewa DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function count($search = '') {
        $where = '';
        $params = [];
        
        if ($search) {
            $where = "WHERE no_plat ILIKE ? OR nama_pelanggan ILIKE ?";
            $params = ["%$search%", "%$search%"];
        }
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM view_rental_lengkap
            $where
        ");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM view_rental_lengkap
            WHERE id_rental = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            
            $stmt = $this->db->prepare("
                INSERT INTO rental (id_kendaraan, id_pelanggan, id_sopir, tgl_sewa, tgl_kembali, total_harga, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'berjalan')
                RETURNING id_rental
            ");
            $stmt->execute([
                $data['id_kendaraan'],
                $data['id_pelanggan'],
                $data['id_sopir'], 
                $data['tgl_sewa'],
                $data['tgl_kembali'],
                $data['total_harga']
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_rental = $result['id_rental'];
            
            
            $stmt = $this->db->prepare("UPDATE kendaraan SET status = 'disewa' WHERE id_kendaraan = ?");
            $stmt->execute([$data['id_kendaraan']]);
            
            $this->db->commit();
            return $id_rental;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE rental 
            SET id_kendaraan=?, id_pelanggan=?, tgl_sewa=?, tgl_kembali=?, total_harga=?
            WHERE id_rental=?
        ");
        return $stmt->execute([
            $data['id_kendaraan'],
            $data['id_pelanggan'],
            $data['tgl_sewa'],
            $data['tgl_kembali'],
            $data['total_harga'],
            $id
        ]);
    }
    
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Get kendaraan id
            $stmt = $this->db->prepare("SELECT id_kendaraan FROM rental WHERE id_rental = ?");
            $stmt->execute([$id]);
            $rental = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$rental) {
                throw new Exception("Rental tidak ditemukan");
            }
            
            // Delete rental
            $stmt = $this->db->prepare("DELETE FROM rental WHERE id_rental = ?");
            $stmt->execute([$id]);
            
            // Update status kendaraan kembali tersedia
            $stmt = $this->db->prepare("UPDATE kendaraan SET status = 'tersedia' WHERE id_kendaraan = ?");
            $stmt->execute([$rental['id_kendaraan']]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function getRentalBerjalan() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM rental WHERE status = 'berjalan'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getPendapatanBulanIni() {
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_harga), 0) as total 
            FROM rental 
            WHERE DATE_TRUNC('month', tgl_sewa) = DATE_TRUNC('month', CURRENT_DATE)
              AND status = 'selesai'
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getRentalTerbaru($limit = 5) {
        $stmt = $this->db->query("
            SELECT *
            FROM view_rental_lengkap
            ORDER BY tgl_sewa DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function hitungTotalHarga($id_kendaraan, $tgl_sewa, $tgl_kembali) {
        // Get harga sewa per hari dari tipe kendaraan
        $stmt = $this->db->prepare("
            SELECT t.harga_sewa_per_hari 
            FROM kendaraan k
            JOIN tipe_kendaraan t ON k.id_tipe = t.id_tipe
            WHERE k.id_kendaraan = ?
        ");
        $stmt->execute([$id_kendaraan]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $harga_per_hari = $result['harga_sewa_per_hari'] ?? 0;
        
        // Hitung jumlah hari
        $date1 = new DateTime($tgl_sewa);
        $date2 = new DateTime($tgl_kembali);
        $interval = $date1->diff($date2);
        $jumlah_hari = $interval->days > 0 ? $interval->days : 1;
        
        return $harga_per_hari * $jumlah_hari;
    }
}
?>