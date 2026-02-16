<?php
header('Content-Type: application/json');
include '../config.php';

$box_number = $_GET['box_number'] ?? '';

if (empty($box_number)) {
    echo json_encode(['success' => false, 'message' => 'Box Number is required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, shop_name FROM donation_shops WHERE box_number = ?");
$stmt->bind_param("s", $box_number);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'shop_id' => $row['id'],
        'shop_name' => $row['shop_name']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Box Number not found'
    ]);
}

$stmt->close();
$conn->close();
?>
