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
                header("Location: index.php?page=laporan&success=refresh");
            } catch (Exception $e) {
                header("Location: index.php?page=laporan&error=refresh");
            }
            exit();
        }
        
        $jenis_laporan = isset($_GET['jenis']) ? $_GET['jenis'] : 'kendaraan_populer';
        $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : null;
        $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : null;
        $tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : null;
        
        $data_laporan = [];
        switch ($jenis_laporan) {
            case 'kendaraan_populer':
                $data_laporan = $this->laporanModel->getKendaraanPopuler($bulan, $tahun);
                break;
            case 'pendapatan':
                $data_laporan = $this->laporanModel->getPendapatanRental($tgl_awal, $tgl_akhir);
                break;
            case 'utilisasi':
                $data_laporan = $this->laporanModel->getUtilisasiKendaraan();
                break;
            case 'pelanggan':
                $data_laporan = $this->laporanModel->getPelangganAktif();
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
                $data = $this->laporanModel->getPendapatanRental($_GET['tgl_awal'], $_GET['tgl_akhir']);
                break;
            case 'utilisasi':
                $data = $this->laporanModel->getUtilisasiKendaraan();
                break;
            case 'pelanggan':
                $data = $this->laporanModel->getPelangganAktif();
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
?>'
    ];
    
    foreach ($files as $file) {
        if (isset($allFiles[$file])) {
            generateFile($file, $allFiles[$file]);
            $generated[] = $file;
        }
    }
    
    echo "<h2>✅ File berhasil dibuat:</h2><ul>";
    foreach ($generated as $f) {
        echo "<li>$f</li>";
    }
    echo "</ul>";
    echo "<p><a href='index.php'>Buka Aplikasi</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Download All Files</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .file-item { padding: 10px; margin: 5px 0; background: #f9f9f9; border-left: 4px solid #4CAF50; }
        button { background: #4CAF50; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #45a049; }
        .warning { background: #FFF3CD; border: 1px solid #FFC107; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📥 Download All Files Generator</h1>
        
        <div class="warning">
            <strong>⚠️ Perhatian:</strong>
            <p>File ini akan membuat controller yang masih PERLU DILENGKAPI dengan code dari model dan view.</p>
            <p>Setelah generate, Anda harus copy code lengkap dari artifacts untuk:</p>
            <ul>
                <li>Semua Models (7 files)</li>
                <li>Semua Views (8 files)</li>
                <li>Assets CSS & JS</li>
                <li>Database schema</li>
            </ul>
        </div>
        
        <form method="POST">
            <h3>Pilih file yang akan dibuat:</h3>
            
            <label><input type="checkbox" name="files[]" value="controllers/RentalController.php" checked> RentalController.php</label><br>
            <label><input type="checkbox" name="files[]" value="controllers/PengembalianController.php" checked> PengembalianController.php</label><br>
            <label><input type="checkbox" name="files[]" value="controllers/LaporanController.php" checked> LaporanController.php</label><br>
            
            <br><br>
            <button type="submit" name="generate">✨ Generate Files</button>
        </form>
        
        <hr>
        <p><strong>Atau gunakan QUICK_FIX.php untuk setup lengkap!</strong></p>
    </div>
</body>
</html>