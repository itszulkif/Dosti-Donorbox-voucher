<?php
header('Content-Type: application/json');
require_once '../lib/SimpleMailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['smtp_host'] ?? '';
    $port = $_POST['smtp_port'] ?? 465;
    $username = $_POST['smtp_user'] ?? '';
    $password = $_POST['smtp_pass'] ?? '';
    $encryption = $_POST['smtp_encryption'] ?? 'ssl';
    $fromEmail = $_POST['smtp_from_email'] ?? '';
    $fromName = $_POST['smtp_from_name'] ?? 'SMTP Test';

    if (empty($host) || empty($username) || empty($password) || empty($fromEmail)) {
        echo json_encode(['success' => false, 'message' => 'Missing required SMTP settings (Host, User, Password, From Email).']);
        exit;
    }

    $mailer = new SimpleMailer();
    $mailer->setHost($host);
    $mailer->setPort((int)$port);
    $mailer->setUsername($username);
    $mailer->setPassword($password);
    $mailer->setEncryption($encryption);
    $mailer->setFrom($fromEmail, $fromName);
    
    // Send to self to verify send capability
    $mailer->addAddress($fromEmail, $fromName);
    
    $mailer->setSubject("SMTP Connection Test");
    $mailer->setBody("<h1>SMTP Test Successful</h1><p>This email confirms that your SMTP settings are correctly configured.</p><p>Timestamp: " . date('Y-m-d H:i:s') . "</p>");

    if ($mailer->send()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Connection successful! Test email sent to ' . $fromEmail,
            'debug' => $mailer->debug
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Connection failed: ' . $mailer->getErrorInfo(),
            'debug' => $mailer->debug
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
