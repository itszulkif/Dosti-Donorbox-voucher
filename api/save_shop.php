<?php
ob_start(); // Start output buffering to capture any unwanted warnings/output
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

// Clear buffer just in case config included anything
ob_clean();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$box_number = $_POST['box_number'] ?? '';
$shop_name = $_POST['shop_name'] ?? '';
$installation_date = !empty($_POST['installation_date']) ? $_POST['installation_date'] : null;
$contact_person = $_POST['contact_person'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($box_number) || empty($shop_name)) {
    echo json_encode(['success' => false, 'message' => 'Box Number and Shop Name are required']);
    exit;
}

if ($id) {
    // Update
    $stmt = $conn->prepare("UPDATE donation_shops SET box_number=?, shop_name=?, email=?, installation_date=?, contact_person=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param("sssssssi", $box_number, $shop_name, $email, $installation_date, $contact_person, $phone, $address, $id);
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO donation_shops (box_number, shop_name, email, installation_date, contact_person, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $box_number, $shop_name, $email, $installation_date, $contact_person, $phone, $address);
}

if ($stmt->execute()) {
    $shop_id = $id ? $id : $stmt->insert_id;
    
    // --- EMAIL SENDING LOGIC (Only for NEW shops) ---
    $email_status = 'Not Sent';
    if (!$id && !empty($email)) {
        require_once __DIR__ . '/../lib/SimpleMailer.php';
        require_once __DIR__ . '/../lib/email_logger.php';
        
        // Fetch Settings
        $settingsResult = $conn->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'email_template_box_%'");
        $settings = [];
        while ($row = $settingsResult->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        if (!empty($settings['smtp_host'])) {
            $subject = $settings['email_template_box_subject'] ?? "Collection Scheduled for {shop_name}";
            $body = $settings['email_template_box_body'] ?? "Hello {contact_person},\n\nOur team will visit {shop_name} for box collection soon.";

            // Replace Placeholders
            $placeholders = [
                '{shop_name}' => $shop_name,
                '{contact_person}' => $contact_person,
                '{box_number}' => $box_number
            ];
            foreach ($placeholders as $tag => $val) {
                $subject = str_replace($tag, $val, $subject);
                $body = str_replace($tag, $val, $body);
            }

            $mail = new SimpleMailer();
            $mail->setHost($settings['smtp_host']);
            $mail->setPort($settings['smtp_port'] ?? 465);
            $mail->setUsername($settings['smtp_user'] ?? '');
            $mail->setPassword($settings['smtp_pass'] ?? '');
            $mail->setEncryption($settings['smtp_encryption'] ?? 'ssl');
            $mail->setFrom($settings['smtp_from_email'] ?? ($settings['smtp_user'] ?? ''), $settings['smtp_from_name'] ?? 'Dosti Welfare');
            $mail->addAddress($email, $contact_person);
            $mail->setSubject($subject);
            $mail->setBody($body);

            if ($mail->send()) {
                $email_status = 'Sent Successfully';
                logEmail($conn, $email, $contact_person, $subject, $body, 'sent', null, 'box');
            } else {
                $error = $mail->getErrorInfo();
                $email_status = 'Failed: ' . $error;
                logEmail($conn, $email, $contact_person, $subject, $body, 'failed', $error, 'box');
            }
        } else {
             $email_status = 'SMTP Not Configured';
        }
    }

    echo json_encode(['success' => true, 'message' => 'Shop saved successfully', 'shop_id' => $shop_id, 'email_status' => $email_status]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
