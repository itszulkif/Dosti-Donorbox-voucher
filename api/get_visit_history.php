<?php
header('Content-Type: application/json');
include '../config.php';

$shop_id = $_GET['shop_id'] ?? '';

if (empty($shop_id)) {
    echo json_encode(['success' => false, 'message' => 'Shop ID is required']);
    exit;
}

$stmt = $conn->prepare("SELECT shop_name, box_number, installation_date FROM donation_shops WHERE id = ?");
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$shop_result = $stmt->get_result();
$shop_data = $shop_result->fetch_assoc();
$stmt->close();

if (!$shop_data) {
    echo json_encode(['success' => false, 'message' => 'Shop not found']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM donation_visits WHERE shop_id = ? ORDER BY visit_date DESC");
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();

$visits = [];
$grand_total = 0;
while($row = $result->fetch_assoc()) {
    $visits[] = $row;
    $grand_total += $row['amount'];
}

echo json_encode([
    'success' => true,
    'visits' => $visits,
    'shop' => $shop_data,
    'grand_total' => $grand_total
]);

$stmt->close();
$conn->close();
?>
