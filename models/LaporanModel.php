<?php
require_once 'config/database.php';

class LaporanModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    
    public function getKendaraanPopuler($bulan = null, $tahun = null) {
        $where = "";
        if ($bulan && $tahun) {
            $where = "WHERE EXTRACT(MONTH FROM r.tgl_sewa) = $bulan AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        } elseif ($tahun) {
            $where = "WHERE EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        }

        
        $sql = "
            SELECT 
                k.id_kendaraan, 
                k.no_plat, 
                k.merk, 
                k.tahun,
                t.nama_tipe,
                COUNT(r.id_rental) as jumlah_rental,
                COALESCE(SUM(r.total_harga), 0) as total_pendapatan
            FROM kendaraan k
            LEFT JOIN tipe_kendaraan t ON k.id_tipe = t.id_tipe
            LEFT JOIN rental r ON k.id_kendaraan = r.id_kendaraan
            $where
            GROUP BY k.id_kendaraan, k.no_plat, k.merk, k.tahun, t.nama_tipe
            ORDER BY jumlah_rental DESC, total_pendapatan DESC
        ";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getPendapatanRental($tgl_awal = null, $tgl_akhir = null) {
        $where = "";
        $params = [];
        
        if ($tgl_awal && $tgl_akhir) {
            $where = "WHERE tgl_sewa BETWEEN ? AND ?";
            $params = [$tgl_awal, $tgl_akhir];
        }
        
        $sql = "
            SELECT 
                tgl_sewa as tanggal,
                COUNT(*) as jumlah_transaksi,
                COALESCE(SUM(total_harga), 0) as total_pendapatan
            FROM rental
            $where
            GROUP BY tgl_sewa
            ORDER BY tgl_sewa DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getUtilisasiKendaraan() {
        
        $sql = "
            SELECT 
                k.no_plat, 
                k.merk, 
                k.tahun, 
                k.status,
                COUNT(r.id_rental) as total_rental,
                COALESCE(SUM(DATE_PART('day', r.tgl_kembali::timestamp - r.tgl_sewa::timestamp)), 0) as total_hari_disewa,
                CASE 
                    WHEN COUNT(r.id_rental) > 0 THEN 
                        ROUND((COALESCE(SUM(DATE_PART('day', r.tgl_kembali::timestamp - r.tgl_sewa::timestamp)), 0) / 365.0 * 100)::numeric, 1)
                    ELSE 0 
                END as persentase_utilisasi
            FROM kendaraan k
            LEFT JOIN rental r ON k.id_kendaraan = r.id_kendaraan
            GROUP BY k.id_kendaraan, k.no_plat, k.merk, k.tahun, k.status
            ORDER BY total_hari_disewa DESC
        ";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getPelangganAktif() {
        $sql = "
            SELECT 
                p.nama, 
                p.no_hp, 
                p.alamat,
                COUNT(r.id_rental) as jumlah_rental,
                COALESCE(SUM(r.total_harga), 0) as total_pengeluaran,
                MAX(r.tgl_sewa) as terakhir_sewa
            FROM pelanggan p
            JOIN rental r ON p.id_pelanggan = r.id_pelanggan
            GROUP BY p.id_pelanggan, p.nama, p.no_hp, p.alamat
            ORDER BY jumlah_rental DESC
        ";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getLaporanPengembalian($bulan = null, $tahun = null) {
        
        $sql = "
            SELECT 
                p.tanggal_kembali as tgl_pengembalian,
                k.merk,
                k.no_plat,
                pl.nama as nama_pelanggan,
                p.kondisi,
                p.denda,
                p.keterangan
            FROM pengembalian p
            JOIN rental r ON p.id_rental = r.id_rental
            JOIN kendaraan k ON r.id_kendaraan = k.id_kendaraan
            JOIN pelanggan pl ON r.id_pelanggan = pl.id_pelanggan
            ORDER BY p.tanggal_kembali DESC
        ";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function refreshMaterializedView($view_name) {
        
        return $this->db->query("REFRESH MATERIALIZED VIEW $view_name");
    }
    
    public function getFromMaterializedView($view_name) {
        
        try {
            return $this->db->query("SELECT * FROM $view_name")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return []; 
        }
    }
}
?>