<?php
header('Content-Type: application/json');
include '../config.php';

$donor_id = $_GET['donor_id'] ?? '';

if (empty($donor_id)) {
    echo json_encode(['success' => false, 'message' => 'Donor ID is required']);
    exit;
}

$query = "
    SELECT 
        do.*,
        r.address as restaurant_address,
        r.discount_percentage
    FROM donor_offers do
    LEFT JOIN restaurants r ON do.restaurant_name = r.name
    WHERE do.donor_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$offers = [];

while ($row = $result->fetch_assoc()) {
    $offers[] = [
        'restaurant_name' => $row['restaurant_name'],
        'status' => $row['status'],
        'redeemed_at' => $row['redeemed_at'],
        'offer_type' => $row['offer_type'],
        'offer_value' => $row['offer_value']
    ];
}

$donorQuery = $conn->prepare("SELECT name, voucher_id FROM donors WHERE id = ?");
$donorQuery->bind_param("i", $donor_id);
$donorQuery->execute();
$donor = $donorQuery->get_result()->fetch_assoc();

echo json_encode([
    'success' => true,
    'donor' => $donor,
    'offers' => $offers
]);

$stmt->close();
$conn->close();
?>
