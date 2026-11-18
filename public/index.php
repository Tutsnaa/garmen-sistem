<?php
session_start();

// 1. Import Konfigurasi Database & Controller
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// 2. Tangkap parameter action dari URL (Default ke 'login' jika belum login, atau 'dashboard' jika sudah)
$action = $_GET['action'] ?? (isset($_SESSION['user']) ? 'dashboard' : 'login');

// 3. Helper Function untuk Proteksi Halaman
function cekLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?action=login');
        exit;
    }
}

// 4. Switch Case Routing Utama
switch ($action) {

    // ==========================================
    // AUTHENTICATION ROUTES
    // ==========================================
    case 'login':
        $auth = new AuthController($pdo);
        $auth->loginForm();
        break;

    case 'login_process':
        $auth = new AuthController($pdo);
        $auth->loginProcess();
        break;

    case 'logout':
        $auth = new AuthController($pdo);
        $auth->logout();
        break;

    // ==========================================
    // DASHBOARD ROUTE
    // ==========================================
    case 'dashboard':
        cekLogin();
        require_once __DIR__ . '/../app/views/dashboard/index.php';
        break;
        
    // ==========================================
    // DEFAULT / FALLBACK ROUTE
    // ==========================================
    default:
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
        } else {
            header('Location: index.php?action=dashboard');
        }
        exit;
        break;
}