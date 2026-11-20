<?php
// app/views/member/delete.php
// Diproses lewat public/index.php (case 'member_delete')
if (!isset($pdo)) {
    require_once __DIR__ . '/../../config/database.php';
}
require_once __DIR__ . '/../../controllers/MemberController.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php?action=member&tipe=danger&pesan=' . urlencode('ID member tidak valid.'));
    exit;
}

$memberController = new MemberController($pdo);
$result = $memberController->destroy($id);
$type = $result['success'] ? 'success' : 'danger';

header('Location: index.php?action=member&tipe=' . $type . '&pesan=' . urlencode($result['message']));
exit;