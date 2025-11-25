<?php
require_once 'config/database.php';

class PengembalianModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function getRentalAktif($search = '') {
        $where = "WHERE r.status = 'berjalan'";
        $params = [];
        if ($search) {
            $where .= " AND (k.no_plat ILIKE ? OR p.nama ILIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        $stmt = $this->db->prepare("
            SELECT r.*, k.no_plat, k.merk, p.nama as nama_pelanggan, p.no_hp 
            FROM rental r 
            JOIN kendaraan k ON r.id_kendaraan = k.id_kendaraan 
            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
            $where 
            ORDER BY r.tgl_sewa DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function prosesPengembalian($id_rental, $data) {
        try {
            $this->db->beginTransaction();
            
            
            $stmt = $this->db->prepare("
                INSERT INTO pengembalian (id_rental, tanggal_kembali, kondisi, denda, keterangan) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $id_rental, 
                $data['tgl_pengembalian'], 
                $data['kondisi'], 
                $data['denda'], 
                $data['keterangan']
            ]);
            
            
            $stmt = $this->db->prepare("UPDATE rental SET status = 'selesai' WHERE id_rental = ?");
            $stmt->execute([$id_rental]);
            
            
            $stmt = $this->db->prepare("SELECT id_kendaraan FROM rental WHERE id_rental = ?");
            $stmt->execute([$id_rental]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                
                $status_baru = ($data['kondisi'] == 'baik') ? 'tersedia' : 'perbaikan';
                $stmt = $this->db->prepare("CALL update_status_kendaraan(?, ?)");
                $stmt->execute([$row['id_kendaraan'], $status_baru]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function hitungDenda($id_rental, $tgl_pengembalian) {

        $stmt = $this->db->prepare("
            SELECT hitung_denda(
                GREATEST(
                    DATE_PART('day', ?::timestamp - tgl_kembali::timestamp)::integer, 
                    0
                )
            ) as denda_total 
            FROM rental 
            WHERE id_rental = ?
        ");
        
        $stmt->execute([$tgl_pengembalian, $id_rental]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['denda_total'] ?? 0;
    }
    
    public function getAll($limit = 10, $offset = 0) {
        
        $stmt = $this->db->query("
            SELECT 
                p.id_pengembalian,
                p.id_rental,
                p.tanggal_kembali as tgl_pengembalian, 
                p.kondisi,
                p.denda,
                p.keterangan,
                k.no_plat, 
                k.merk, 
                pl.nama as nama_pelanggan
            FROM pengembalian p 
            JOIN rental r ON p.id_rental = r.id_rental 
            JOIN kendaraan k ON r.id_kendaraan = k.id_kendaraan
            JOIN pelanggan pl ON r.id_pelanggan = pl.id_pelanggan
            ORDER BY p.tanggal_kembali DESC 
            LIMIT $limit OFFSET $offset
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function count() {
        return $this->db->query("SELECT COUNT(*) FROM pengembalian")->fetchColumn();
    }
}
?>