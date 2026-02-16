<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['restaurant_name'] ?? '';
    $address = $_POST['restaurant_address'] ?? '';
    $discount = $_POST['discount_percentage'] ?? 0;
    $price = $_POST['custom_price'] ?? 0;
    $partner_username = $_POST['partner_username'] ?? '';
    $partner_password = $_POST['partner_password'] ?? '';

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Restaurant name is required.']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Create restaurant
        $stmt = $conn->prepare("INSERT INTO restaurants (name, address, discount_percentage, custom_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $name, $address, $discount, $price);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create restaurant.');
        }
        
        $restaurant_id = $conn->insert_id;
        $stmt->close();

        // 2. Create partner login (if credentials provided)
        if (!empty($partner_username) && !empty($partner_password)) {
            // Check if username already exists
            $checkStmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
            $checkStmt->bind_param("s", $partner_username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                throw new Exception('Username already exists. Please choose a different username.');
            }
            $checkStmt->close();

            // Hash password and create admin account
            $password_hash = password_hash($partner_password, PASSWORD_BCRYPT);
            $email = $partner_username . '@partner.local';
            $role = 'admin';

            $adminStmt = $conn->prepare("INSERT INTO admins (full_name, username, email, password_hash, role, restaurant_id) VALUES (?, ?, ?, ?, ?, ?)");
            $adminStmt->bind_param("sssssi", $name, $partner_username, $email, $password_hash, $role, $restaurant_id);
            
            if (!$adminStmt->execute()) {
                throw new Exception('Failed to create partner login.');
            }
            $adminStmt->close();
        }

        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
