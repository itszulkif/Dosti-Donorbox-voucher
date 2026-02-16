<?php
include '../config.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid ID']);
    exit;
}

// donor_offers table does NOT have a restaurant_id column; it stores restaurant_name.
// We first fetch the restaurant name by ID, then remove any donor_offers rows that use that name.
try {
    $conn->begin_transaction();

    // Get restaurant name for this ID
    $nameStmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
    $nameStmt->bind_param("i", $id);
    $nameStmt->execute();
    $nameResult = $nameStmt->get_result();
    $restaurant = $nameResult->fetch_assoc();
    $nameStmt->close();

    if ($restaurant) {
        $restName = $restaurant['name'];

        // Delete any donor_offers linked by restaurant name
        $offerStmt = $conn->prepare("DELETE FROM donor_offers WHERE restaurant_name = ?");
        $offerStmt->bind_param("s", $restName);
        $offerStmt->execute();
        $offerStmt->close();

        // Delete associated admin credentials
        $adminStmt = $conn->prepare("DELETE FROM admins WHERE restaurant_id = ?");
        $adminStmt->bind_param("i", $id);
        $adminStmt->execute();
        $adminStmt->close();
    }

    // Delete the restaurant itself
    $stmt2 = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
}
