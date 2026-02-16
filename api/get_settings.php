<?php
header('Content-Type: application/json');
include '../config.php';

$result = $conn->query("SELECT setting_key, setting_value FROM system_settings");
$settings = [];

while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

echo json_encode(['success' => true, 'settings' => $settings]);
?>
