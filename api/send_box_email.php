<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_id = $_POST['shop_id'] ?? '';

    if (empty($shop_id)) {
        echo json_encode(['success' => false, 'message' => 'Shop ID is required.']);
        exit;
    }

    // 1. Fetch Shop Details
    $stmt = $conn->prepare("SELECT shop_name, email, box_number, contact_person FROM donation_shops WHERE id = ?");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $shop = $stmt->get_result()->fetch_assoc();
    
    if (!$shop || empty($shop['email'])) {
        echo json_encode(['success' => false, 'message' => 'Shop not found or has no email address.']);
        exit;
    }

    // 2. Fetch SMTP & Template Settings
    $settingsResult = $conn->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'email_template_box_%'");
    $settings = [];
    while ($row = $settingsResult->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    // Validate SMTP configuration
    if (empty($settings['smtp_host']) || empty($settings['smtp_user']) || empty($settings['smtp_pass'])) {
        echo json_encode(['success' => false, 'message' => 'SMTP settings are not configured.']);
        exit;
    }

    // 3. Prepare Template
    $subject = $settings['email_template_box_subject'] ?? "Collection Scheduled for {shop_name}";
    $body = $settings['email_template_box_body'] ?? "Hello {contact_person},\n\nOur team will visit {shop_name} for box collection soon.\nBox Number: {box_number}";

    // Replace Placeholders
    $placeholders = [
        '{shop_name}' => $shop['shop_name'],
        '{contact_person}' => $shop['contact_person'] ?? 'Partner',
        '{box_number}' => $shop['box_number']
    ];

    foreach ($placeholders as $tag => $val) {
        $subject = str_replace($tag, $val, $subject);
        $body = str_replace($tag, $val, $body);
    }

    // 4. Send Email using SimpleMailer
    require_once __DIR__ . '/../lib/SimpleMailer.php';
    require_once __DIR__ . '/../lib/email_logger.php';
    
    $mail = new SimpleMailer();
    $mail->setHost($settings['smtp_host']);
    $mail->setPort($settings['smtp_port'] ?? 465);
    $mail->setUsername($settings['smtp_user']);
    $mail->setPassword($settings['smtp_pass']);
    $mail->setEncryption($settings['smtp_encryption'] ?? 'ssl');
    $mail->setFrom($settings['smtp_from_email'] ?? $settings['smtp_user'], $settings['smtp_from_name'] ?? 'Dosti Welfare');
    $mail->addAddress($shop['email'], $shop['contact_person'] ?? $shop['shop_name']);
    $mail->setSubject($subject);
    $mail->setBody($body);

    if ($mail->send()) {
        logEmail($conn, $shop['email'], $shop['contact_person'] ?? $shop['shop_name'], $subject, $body, 'sent', null, 'box');
        echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
    } else {
        $error = $mail->getErrorInfo();
        logEmail($conn, $shop['email'], $shop['contact_person'] ?? $shop['shop_name'], $subject, $body, 'failed', $error, 'box');
        echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $error]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
