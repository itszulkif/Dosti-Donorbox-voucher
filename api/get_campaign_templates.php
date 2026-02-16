<?php
header('Content-Type: application/json');
include '../config.php';

$sql = "SELECT * FROM campaign_templates ORDER BY created_at DESC";
$result = $conn->query($sql);

$templates = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $templates[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'templates' => $templates
]);

$conn->close();
?>
