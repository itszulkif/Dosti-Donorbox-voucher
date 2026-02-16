<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$campaign_id = $_POST['campaign_id'] ?? null;

if (!$campaign_id) {
    echo json_encode(['success' => false, 'message' => 'Campaign ID is required']);
    exit;
}

// Delete related records first
$conn->query("DELETE FROM campaign_opens WHERE campaign_id = $campaign_id");
$conn->query("DELETE FROM campaign_clicks WHERE campaign_id = $campaign_id");

// Delete the campaign
$stmt = $conn->prepare("DELETE FROM email_campaigns WHERE id = ?");
$stmt->bind_param("i", $campaign_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Campaign deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete campaign: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
