<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$log_id = $_POST['log_id'] ?? null;

if (empty($log_id)) {
    echo json_encode(['success' => false, 'message' => 'Log ID is required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM email_logs WHERE id = ?");
$stmt->bind_param("i", $log_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Log deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete log']);
}

$stmt->close();
$conn->close();
?>
