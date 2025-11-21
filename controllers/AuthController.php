<?php
require_once 'models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        session_start();
        $error_message = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $pass = $_POST['password'];
            
            if (empty($email) || empty($pass)) {
                $error_message = "Silakan isi semua field";
            } else {
                $user = $this->userModel->findByEmail($email);
                
                if ($user && $this->userModel->verifyPassword($pass, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['nama'] = $user['nama'];
                    
                    header("Location: index.php?page=dashboard");
                    exit();
                } else {
                    $error_message = "Email atau password tidak valid";
                }
            }
        }
        
        require_once 'views/auth/login.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?page=login");
        exit();
    }
}
?>