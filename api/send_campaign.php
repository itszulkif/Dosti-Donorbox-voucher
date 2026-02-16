<?php
header('Content-Type: application/json');
include '../config.php';
require_once __DIR__ . '/../lib/SimpleMailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$audience_type = $_POST['audience'] ?? 'donor';
$subject = $_POST['subject'] ?? '';
$body = $_POST['body'] ?? '';
$action_type = $_POST['action_type'] ?? 'now';
$scheduled_at = $_POST['scheduled_at'] ?? '';

if (empty($subject) || empty($body)) {
    echo json_encode(['success' => false, 'message' => 'Subject and Content are required']);
    exit;
}

// 1. Handle Scheduling
if ($action_type === 'schedule') {
    if (empty($scheduled_at)) {
        echo json_encode(['success' => false, 'message' => 'Schedule time is required']);
        exit;
    }

    // First, save as a template to reference it
    $stmt = $conn->prepare("INSERT INTO campaign_templates (name, subject, body, audience_type) VALUES (?, ?, ?, ?)");
    $temp_name = "Scheduled: " . date('Y-m-d H:i');
    $stmt->bind_param("ssss", $temp_name, $subject, $body, $audience_type);
    $stmt->execute();
    $template_id = $stmt->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO scheduled_campaigns (template_id, audience_type, scheduled_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $template_id, $audience_type, $scheduled_at);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Campaign scheduled successfully for ' . $scheduled_at]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Scheduling failed: ' . $conn->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// 2. Handle Send Now
// Fetch SMTP Settings
$settingsResult = $conn->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'smtp_%'");
$settings = [];
while ($row = $settingsResult->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

if (empty($settings['smtp_host']) || empty($settings['smtp_user'])) {
    echo json_encode(['success' => false, 'message' => 'SMTP settings not configured. Please check Settings > Email SMTP.']);
    exit;
}

// Fetch Audience
$recipients = [];
if ($audience_type === 'donor') {
    $res = $conn->query("SELECT name, email, phone, voucher_id FROM donors WHERE email != '' AND email IS NOT NULL");
    while($row = $res->fetch_assoc()) $recipients[] = $row;
} else {
    $res = $conn->query("SELECT contact_person as name, email, phone, shop_name, box_number FROM donation_shops WHERE email != '' AND email IS NOT NULL");
    while($row = $res->fetch_assoc()) $recipients[] = $row;
}

if (empty($recipients)) {
    echo json_encode(['success' => false, 'message' => 'No recipients found with email addresses in this audience.']);
    exit;
}

$sent_count = 0;
$fail_count = 0;

// Log the campaign initially
$stmt = $conn->prepare("INSERT INTO email_campaigns (subject, body, audience_type) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $subject, $body, $audience_type);
$stmt->execute();
$campaign_id = $stmt->insert_id;
$stmt->close();

    // Use CampaignSender
    require_once __DIR__ . '/../lib/CampaignSender.php';
    $sender = new CampaignSender($conn);
    $result = $sender->sendCampaign($campaign_id, $subject, $body, $recipients);

    if (!$result['success']) {
        // Log failure if SMTP setup is wrong
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit;
    }

    $sent_count = $result['sent'];
    $fail_count = $result['failed'];

// Update campaign counts
$stmt = $conn->prepare("UPDATE email_campaigns SET sent_count = ?, fail_count = ? WHERE id = ?");
$stmt->bind_param("iii", $sent_count, $fail_count, $campaign_id);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true, 
    'message' => "Campaign Completed. Sent: $sent_count, Failed: $fail_count."
]);

$conn->close();
?>
