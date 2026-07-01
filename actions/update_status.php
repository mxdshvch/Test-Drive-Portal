<?php
require_once '../config.php';

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Метод запроса не поддерживается']);
    exit;
}

$applicationId = (int)($_POST['application_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';
$rejectReason = isset($_POST['reject_reason']) ? trim($_POST['reject_reason']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($applicationId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Некорректный ID заявки']);
    exit;
}

$allowedStatuses = ['pending', 'approved', 'completed', 'rejected', 'new', 'processing'];
if (!in_array($newStatus, $allowedStatuses, true)) {
    echo json_encode(['success' => false, 'message' => 'Некорректный статус']);
    exit;
}

if ($newStatus === 'rejected' && $rejectReason === '') {
    echo json_encode(['success' => false, 'message' => 'Укажите причину отклонения заявки']);
    exit;
}

try {
    $isAdminUser = isAdmin();
    $userId = (int)$_SESSION['user_id'];

    $stmt = $pdo->prepare('SELECT user_id FROM applications WHERE id = ?');
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch();

    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Заявка не найдена']);
        exit;
    }

    if (!$isAdminUser && (int)$application['user_id'] !== $userId) {
        echo json_encode(['success' => false, 'message' => 'У вас нет прав для изменения этой заявки']);
        exit;
    }

    $stmt = $pdo->prepare(
        'UPDATE applications
         SET status = ?, rejection_reason = ?, comment = ?
         WHERE id = ?'
    );
    $result = $stmt->execute([
        $newStatus,
        $newStatus === 'rejected' ? $rejectReason : null,
        $comment !== '' ? $comment : null,
        $applicationId,
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Статус заявки успешно обновлен',
            'data' => [
                'application_id' => $applicationId,
                'status' => $newStatus,
                'rejection_reason' => $newStatus === 'rejected' ? $rejectReason : null,
                'comment' => $comment !== '' ? $comment : null,
            ],
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Статус не был изменен']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении статуса']);
}
