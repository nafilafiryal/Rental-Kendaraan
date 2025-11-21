<?php
require_once 'config/database.php';

class RentalModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
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
            SELECT r.*, k.merk, k.no_plat, p.nama as nama_pelanggan
            FROM rental r
            JOIN kendaraan k ON r.id_kendaraan = k.id_kendaraan
            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan
            ORDER BY r.tgl_sewa DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>