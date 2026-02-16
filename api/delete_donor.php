<?php
include '../config.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

try {
    $conn->begin_transaction();

    // Delete offers first (foreign key or logical relationship)
    $stmt1 = $conn->prepare("DELETE FROM donor_offers WHERE donor_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    // Delete usage
    $stmt2 = $conn->prepare("DELETE FROM voucher_usage WHERE donor_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    // Delete donor
    $stmt3 = $conn->prepare("DELETE FROM donors WHERE id = ?");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
