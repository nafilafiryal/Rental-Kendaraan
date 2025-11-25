<?php
require_once 'models/PengembalianModel.php';

class PengembalianController {
    private $pengembalianModel;
    
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        $this->pengembalianModel = new PengembalianModel();
    }
    
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_rental'])) {
            $data = [
                'tgl_pengembalian' => $_POST['tgl_pengembalian'],
                'kondisi' => $_POST['kondisi'],
                'denda' => $_POST['denda'],
                'keterangan' => trim($_POST['keterangan'])
            ];
            
            try {
                $this->pengembalianModel->prosesPengembalian($_POST['id_rental'], $data);
                header("Location: index.php?page=pengembalian&success=proses");
            } catch (Exception $e) {
                $error_message = "Gagal: " . $e->getMessage();
            }
            exit();
        }
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $rental_aktif = $this->pengembalianModel->getRentalAktif($search);
        
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        $total_records = $this->pengembalianModel->count();
        $total_pages = ceil($total_records / $per_page);
        $riwayat_pengembalian = $this->pengembalianModel->getAll($per_page, $offset);
        
        require_once 'views/pengembalian/index.php';
    }
    
    public function hitungDenda() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_rental = $_POST['id_rental'];
            $tgl_pengembalian = $_POST['tgl_pengembalian'];
            
            $denda = $this->pengembalianModel->hitungDenda($id_rental, $tgl_pengembalian);
            
            header('Content-Type: application/json');
            echo json_encode(['denda' => $denda]);
            exit();
        }
    }
}
?>