<?php
require_once 'models/LaporanModel.php';

class LaporanController {
    private $laporanModel;
    
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        $this->laporanModel = new LaporanModel();
    }
    
    public function index() {
        if (isset($_GET['refresh_mv'])) {
            try {
                $this->laporanModel->refreshMaterializedView('mv_kendaraan_populer');
                header("Location: index.php?page=laporan&jenis=materialized_view&success=refresh");
            } catch (Exception $e) {
                header("Location: index.php?page=laporan&jenis=materialized_view&error=refresh");
            }
            exit();
        }
        
        $jenis_laporan = isset($_GET['jenis']) ? $_GET['jenis'] : 'kendaraan_populer';
        
        // Ambil Filter dari URL
        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : null;
        $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : null;
        $tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : null;
        
        $data_laporan = [];
        
        // PERBAIKAN: Kirim $bulan dan $tahun ke semua fungsi yang butuh filter
        switch ($jenis_laporan) {
            case 'kendaraan_populer':
                $data_laporan = $this->laporanModel->getKendaraanPopuler($bulan, $tahun);
                break;
            case 'pendapatan':
                $data_laporan = $this->laporanModel->getPendapatanRental($tgl_awal, $tgl_akhir);
                break;
            case 'utilisasi':
                // Update: Kirim parameter filter
                $data_laporan = $this->laporanModel->getUtilisasiKendaraan($bulan, $tahun);
                break;
            case 'pelanggan':
                // Update: Kirim parameter filter
                $data_laporan = $this->laporanModel->getPelangganAktif($bulan, $tahun);
                break;
            case 'pengembalian':
                $data_laporan = $this->laporanModel->getLaporanPengembalian($bulan, $tahun);
                break;
            case 'materialized_view':
                $data_laporan = $this->laporanModel->getFromMaterializedView('mv_kendaraan_populer');
                break;
        }
        
        require_once 'views/laporan/index.php';
    }
    
    public function export() {
        $jenis_laporan = isset($_GET['jenis']) ? $_GET['jenis'] : 'kendaraan_populer';
        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : null;
        $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        
        $data = [];
        switch ($jenis_laporan) {
            case 'kendaraan_populer':
                $data = $this->laporanModel->getKendaraanPopuler($bulan, $tahun);
                break;
            case 'pendapatan':
                $data = $this->laporanModel->getPendapatanRental($_GET['tgl_awal'] ?? null, $_GET['tgl_akhir'] ?? null);
                break;
            case 'utilisasi':
                $data = $this->laporanModel->getUtilisasiKendaraan($bulan, $tahun);
                break;
            case 'pelanggan':
                $data = $this->laporanModel->getPelangganAktif($bulan, $tahun);
                break;
            case 'pengembalian':
                $data = $this->laporanModel->getLaporanPengembalian($bulan, $tahun);
                break;
            case 'materialized_view':
                $data = $this->laporanModel->getFromMaterializedView('mv_kendaraan_populer');
                break;
        }
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=laporan_' . $jenis_laporan . '_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit();
    }
}
?>