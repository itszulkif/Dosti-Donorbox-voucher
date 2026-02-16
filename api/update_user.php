<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

// Only Super Admins can update users
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($id) || empty($full_name) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }
    
    // Validate role
    $valid_roles = ['super_admin', 'voucher_editor', 'box_editor'];
    if (!in_array($role, $valid_roles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid role.']);
        exit;
    }

    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admins SET full_name = ?, designation = ?, role = ?, password_hash = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $full_name, $designation, $role, $hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET full_name = ?, designation = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $designation, $role, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
