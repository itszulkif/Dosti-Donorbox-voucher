<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in first.']);
    exit;
}

if (!isset($_GET['voucher_id']) || empty($_GET['voucher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Voucher ID is required']);
    exit;
}

$voucher_id = $_GET['voucher_id'];

// Check if voucher exists (Exact Match)
$stmt = $conn->prepare("SELECT id, name, status FROM donors WHERE voucher_id = ?");
$stmt->bind_param("s", $voucher_id);
$stmt->execute();
$result = $stmt->get_result();

// Fallback: If not found, try with 'DV-' prefix
if ($result->num_rows === 0 && !str_starts_with($voucher_id, 'DV-')) {
    $prefixed_id = 'DV-'. $voucher_id;
    $stmt = $conn->prepare("SELECT id, name, status FROM donors WHERE voucher_id = ?");
    $stmt->bind_param("s", $prefixed_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Voucher not found']);
    exit;
}

$donor = $result->fetch_assoc();
$donor_id = $donor['id'];

// Check if logged in as partner
$partner_id = $_SESSION['partner_id'] ?? null;
$is_partner = !empty($partner_id);
$partner_restaurant_name = null;

if ($is_partner) {
    // Get partner's restaurant name
    $restStmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
    $restStmt->bind_param("i", $partner_id);
    $restStmt->execute();
    $restResult = $restStmt->get_result();
    if ($restRow = $restResult->fetch_assoc()) {
        $partner_restaurant_name = $restRow['name'];
    }
    $restStmt->close();
}

// Check assignment security for partners
if ($is_partner && $partner_restaurant_name) {
    // Check if this voucher is assigned to ANY restaurant
    $checkAssignStmt = $conn->prepare("SELECT id FROM donor_offers WHERE donor_id = ?");
    $checkAssignStmt->bind_param("i", $donor_id);
    $checkAssignStmt->execute();
    $checkAssignRes = $checkAssignStmt->get_result();
    
    if ($checkAssignRes->num_rows > 0) {
        // It is assigned. Now check if it's assigned to THIS partner.
        $strictStmt = $conn->prepare("SELECT id FROM donor_offers WHERE donor_id = ? AND restaurant_name = ?");
        $strictStmt->bind_param("is", $donor_id, $partner_restaurant_name);
        $strictStmt->execute();
        $strictRes = $strictStmt->get_result();
        
        if ($strictRes->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: This voucher belongs to another partner.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'This voucher is not assigned to any partner yet.']);
        exit;
    }
}

// Fetch assignments - filter if partner
if ($is_partner && $partner_restaurant_name) {
    // Partner: Only show their restaurant assignment
    $assignStmt = $conn->prepare("SELECT restaurant_name, status, redeemed_at FROM donor_offers WHERE donor_id = ? AND restaurant_name = ?");
    $assignStmt->bind_param("is", $donor_id, $partner_restaurant_name);
} else {
    // Regular admin: Show all assignments
    $assignStmt = $conn->prepare("SELECT restaurant_name, status, redeemed_at FROM donor_offers WHERE donor_id = ?");
    $assignStmt->bind_param("i", $donor_id);
}

$assignStmt->execute();
$assignResult = $assignStmt->get_result();
$assignments = [];
while($row = $assignResult->fetch_assoc()) {
    $assignments[] = $row;
}
$assignStmt->close();

echo json_encode([
    'success' => true,
    'data' => [
        'id' => $donor_id,
        'name' => $donor['name'],
        'status' => $donor['status'],
        'assignments' => $assignments,
        'is_partner' => $is_partner
    ]
]);

$stmt->close();
$conn->close();
?>
