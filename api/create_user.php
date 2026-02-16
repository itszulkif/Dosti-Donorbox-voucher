<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

// Only Super Admins can create users
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Only Super Admins can create users.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    
    // Validate required fields
    if (empty($full_name) || empty($username) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields except designation are required.']);
        exit;
    }
    
    // Validate role
    $valid_roles = ['super_admin', 'voucher_editor', 'box_editor'];
    if (!in_array($role, $valid_roles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
        exit;
    }
    
    // Validate password strength (minimum 6 characters)
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit;
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Create user (use provided full name for email or a clean username-based one)
    $clean_name = preg_replace('/[^a-z0-9]/', '', strtolower($username));
    $email = $clean_name . '@dostiwelfare.org';
    
    $stmt = $conn->prepare("INSERT INTO admins (full_name, designation, username, email, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $designation, $username, $email, $password_hash, $role);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'user_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
