<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$box_number = $_POST['box_number'] ?? '';
$shop_name = $_POST['shop_name'] ?? '';
$installation_date = $_POST['installation_date'] ?? '';
$contact_person = $_POST['contact_person'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$email = $_POST['email'] ?? '';

// Validate required fields
if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Shop ID is required']);
    exit;
}

if (empty($box_number) || empty($shop_name)) {
    echo json_encode(['success' => false, 'message' => 'Box Number and Shop Name are required']);
    exit;
}

try {
    // Update existing shop record
    $stmt = $conn->prepare("UPDATE donation_shops SET box_number=?, shop_name=?, email=?, installation_date=?, contact_person=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param("sssssssi", $box_number, $shop_name, $email, $installation_date, $contact_person, $phone, $address, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Shop updated successfully']);
        } else {
            // No rows affected could mean ID doesn't exist or no changes made
            echo json_encode(['success' => true, 'message' => 'No changes detected']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
