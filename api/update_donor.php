<?php
include '../config.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$phone = $_POST['phone'] ?? null;

if (!$id || !$name || !$email || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    exit;
}

$stmt = $conn->prepare("UPDATE donors SET name = ?, email = ?, phone = ? WHERE id = ?");
$stmt->bind_param("sssi", $name, $email, $phone, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
