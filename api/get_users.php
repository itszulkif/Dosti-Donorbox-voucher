<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

// Only Super Admins can view users
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get all users including restaurant partners
$stmt = $conn->prepare("
    SELECT 
        a.id, 
        a.full_name, 
        a.designation, 
        a.username, 
        a.role, 
        a.restaurant_id,
        a.created_at,
        r.name as restaurant_name
    FROM admins a
    LEFT JOIN restaurants r ON a.restaurant_id = r.id
    ORDER BY a.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['success' => true, 'users' => $users]);
$stmt->close();
$conn->close();
?>
