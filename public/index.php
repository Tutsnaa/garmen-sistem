<?php
session_start();

// 1. Import Konfigurasi Database & Controller
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/MemberController.php';
require_once __DIR__ . '/../app/controllers/PenggunaController.php';

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
    // PENGGUNA ROUTES
    // ==========================================
    case 'pengguna':
        cekLogin();
        $penggunaController = new PenggunaController($pdo);
        $penggunaList = $penggunaController->index();
        $pesan = $_GET['pesan'] ?? null;
        $tipe  = ($_GET['tipe'] ?? 'success') === 'danger' ? 'danger' : 'success';
        require_once __DIR__ . '/../app/views/pengguna/index.php';
        break;

    case 'pengguna_store':
        cekLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=pengguna');
            exit;
        }
        $penggunaController = new PenggunaController($pdo);
        $result = $penggunaController->store($_POST);
        $type = $result['success'] ? 'success' : 'danger';
        $openCreateParam = $result['success'] ? '' : '&open_create=1';
        header('Location: index.php?action=pengguna&tipe=' . $type . $openCreateParam . '&pesan=' . urlencode($result['message']));
        exit;

    case 'pengguna_edit':
    case 'pengguna_update':
        cekLogin();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=pengguna&tipe=danger&pesan=' . urlencode('Permintaan edit pengguna tidak valid.'));
            exit;
        }

        $penggunaController = new PenggunaController($pdo);
        $result = $penggunaController->update($id, $_POST);
        $type = $result['success'] ? 'success' : 'danger';

        header('Location: index.php?action=pengguna&tipe=' . $type . '&pesan=' . urlencode($result['message']));
        exit;

    case 'pengguna_delete':
        cekLogin();
        require_once __DIR__ . '/../app/views/pengguna/delete.php';
        break;

    // ==========================================
    // MEMBER ROUTES
    // ==========================================
    case 'member':
        cekLogin();
        $memberController = new MemberController($pdo);
        $members = $memberController->index();
        $pesan = $_GET['pesan'] ?? null;
        $tipe  = ($_GET['tipe'] ?? 'success') === 'danger' ? 'danger' : 'success';
        require_once __DIR__ . '/../app/views/member/index.php';
        break;

    case 'member_store':
        cekLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=member');
            exit;
        }
        $memberController = new MemberController($pdo);
        $result = $memberController->store($_POST);
        $type = $result['success'] ? 'success' : 'danger';
        $openCreateParam = $result['success'] ? '' : '&open_create=1';
        header('Location: index.php?action=member&tipe=' . $type . $openCreateParam . '&pesan=' . urlencode($result['message']));
        exit;

    case 'member_edit':
    case 'member_update':
        cekLogin();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: index.php?action=member&tipe=danger&pesan=' . urlencode('ID member tidak valid.'));
            exit;
        }

        $memberController = new MemberController($pdo);
        $errorMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $memberController->update($id, $_POST);
            if ($result['success']) {
                header('Location: index.php?action=member&tipe=success&pesan=' . urlencode($result['message']));
                exit;
            }
            $errorMessage = $result['message'];
        }

        $member = $memberController->edit($id);
        if (!$member) {
            header('Location: index.php?action=member&tipe=danger&pesan=' . urlencode('Data member tidak ditemukan.'));
            exit;
        }

        require_once __DIR__ . '/../app/views/member/update.php';
        break;

    case 'member_delete':
        cekLogin();
        require_once __DIR__ . '/../app/views/member/delete.php';
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