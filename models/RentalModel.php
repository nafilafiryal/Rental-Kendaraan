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
            ORDER BY 
                CASE WHEN status = 'booking' THEN 1 
                     WHEN status = 'berjalan' THEN 2 
                     ELSE 3 
                END ASC,
                tgl_sewa ASC
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
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM view_rental_lengkap $where");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM view_rental_lengkap WHERE id_rental = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // LOGIC STATUS OTOMATIS
            $today = date('Y-m-d');
            $tgl_sewa = date('Y-m-d', strtotime($data['tgl_sewa']));
            
            // Jika sewa di masa depan, status 'booking'. Jika hari ini/lewat, status 'berjalan'
            $status_rental = ($tgl_sewa > $today) ? 'booking' : 'berjalan';
            
            $stmt = $this->db->prepare("
                INSERT INTO rental (id_kendaraan, id_pelanggan, id_sopir, tgl_sewa, tgl_kembali, total_harga, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                RETURNING id_rental
            ");
            $stmt->execute([
                $data['id_kendaraan'],
                $data['id_pelanggan'],
                $data['id_sopir'],
                $data['tgl_sewa'],
                $data['tgl_kembali'],
                $data['total_harga'],
                $status_rental // Status dinamis
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_rental = $result['id_rental'];
            
            // Mobil tetap kita set 'disewa' agar tidak bisa dibooking orang lain
            // (Meskipun status rentalnya masih 'booking')
            $stmt = $this->db->prepare("UPDATE kendaraan SET status = 'disewa' WHERE id_kendaraan = ?");
            $stmt->execute([$data['id_kendaraan']]);
            
            $this->db->commit();
            return $id_rental;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // FITUR BARU: Mengaktifkan Booking (Saat mobil diambil)
    public function activateBooking($id_rental) {
        $stmt = $this->db->prepare("UPDATE rental SET status = 'berjalan' WHERE id_rental = ?");
        return $stmt->execute([$id_rental]);
    }

    public function update($id, $data) { /* ... (sama seperti sebelumnya) ... */ }
    
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("SELECT id_kendaraan FROM rental WHERE id_rental = ?");
            $stmt->execute([$id]);
            $rental = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$rental) throw new Exception("Rental tidak ditemukan");
            
            $stmt = $this->db->prepare("DELETE FROM rental WHERE id_rental = ?");
            $stmt->execute([$id]);
            
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
        // Hitung berjalan + booking
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM rental WHERE status IN ('berjalan', 'booking')");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getPendapatanBulanIni() {
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM rental WHERE DATE_TRUNC('month', tgl_sewa) = DATE_TRUNC('month', CURRENT_DATE) AND status = 'selesai'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function getRentalTerbaru($limit = 5) {
        $stmt = $this->db->query("SELECT * FROM view_rental_lengkap ORDER BY tgl_sewa DESC LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function hitungTotalHarga($id_kendaraan, $tgl_sewa, $tgl_kembali) {
        $stmt = $this->db->prepare("SELECT harga_sewa FROM kendaraan WHERE id_kendaraan = ?");
        $stmt->execute([$id_kendaraan]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $harga_per_hari = $result['harga_sewa'] ?? 300000; 
        
        $date1 = new DateTime($tgl_sewa);
        $date2 = new DateTime($tgl_kembali);
        $diff = $date1->diff($date2);
        $jumlah_hari = $diff->days;
        if ($jumlah_hari == 0) $jumlah_hari = 1;
        
        return $harga_per_hari * $jumlah_hari;
    }
}
?>