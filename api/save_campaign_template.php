<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Untitled Template';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    $audience_type = $_POST['audience_type'] ?? 'donor';
    $id = $_POST['id'] ?? null;

    if (empty($subject) || empty($body)) {
        echo json_encode(['success' => false, 'message' => 'Subject and Body are required.']);
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE campaign_templates SET name=?, subject=?, body=?, audience_type=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $subject, $body, $audience_type, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO campaign_templates (name, subject, body, audience_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $subject, $body, $audience_type);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Template saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
$conn->close();
?>
