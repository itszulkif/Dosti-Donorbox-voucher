<?php
include '../config.php';

$campaign_id = $_GET['c'] ?? null;
$recipient_email = $_GET['e'] ?? '';

if ($campaign_id && $recipient_email) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt = $conn->prepare("INSERT INTO campaign_opens (campaign_id, recipient_email, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isss", $campaign_id, $recipient_email, $ip, $ua);
        if (!$stmt->execute()) {
             error_log("Tracking Open Failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Tracking Open Prepare Failed: " . $conn->error);
    }
}

// Serve a 1x1 transparent GIF
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
$conn->close();
?>
