<?php
/**
 * Email Logging Helper Function
 * Call this after attempting to send an email to log the result
 */
function logEmail($conn, $recipient_email, $recipient_name, $subject, $body, $status, $error_message, $email_type, $campaign_id = null) {
    // Check for duplicate entry within last 5 seconds
    $checkStmt = $conn->prepare("SELECT id FROM email_logs WHERE recipient_email = ? AND subject = ? AND email_type = ? AND sent_at >= DATE_SUB(NOW(), INTERVAL 5 SECOND)");
    $checkStmt->bind_param("sss", $recipient_email, $subject, $email_type);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Duplicate detected within 5 seconds, skip logging
        $checkStmt->close();
        return;
    }
    $checkStmt->close();
    
    // No duplicate found, proceed with logging
    $stmt = $conn->prepare("INSERT INTO email_logs (recipient_email, recipient_name, subject, body, status, error_message, email_type, campaign_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $recipient_email, $recipient_name, $subject, $body, $status, $error_message, $email_type, $campaign_id);
    $stmt->execute();
    $stmt->close();
}
?>
