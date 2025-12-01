<?php
// coba 
// Router utama aplikasi
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

switch ($page) {
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'kendaraan':
        require_once 'controllers/KendaraanController.php';
        $controller = new KendaraanController();
        $controller->index();
        break;
        
    case 'pelanggan':
        require_once 'controllers/PelangganController.php';
        $controller = new PelangganController();
        $controller->index();
        break;
        
    case 'rental':
        require_once 'controllers/RentalController.php';
        $controller = new RentalController();
        if (isset($_GET['action']) && $_GET['action'] == 'hitung_total') {
            $controller->hitungTotal();
        } else {
            $controller->index();
        }
        break;
        
    case 'pengembalian':
        require_once 'controllers/PengembalianController.php';
        $controller = new PengembalianController();
        if (isset($_GET['action']) && $_GET['action'] == 'hitung_denda') {
            $controller->hitungDenda();
        } else {
            $controller->index();
        }
        break;
        
    case 'laporan':
        require_once 'controllers/LaporanController.php';
        $controller = new LaporanController();
        if (isset($_GET['action']) && $_GET['action'] == 'export') {
            $controller->export();
        } else {
            $controller->index();
        }
        break;
        
    default:
        header("Location: index.php?page=login");
        exit();
}
?>