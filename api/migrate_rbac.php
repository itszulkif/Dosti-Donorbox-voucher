<?php
session_start();
header('Content-Type: application/json');
include '../config.php';

// Only allow super admins to run migrations
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Only Super Admins can run migrations.']);
    exit;
}

try {
    $migrations = [];
    
    // 1. Add full_name column if it doesn't exist
    $cols = $conn->query("SHOW COLUMNS FROM admins LIKE 'full_name'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD COLUMN full_name VARCHAR(255) DEFAULT NULL AFTER id");
        $migrations[] = "Added 'full_name' column to admins table";
    }
    
    // 2. Add designation column if it doesn't exist
    $cols = $conn->query("SHOW COLUMNS FROM admins LIKE 'designation'");
    if ($cols->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD COLUMN designation VARCHAR(100) DEFAULT NULL AFTER full_name");
        $migrations[] = "Added 'designation' column to admins table";
    }
    
    // 3. Update role ENUM to include new role types
    $result = $conn->query("SHOW COLUMNS FROM admins LIKE 'role'");
    if ($result && $row = $result->fetch_assoc()) {
        $type = $row['Type'];
        // Check if new roles are already in the ENUM
        if (strpos($type, 'voucher_editor') === false || strpos($type, 'box_editor') === false) {
            $conn->query("ALTER TABLE admins MODIFY COLUMN role ENUM('super_admin', 'admin', 'voucher_editor', 'box_editor') DEFAULT 'admin'");
            $migrations[] = "Updated 'role' ENUM to include 'voucher_editor' and 'box_editor'";
        }
    }
    
    // 4. Set default full_name for existing admin (use username as fallback)
    $conn->query("UPDATE admins SET full_name = CONCAT(UPPER(SUBSTRING(username, 1, 1)), SUBSTRING(username, 2)) WHERE full_name IS NULL");
    $migrations[] = "Set default full_name for existing admins";
    
    // 5. Set default designation for existing super_admin
    $conn->query("UPDATE admins SET designation = 'System Administrator' WHERE role = 'super_admin' AND designation IS NULL");
    $migrations[] = "Set default designation for super admins";
    
    echo json_encode([
        'success' => true, 
        'message' => 'RBAC migration completed successfully',
        'migrations' => $migrations
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Migration failed: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
