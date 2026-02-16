<?php
include '../config.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) { echo json_encode(['success' => false]); exit; }

$stmt = $conn->prepare("DELETE FROM donation_shops WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
