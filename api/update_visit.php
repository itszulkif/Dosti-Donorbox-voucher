<?php
include '../config.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$amount = $_POST['amount'] ?? null;
$visit_date = $_POST['visit_date'] ?? null;
$received_from = $_POST['received_from'] ?? null;
$received_by = $_POST['received_by'] ?? null;

if (!$id || !$amount || !$visit_date || !$received_from || !$received_by) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    exit;
}

$stmt = $conn->prepare("UPDATE donation_visits SET amount = ?, visit_date = ?, received_from = ?, received_by = ? WHERE id = ?");
$stmt->bind_param("dsssi", $amount, $visit_date, $received_from, $received_by, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
