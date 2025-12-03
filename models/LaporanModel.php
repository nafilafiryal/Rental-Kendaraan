<?php
require_once 'config/database.php';

class LaporanModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    // 1. Laporan Kendaraan Populer (Tab 1)
    public function getKendaraanPopuler($bulan = null, $tahun = null) {
        $where = "";
        // Filter WHERE diletakkan di JOIN atau WHERE tergantung kebutuhan
        // Di sini kita pakai WHERE karena ingin memfilter hasil akhir
        if ($bulan && $tahun) {
            $where = "WHERE EXTRACT(MONTH FROM r.tgl_sewa) = $bulan AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        } elseif ($tahun) {
            $where = "WHERE EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        }

        $sql = "SELECT k.id_kendaraan, k.no_plat, k.merk, k.tahun, t.nama_tipe, ";
        $sql .= "COUNT(r.id_rental) as jumlah_rental, ";
        $sql .= "COALESCE(SUM(r.total_harga), 0) as total_pendapatan ";
        $sql .= "FROM kendaraan k ";
        $sql .= "LEFT JOIN tipe_kendaraan t ON k.id_tipe = t.id_tipe ";
        $sql .= "LEFT JOIN rental r ON k.id_kendaraan = r.id_kendaraan ";
        $sql .= $where . " ";
        $sql .= "GROUP BY k.id_kendaraan, k.no_plat, k.merk, k.tahun, t.nama_tipe ";
        $sql .= "ORDER BY jumlah_rental DESC, total_pendapatan DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 2. Laporan Pendapatan (Tab 2)
    public function getPendapatanRental($tgl_awal = null, $tgl_akhir = null) {
        $where = "";
        $params = [];
        
        if ($tgl_awal && $tgl_akhir) {
            $where = "WHERE tgl_sewa BETWEEN ? AND ?";
            $params = [$tgl_awal, $tgl_akhir];
        }
        
        $sql = "SELECT tgl_sewa as tanggal, COUNT(*) as jumlah_transaksi, ";
        $sql .= "COALESCE(SUM(total_harga), 0) as total_pendapatan ";
        $sql .= "FROM rental ";
        $sql .= $where . " ";
        $sql .= "GROUP BY tgl_sewa ORDER BY tgl_sewa DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 3. Laporan Utilisasi (FIXED & OPTIMIZED)
    public function getUtilisasiKendaraan($bulan = null, $tahun = null) {
        $join_condition = "";
        
        // Filter diletakkan di dalam ON (JOIN) agar kendaraan yang TIDAK disewa 
        // tetap muncul di list (dengan nilai 0), bukan hilang.
        if ($bulan && $tahun) {
            $join_condition = " AND EXTRACT(MONTH FROM r.tgl_sewa) = $bulan AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        } elseif ($tahun) {
            $join_condition = " AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        }

        // Logic Pembagi: Jika filter bulan, bagi 30 hari. Jika tahun, bagi 365 hari.
        $pembagi = ($bulan) ? 30.0 : 365.0;
        $str_pembagi = number_format($pembagi, 1, '.', '');

        $sql = "SELECT k.id_kendaraan, k.no_plat, k.merk, k.tahun, k.status, ";
        $sql .= "COUNT(r.id_rental) as total_rental, ";
        $sql .= "COALESCE(SUM(DATE_PART('day', r.tgl_kembali::timestamp - r.tgl_sewa::timestamp)), 0) as total_hari_disewa, ";
        
        // Rumus Persentase: (Total Hari Sewa / Pembagi) * 100
        $sql .= "ROUND((COALESCE(SUM(DATE_PART('day', r.tgl_kembali::timestamp - r.tgl_sewa::timestamp)), 0) / " . $str_pembagi . " * 100)::numeric, 1) as persentase_utilisasi ";
        
        $sql .= "FROM kendaraan k ";
        $sql .= "LEFT JOIN rental r ON k.id_kendaraan = r.id_kendaraan " . $join_condition . " ";
        
        $sql .= "GROUP BY k.id_kendaraan, k.no_plat, k.merk, k.tahun, k.status ";
        $sql .= "ORDER BY total_hari_disewa DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 4. Laporan Pelanggan Aktif (Tab 4)
    public function getPelangganAktif() {
        $sql = "SELECT p.id_pelanggan, p.nama, p.no_hp, p.alamat, ";
        $sql .= "COUNT(r.id_rental) as jumlah_rental, ";
        $sql .= "COALESCE(SUM(r.total_harga), 0) as total_pengeluaran, ";
        $sql .= "MAX(r.tgl_sewa) as terakhir_sewa ";
        $sql .= "FROM pelanggan p ";
        $sql .= "JOIN rental r ON p.id_pelanggan = r.id_pelanggan ";
        $sql .= "GROUP BY p.id_pelanggan, p.nama, p.no_hp, p.alamat ";
        $sql .= "ORDER BY jumlah_rental DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 5. Laporan Pengembalian (Tab 5)
    public function getLaporanPengembalian($bulan = null, $tahun = null) {
        $where = "";
        if ($bulan && $tahun) {
            $where = "WHERE EXTRACT(MONTH FROM p.tanggal_kembali) = $bulan AND EXTRACT(YEAR FROM p.tanggal_kembali) = $tahun";
        } elseif ($tahun) {
            $where = "WHERE EXTRACT(YEAR FROM p.tanggal_kembali) = $tahun";
        }

        $sql = "SELECT p.tanggal_kembali as tgl_pengembalian, ";
        $sql .= "k.merk, k.no_plat, pl.nama as nama_pelanggan, ";
        $sql .= "p.kondisi, p.denda, p.keterangan ";
        $sql .= "FROM pengembalian p ";
        $sql .= "JOIN rental r ON p.id_rental = r.id_rental ";
        $sql .= "JOIN kendaraan k ON r.id_kendaraan = k.id_kendaraan ";
        $sql .= "JOIN pelanggan pl ON r.id_pelanggan = pl.id_pelanggan ";
        $sql .= $where . " ";
        $sql .= "ORDER BY p.tanggal_kembali DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 6. Materialized View Management (Tab 6)
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