<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$voucher_id = $_POST['voucher_id'] ?? '';

// Check if logged in as partner
$partner_id = $_SESSION['partner_id'] ?? null;
$is_partner = !empty($partner_id);

// Determine restaurant to use
if ($is_partner) {
    // Partner: Automatically use their restaurant
    $restStmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
    $restStmt->bind_param("i", $partner_id);
    $restStmt->execute();
    $restResult = $restStmt->get_result();
    
    if ($restResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Partner restaurant not found']);
        exit;
    }
    
    $restaurant_id = $restResult->fetch_assoc()['name'];
    $restStmt->close();
} else {
    // Regular admin: Use provided restaurant
    $restaurant_id = $_POST['restaurant_id'] ?? '';
}

if (empty($voucher_id) || empty($restaurant_id)) {
    echo json_encode(['success' => false, 'message' => 'Voucher ID and Restaurant are required']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // 1. Get donor ID and check status
    $stmt = $conn->prepare("SELECT id, status FROM donors WHERE voucher_id = ?");
    $stmt->bind_param("s", $voucher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Voucher not found');
    }

    $donor = $result->fetch_assoc();

    if ($donor['status'] === 'Redeemed') {
        throw new Exception('Voucher has already been redeemed');
    }

    if ($donor['status'] !== 'Active') {
        throw new Exception('Voucher is not active');
    }

    $donor_id = $donor['id'];

    // 2. Check if the voucher is assigned to THIS restaurant and is Pending
    $offerStmt = $conn->prepare("SELECT status FROM donor_offers WHERE donor_id = ? AND restaurant_name = ?");
    $offerStmt->bind_param("is", $donor_id, $restaurant_id);
    $offerStmt->execute();
    $offerRes = $offerStmt->get_result();

    if ($offerRes->num_rows === 0) {
        throw new Exception('This voucher is not valid for this location.');
    }

    $offer = $offerRes->fetch_assoc();
    if ($offer['status'] === 'Redeemed') {
        throw new Exception('This voucher has already been redeemed here.');
    }

    // 3. Update status for THIS restaurant ONLY
    $updateStmt = $conn->prepare("UPDATE donor_offers SET status = 'Redeemed', redeemed_at = NOW() WHERE donor_id = ? AND restaurant_name = ?");
    $updateStmt->bind_param("is", $donor_id, $restaurant_id);
    $updateStmt->execute();

    // 4. Record voucher usage for history
    $restaurants_map = [
        'Melting Spots' => 'MS',
        'District 9' => 'D9',
        'Green Olive' => 'GO'
    ];
    $restaurant_code = $restaurants_map[$restaurant_id] ?? 'GO';

    $usageStmt = $conn->prepare("INSERT INTO voucher_usage (donor_id, restaurant) VALUES (?, ?)");
    $usageStmt->bind_param("is", $donor_id, $restaurant_code);
    $usageStmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Voucher Successfully Redeemed at ' . $restaurant_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
