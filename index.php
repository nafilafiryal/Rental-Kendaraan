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
        // TODO: Buat PelangganController
        echo "Halaman Pelanggan - Coming Soon";
        break;
        
    case 'rental':
        // TODO: Buat RentalController
        echo "Halaman Rental - Coming Soon";
        break;
        
    case 'pengembalian':
        // TODO: Buat PengembalianController
        echo "Halaman Pengembalian - Coming Soon";
        break;
        
    case 'laporan':
        // TODO: Buat LaporanController
        echo "Halaman Laporan - Coming Soon";
        break;
        
    default:
        header("Location: index.php?page=login");
        exit();
}
?>