<?php
include '../config.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$name = $_POST['restaurant_name'] ?? null;
$address = $_POST['restaurant_address'] ?? null;
$discount = $_POST['discount_percentage'] ?? 0;
$price = $_POST['custom_price'] ?? 0;

if (!$id || !$name) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    exit;
}

$stmt = $conn->prepare("UPDATE restaurants SET name = ?, address = ?, discount_percentage = ?, custom_price = ? WHERE id = ?");
$stmt->bind_param("ssdid", $name, $address, $discount, $price, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
