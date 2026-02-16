<?php
include '../config.php';

$campaign_id = $_GET['c'] ?? null;
$recipient_email = $_GET['e'] ?? '';
$url = $_GET['u'] ?? '';

if ($campaign_id && $recipient_email && $url) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt = $conn->prepare("INSERT INTO campaign_clicks (campaign_id, recipient_email, url, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $campaign_id, $recipient_email, $url, $ip, $ua);
        if (!$stmt->execute()) {
            error_log("Tracking Click Failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
         error_log("Tracking Click Prepare Failed: " . $conn->error);
    }
}

if (!empty($url)) {
    header("Location: " . $url);
} else {
    echo "Invalid link.";
}

$conn->close();
?>
