<?php
require_once 'models/KendaraanModel.php';
require_once 'models/PelangganModel.php';
require_once 'models/RentalModel.php';

class DashboardController {
    private $kendaraanModel;
    private $pelangganModel;
    private $rentalModel;
    
    public function __construct() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        
        $this->kendaraanModel = new KendaraanModel();
        $this->pelangganModel = new PelangganModel();
        $this->rentalModel = new RentalModel();
    }
    
    public function index() {
        $data = [
            'total_kendaraan' => $this->kendaraanModel->getTotalKendaraan(),
            'kendaraan_tersedia' => $this->kendaraanModel->getKendaraanTersedia(),
            'kendaraan_disewa' => $this->kendaraanModel->getKendaraanDisewa(),
            'total_pelanggan' => $this->pelangganModel->getTotalPelanggan(),
            'rental_berjalan' => $this->rentalModel->getRentalBerjalan(),
            'pendapatan_bulan_ini' => $this->rentalModel->getPendapatanBulanIni(),
            'rental_terbaru' => $this->rentalModel->getRentalTerbaru(5),
            'kendaraan_populer' => $this->kendaraanModel->getKendaraanPopuler(5)
        ];
        
        require_once 'views/dashboard/index.php';
    }
}
?>