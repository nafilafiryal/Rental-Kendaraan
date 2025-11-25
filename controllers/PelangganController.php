<?php
require_once 'models/PelangganModel.php';

class PelangganController {
    private $pelangganModel;
    
    public function __construct() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        
        $this->pelangganModel = new PelangganModel();
    }
    
    public function index() {
        // Handle Delete
        if (isset($_GET['delete'])) {
            $this->pelangganModel->delete($_GET['delete']);
            header("Location: index.php?page=pelanggan&success=delete");
            exit();
        }
        
        // Handle Add/Edit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama' => trim($_POST['nama']),
                'alamat' => trim($_POST['alamat']),
                'no_hp' => trim($_POST['no_hp']),
                'no_ktp' => trim($_POST['no_ktp']),
                'email' => trim($_POST['email'])
            ];
            
            $is_edit = isset($_POST['id_pelanggan']) && !empty($_POST['id_pelanggan']);
            $ktp_exists = $this->pelangganModel->checkKTPExists(
                $data['no_ktp'], 
                $is_edit ? $_POST['id_pelanggan'] : null
            );
            
            if ($ktp_exists) {
                $error_message = "Nomor KTP sudah terdaftar!";
                $edit_data = $data;
                if ($is_edit) {
                    $edit_data['id_pelanggan'] = $_POST['id_pelanggan'];
                }
            } else {
                if ($is_edit) {
                    $this->pelangganModel->update($_POST['id_pelanggan'], $data);
                    header("Location: index.php?page=pelanggan&success=update");
                } else {
                    $this->pelangganModel->create($data);
                    header("Location: index.php?page=pelanggan&success=add");
                }
                exit();
            }
        }
        
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $total_records = $this->pelangganModel->count($search);
        $total_pages = ceil($total_records / $per_page);
        $pelanggan_list = $this->pelangganModel->getAll($search, $per_page, $offset);
        
        $edit_data = null;
        if (isset($_GET['edit'])) {
            $edit_data = $this->pelangganModel->getById($_GET['edit']);
        }
        
        require_once 'views/pelanggan/index.php';
    }
}
?>