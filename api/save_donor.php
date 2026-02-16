<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $voucher_id = $_POST['voucher_id'] ?? '';

    if (empty($name) || empty($voucher_id)) {
        echo json_encode(['success' => false, 'message' => 'Name and Voucher ID are required.']);
        exit;
    }

    // Fetch current voucher price from settings
    $priceResult = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = 'voucher_price'");
    $voucher_value = 500.00; // Default fallback
    if ($priceResult && $row = $priceResult->fetch_assoc()) {
        $voucher_value = (float)$row['setting_value'];
    }

    $stmt = $conn->prepare("INSERT INTO donors (name, email, phone, voucher_id, voucher_value) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $name, $email, $phone, $voucher_id, $voucher_value);

    if ($stmt->execute()) {
        $donor_id = $conn->insert_id;
        
        // Handle Assigned Restaurants (Multi-Redemption)
        if (isset($_POST['assigned_restaurants']) && is_array($_POST['assigned_restaurants'])) {
            $assigned = $_POST['assigned_restaurants'];
            
            $offerStmt = $conn->prepare("INSERT INTO donor_offers (donor_id, restaurant_name, restaurant_address, offer_type, offer_value, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
            
            foreach ($assigned as $r_name) {
                // Fetch restaurant details for the offer
                $resQuery = $conn->prepare("SELECT address, discount_percentage, custom_price FROM restaurants WHERE name = ?");
                $resQuery->bind_param("s", $r_name);
                $resQuery->execute();
                $resData = $resQuery->get_result()->fetch_assoc();
                
                if ($resData) {
                    $r_addr = $resData['address'];
                    $r_type = $resData['discount_percentage'] > 0 ? 'percentage' : 'fixed';
                    $r_val = $r_type === 'percentage' ? $resData['discount_percentage'] : $resData['custom_price'];
                    
                    $offerStmt->bind_param("isssd", $donor_id, $r_name, $r_addr, $r_type, $r_val);
                    $offerStmt->execute();
                }
                $resQuery->close();
            }
            $offerStmt->close();
        }

        // --- EMAIL SENDING LOGIC ---
        $email_status = 'Not Sent';
        if (!empty($email)) {
            require_once __DIR__ . '/../lib/SimpleMailer.php';
            require_once __DIR__ . '/../lib/email_logger.php';
            
            // Fetch Settings
            $settingsResult = $conn->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'email_template_voucher_%'");
            $settings = [];
            while ($row = $settingsResult->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }

            if (!empty($settings['smtp_host'])) {
                $subject = $settings['email_template_voucher_subject'] ?? "Your Dosti Voucher: {voucher_id}";
                $body = $settings['email_template_voucher_body'] ?? "Hello {name},\n\nYour voucher ID is: {voucher_id}.\nThank you for your support!";

                // Replace Placeholders
                $placeholders = [
                    '{name}' => $name,
                    '{email}' => $email,
                    '{voucher_id}' => $voucher_id,
                    '{phone}' => $phone
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
                $mail->addAddress($email, $name);
                $mail->setSubject($subject);
                $mail->setBody($body);

                if ($mail->send()) {
                    $email_status = 'Sent Successfully';
                    logEmail($conn, $email, $name, $subject, $body, 'sent', null, 'voucher');
                } else {
                    $error = $mail->getErrorInfo();
                    $email_status = 'Failed: ' . $error;
                    logEmail($conn, $email, $name, $subject, $body, 'failed', $error, 'voucher');
                }
            } else {
                 $email_status = 'SMTP Not Configured';
            }
        }

        echo json_encode(['success' => true, 'donor_id' => $donor_id, 'email_status' => $email_status]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error or Voucher ID already exists.']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
