<?php
require_once 'models/KendaraanModel.php';
require_once 'models/TipeKendaraanModel.php';

class KendaraanController {
    private $kendaraanModel;
    private $tipeModel;
    
    public function __construct() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        
        $this->kendaraanModel = new KendaraanModel();
        $this->tipeModel = new TipeKendaraanModel();
    }
    
    public function index() {
        
        if (isset($_GET['delete'])) {
            $this->kendaraanModel->delete($_GET['delete']);
            header("Location: index.php?page=kendaraan&success=delete");
            exit();
        }
        
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'no_plat' => trim($_POST['no_plat']),
                'merk' => trim($_POST['merk']),
                'tahun' => $_POST['tahun'],
                'id_tipe' => $_POST['id_tipe'],
                'status' => $_POST['status'],
                'harga_sewa' => $_POST['harga_sewa']
            ];
            
            if (isset($_POST['id_kendaraan']) && !empty($_POST['id_kendaraan'])) {
                $this->kendaraanModel->update($_POST['id_kendaraan'], $data);
                header("Location: index.php?page=kendaraan&success=update");
            } else {
                $this->kendaraanModel->create($data);
                header("Location: index.php?page=kendaraan&success=add");
            }
            exit();
        }
        
        
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        
        $total_records = $this->kendaraanModel->count($search);
        $total_pages = ceil($total_records / $per_page);
        $kendaraan_list = $this->kendaraanModel->getAll($search, $per_page, $offset);
        $tipe_list = $this->tipeModel->getAll();
        
        
        $edit_data = null;
        if (isset($_GET['edit'])) {
            $edit_data = $this->kendaraanModel->getById($_GET['edit']);
        }
        
        require_once 'views/kendaraan/index.php';
    }
}
?>