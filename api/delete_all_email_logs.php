<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if ($conn->query("TRUNCATE TABLE email_logs")) {
    echo json_encode(['success' => true, 'message' => 'All logs deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete logs']);
}

$conn->close();
?>
