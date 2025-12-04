<?php
// Router Utama Aplikasi

// Ambil parameter page, default = login
$page = $_GET['page'] ?? 'login';

// Helper untuk load controller + method
function loadController($controllerName, $method, $action = null) {
    $path = "controllers/{$controllerName}.php";

    if (!file_exists($path)) {
        die("Controller <b>$controllerName</b> tidak ditemukan!");
    }

    require_once $path;

    if (!class_exists($controllerName)) {
        die("Class <b>$controllerName</b> tidak ditemukan di file!");
    }

    $controller = new $controllerName();

    if ($action !== null && method_exists($controller, $action)) {
        return $controller->$action();
    }

    return $controller->$method();
}

// Routing
switch ($page) {

    // Auth
    case 'login':
        loadController('AuthController', 'login');
        break;

    case 'logout':
        loadController('AuthController', 'logout');
        break;

    // Dashboard
    case 'dashboard':
        loadController('DashboardController', 'index');
        break;

    // Kendaraan
    case 'kendaraan':
        loadController('KendaraanController', 'index');
        break;

    // Pelanggan
    case 'pelanggan':
        loadController('PelangganController', 'index');
        break;

    // Rental
    case 'rental':
        $action = ($_GET['action'] ?? '') === 'hitung_total' ? 'hitungTotal' : 'index';
        loadController('RentalController', $action);
        break;

    // Pengembalian
    case 'pengembalian':
        $action = ($_GET['action'] ?? '') === 'hitung_denda' ? 'hitungDenda' : 'index';
        loadController('PengembalianController', $action);
        break;

    // Laporan
    case 'laporan':
        $action = ($_GET['action'] ?? '') === 'export' ? 'export' : 'index';
        loadController('LaporanController', $action);
        break;

    // Default kembali ke login
    default:
        header("Location: index.php?page=login");
        exit();
}
?>
