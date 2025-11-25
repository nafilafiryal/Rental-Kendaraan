<?php
require_once 'models/RentalModel.php';
require_once 'models/KendaraanModel.php';
require_once 'models/PelangganModel.php';

class RentalController {
    private $rentalModel;
    private $kendaraanModel;
    private $pelangganModel;
    
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        $this->rentalModel = new RentalModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->pelangganModel = new PelangganModel();
    }
    
    public function index() {
        if (isset($_GET['delete'])) {
            try {
                $this->rentalModel->delete($_GET['delete']);
                header("Location: index.php?page=rental&success=delete");
            } catch (Exception $e) {
                header("Location: index.php?page=rental&error=delete");
            }
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id_kendaraan' => $_POST['id_kendaraan'],
                'id_pelanggan' => $_POST['id_pelanggan'],
                'tgl_sewa' => $_POST['tgl_sewa'],
                'tgl_kembali' => $_POST['tgl_kembali'],
                'total_harga' => $_POST['total_harga']
            ];
            
            try {
                $this->rentalModel->create($data);
                header("Location: index.php?page=rental&success=add");
            } catch (Exception $e) {
                $error_message = "Gagal: " . $e->getMessage();
            }
            exit();
        }
        
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $total_records = $this->rentalModel->count($search);
        $total_pages = ceil($total_records / $per_page);
        $rental_list = $this->rentalModel->getAll($search, $per_page, $offset);
        
        $kendaraan_list = $this->kendaraanModel->getAll('', 1000, 0);
        $kendaraan_tersedia = array_filter($kendaraan_list, function($k) {
            return $k['status'] == 'tersedia';
        });
        $pelanggan_list = $this->pelangganModel->getAll('', 1000, 0);
        
        $view_data = null;
        if (isset($_GET['view'])) {
            $view_data = $this->rentalModel->getById($_GET['view']);
        }
        
        require_once 'views/rental/index.php';
    }
    
    public function hitungTotal() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_kendaraan = $_POST['id_kendaraan'];
            $tgl_sewa = $_POST['tgl_sewa'];
            $tgl_kembali = $_POST['tgl_kembali'];
            
            $total = $this->rentalModel->hitungTotalHarga($id_kendaraan, $tgl_sewa, $tgl_kembali);
            
            header('Content-Type: application/json');
            echo json_encode(['total_harga' => $total]);
            exit();
        }
    }
}
?>