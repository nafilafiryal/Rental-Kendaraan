<?php
require_once 'models/RentalModel.php';
require_once 'models/KendaraanModel.php';
require_once 'models/PelangganModel.php';
require_once 'models/SopirModel.php';

class RentalController {
    private $rentalModel;
    private $kendaraanModel;
    private $pelangganModel;
    private $sopirModel;
    
    public function __construct() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
        $this->rentalModel = new RentalModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->pelangganModel = new PelangganModel();
        $this->sopirModel = new SopirModel();
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
        
        if (isset($_GET['activate'])) {
            $this->rentalModel->activateBooking($_GET['activate']);
            header("Location: index.php?page=rental&success=activate");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // LOGIKA BARU: Cek apakah input pelanggan baru atau pilih lama
                $id_pelanggan_fix = null;
                $pesan_info = null;

                if (isset($_POST['mode_pelanggan']) && $_POST['mode_pelanggan'] == 'baru') {
                    // Ambil inputan
                    $hp_baru = trim($_POST['hp_baru']);
                    $ktp_baru = trim($_POST['ktp_baru']);
                    
                    // 1. CEK DULU: Apakah No HP atau KTP ini sudah ada di database?
                    $existing = $this->pelangganModel->findByKtpOrHp($ktp_baru, $hp_baru);
                    
                    if ($existing) {
                        // KASUS: Data Sudah Ada (Duplikat HP atau KTP)
                        // Solusi: Gunakan ID pelanggan yang sudah ada (Auto-Merge)
                        $id_pelanggan_fix = $existing['id_pelanggan'];
                        
                        // Beri info ke admin bahwa data lama yang dipakai
                        $pesan_info = "Nomor HP/KTP sudah terdaftar a.n " . $existing['nama'] . ". Transaksi otomatis digabungkan ke data lama.";
                        
                    } else {
                        // KASUS: Data Benar-benar Baru -> Buat Baru
                        $dataPelanggan = [
                            'nama' => trim($_POST['nama_baru']),
                            'no_ktp' => $ktp_baru,
                            'no_hp' => $hp_baru,
                            'alamat' => trim($_POST['alamat_baru']),
                            'email' => !empty($_POST['email_baru']) ? trim($_POST['email_baru']) : null
                        ];
                        
                        // Simpan dan ambil ID barunya
                        $id_pelanggan_fix = $this->pelangganModel->create($dataPelanggan);
                    }
                    
                } else {
                    // 2. Jika Mode Pilih Pelanggan Lama
                    $id_pelanggan_fix = $_POST['id_pelanggan'];
                }

                // Validasi akhir ID Pelanggan
                if (!$id_pelanggan_fix) {
                    throw new Exception("Data pelanggan tidak valid.");
                }

                // 3. Simpan Data Rental menggunakan ID Pelanggan yang sudah dipastikan (Baru/Lama)
                $dataRental = [
                    'id_kendaraan' => $_POST['id_kendaraan'],
                    'id_pelanggan' => $id_pelanggan_fix, 
                    'id_sopir' => (!empty($_POST['pakai_sopir']) && !empty($_POST['id_sopir'])) ? $_POST['id_sopir'] : null,
                    'tgl_sewa' => $_POST['tgl_sewa'],
                    'tgl_kembali' => $_POST['tgl_kembali'],
                    'total_harga' => $_POST['total_harga']
                ];
                
                $this->rentalModel->create($dataRental);
                
                // Redirect dengan pesan sukses (dan info jika ada merge)
                $url = "index.php?page=rental&success=add";
                if ($pesan_info) {
                    $url .= "&info=" . urlencode($pesan_info);
                }
                
                header("Location: " . $url);
                
            } catch (Exception $e) {
                // Tangkap error lain (misal koneksi putus)
                $error = urlencode($e->getMessage());
                header("Location: index.php?page=rental&error=" . $error);
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
        $pelanggan_list = $this->pelangganModel->getAll('', 1000, 0);                
        $sopir_list = $this->sopirModel->getAll();                
        $semua_kendaraan = $this->kendaraanModel->getAll('', 1000, 0);                
        $kendaraan_tersedia = array_filter($semua_kendaraan, function($k) {
        
            return strtolower($k['status']) === 'tersedia';
        });
              
        $view_data = null;
        if (isset($_GET['view'])) {
            $view_data = $this->rentalModel->getById($_GET['view']);
        }
        
        require_once 'views/rental/index.php';
    }
}
?>