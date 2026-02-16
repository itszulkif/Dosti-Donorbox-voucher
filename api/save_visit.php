<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$box_number = $_POST['box_number'] ?? '';
$visit_date = $_POST['visit_date'] ?? '';
$amount = (float)($_POST['amount'] ?? 0);
$amount_1 = (float)($_POST['amount_1'] ?? 0);
$amount_2 = (float)($_POST['amount_2'] ?? 0);
$received_from = $_POST['received_from'] ?? '';
$received_by = $_POST['received_by'] ?? '';

if (empty($box_number) || empty($visit_date)) {
    echo json_encode(['success' => false, 'message' => 'Box Number and Date are required']);
    exit;
}

if ($amount_1 <= 0 || abs($amount_1 - $amount_2) > 0.001) {
    echo json_encode(['success' => false, 'message' => 'Verification failed: Amounts must match and be greater than zero.']);
    exit;
}

// 1. Find shop_id by box_number
$shopStmt = $conn->prepare("SELECT id FROM donation_shops WHERE box_number = ?");
$shopStmt->bind_param("s", $box_number);
$shopStmt->execute();
$shopResult = $shopStmt->get_result();

if ($shop = $shopResult->fetch_assoc()) {
    $shop_id = $shop['id'];
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Box Number']);
    exit;
}
$shopStmt->close();

// 2. Insert visit log
$stmt = $conn->prepare("INSERT INTO donation_visits (shop_id, visit_date, amount, amount_1, amount_2, received_from, received_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isdddss", $shop_id, $visit_date, $amount, $amount_1, $amount_2, $received_from, $received_by);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Visit logged successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
