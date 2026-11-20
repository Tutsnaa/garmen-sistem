<?php
// app/views/pengguna/delete.php
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config/database.php';
}
require_once __DIR__ . '/../../controllers/PenggunaController.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php?action=pengguna&tipe=danger&pesan=' . urlencode('ID pengguna tidak valid.'));
    exit;
}

$currentUserId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;

$penggunaController = new PenggunaController($pdo);
$result = $penggunaController->destroy($id, $currentUserId);
$type = $result['success'] ? 'success' : 'danger';

header('Location: index.php?action=pengguna&tipe=' . $type . '&pesan=' . urlencode($result['message']));
exit;