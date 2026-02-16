<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $login_type = $_POST['login_type'] ?? 'admin'; // 'admin' or 'partner'

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both username and password.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, username, password_hash, role, restaurant_id, full_name, designation FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Enforce Login Isolation
        $is_partner_account = !empty($row['restaurant_id']);
        
        if ($login_type === 'partner' && !$is_partner_account) {
            echo json_encode(['success' => false, 'message' => 'This account is not a partner account.']);
            exit;
        }
        
        if ($login_type === 'admin' && $is_partner_account) {
            echo json_encode(['success' => false, 'message' => 'Partner accounts must use the partner login page.']);
            exit;
        }

        if (password_verify($password, $row['password_hash'])) {
            // Login Success
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_role'] = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['designation'] = $row['designation'];
            $_SESSION['partner_id'] = $row['restaurant_id']; // NULL for regular admins, ID for partners
            
            // For older scripts still using restaurant_id
            $_SESSION['restaurant_id'] = $row['restaurant_id'];
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
