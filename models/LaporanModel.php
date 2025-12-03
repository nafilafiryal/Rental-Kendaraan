<?php
require_once 'config/database.php';

class LaporanModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    // 1. Laporan Kendaraan Populer
    public function getKendaraanPopuler($bulan = null, $tahun = null) {
        $filter = "";
        if ($bulan && $tahun) {
            $filter = "WHERE EXTRACT(MONTH FROM r.tgl_sewa) = $bulan AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        } elseif ($tahun) {
            $filter = "WHERE EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        }

        $sql = "SELECT k.id_kendaraan, k.no_plat, k.merk, k.tahun, t.nama_tipe, ";
        $sql .= "COUNT(r.id_rental) as jumlah_rental, ";
        $sql .= "COALESCE(SUM(r.total_harga), 0) as total_pendapatan ";
        $sql .= "FROM kendaraan AS k ";
        $sql .= "LEFT JOIN tipe_kendaraan AS t ON k.id_tipe = t.id_tipe ";
        $sql .= "LEFT JOIN rental AS r ON k.id_kendaraan = r.id_kendaraan ";
        $sql .= $filter . " ";
        $sql .= "GROUP BY k.id_kendaraan, k.no_plat, k.merk, k.tahun, t.nama_tipe ";
        $sql .= "ORDER BY jumlah_rental DESC, total_pendapatan DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 2. Laporan Pendapatan
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
    
    // 3. Laporan Utilisasi
    public function getUtilisasiKendaraan($bulan = null, $tahun = null) {
        $filter_join = "";
        
        if ($bulan && $tahun) {
            $filter_join = " AND EXTRACT(MONTH FROM r.tgl_sewa) = $bulan AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        } elseif ($tahun) {
            $filter_join = " AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        }

        $pembagi = ($bulan) ? 30.0 : 365.0;
        $str_pembagi = number_format($pembagi, 1, '.', '');

        $sql = "SELECT k.id_kendaraan, k.no_plat, k.merk, k.tahun, k.status, ";
        $sql .= "COUNT(r.id_rental) as total_rental, ";
        $sql .= "COALESCE(SUM(DATE_PART('day', r.tgl_kembali::timestamp - r.tgl_sewa::timestamp)), 0) as total_hari_disewa, ";
        $sql .= "ROUND((COALESCE(SUM(DATE_PART('day', r.tgl_kembali::timestamp - r.tgl_sewa::timestamp)), 0) / " . $str_pembagi . " * 100)::numeric, 1) as persentase_utilisasi ";
        $sql .= "FROM kendaraan AS k ";
        $sql .= "LEFT JOIN rental AS r ON k.id_kendaraan = r.id_kendaraan " . $filter_join . " ";
        $sql .= "GROUP BY k.id_kendaraan, k.no_plat, k.merk, k.tahun, k.status ";
        $sql .= "ORDER BY total_hari_disewa DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 4. Laporan Pelanggan Aktif (REVISI: MENAMBAHKAN FILTER)
    public function getPelangganAktif($bulan = null, $tahun = null) {
        $filter_join = "";
        
        // Logika Filter Waktu untuk Pelanggan
        if ($bulan && $tahun) {
            $filter_join = " AND EXTRACT(MONTH FROM r.tgl_sewa) = $bulan AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        } elseif ($tahun) {
            $filter_join = " AND EXTRACT(YEAR FROM r.tgl_sewa) = $tahun";
        }

        $sql = "SELECT p.id_pelanggan, p.nama, p.no_hp, p.alamat, ";
        $sql .= "COUNT(r.id_rental) as jumlah_rental, ";
        $sql .= "COALESCE(SUM(r.total_harga), 0) as total_pengeluaran, ";
        $sql .= "MAX(r.tgl_sewa) as terakhir_sewa ";
        $sql .= "FROM pelanggan AS p ";
        // Gunakan INNER JOIN + Filter agar hanya menampilkan pelanggan yang aktif di periode tersebut
        $sql .= "JOIN rental AS r ON p.id_pelanggan = r.id_pelanggan " . $filter_join . " ";
        $sql .= "GROUP BY p.id_pelanggan, p.nama, p.no_hp, p.alamat ";
        $sql .= "ORDER BY jumlah_rental DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 5. Laporan Pengembalian
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
        $sql .= "FROM pengembalian AS p ";
        $sql .= "JOIN rental AS r ON p.id_rental = r.id_rental ";
        $sql .= "JOIN kendaraan AS k ON r.id_kendaraan = k.id_kendaraan ";
        $sql .= "JOIN pelanggan AS pl ON r.id_pelanggan = pl.id_pelanggan ";
        $sql .= $where . " ";
        $sql .= "ORDER BY p.tanggal_kembali DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 6. Materialized View Management
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