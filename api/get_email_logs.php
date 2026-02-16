<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$type = isset($_GET['type']) ? $_GET['type'] : null;
$campaign_id = isset($_GET['campaign_id']) ? (int)$_GET['campaign_id'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build WHERE clause for filters
$whereConditions = [];
$params = [];
$types = '';

if ($type) {
    $whereConditions[] = 'email_type = ?';
    $params[] = $type;
    $types .= 's';
}

if ($campaign_id) {
    $whereConditions[] = 'campaign_id = ?';
    $params[] = $campaign_id;
    $types .= 'i';
}

if ($search) {
    $whereConditions[] = '(recipient_email LIKE ? OR recipient_name LIKE ? OR subject LIKE ?)';
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

$whereClause = count($whereConditions) > 0 ? ' WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM email_logs" . $whereClause;
if (count($params) > 0) {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();
} else {
    $total = $conn->query($countQuery)->fetch_assoc()['total'];
}

$totalPages = ceil($total / $limit);

// Fetch paginated logs
$query = "SELECT id, recipient_email, recipient_name, subject, status, email_type, sent_at, error_message, body, campaign_id FROM email_logs" . $whereClause . " ORDER BY sent_at DESC LIMIT ? OFFSET ?";

if (count($params) > 0) {
    $stmt = $conn->prepare($query);
    $types .= 'ii';
    $params[] = $limit;
    $params[] = $offset;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode([
    'success' => true,
    'logs' => $logs,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_records' => $total,
        'per_page' => $limit
    ]
]);

$stmt->close();
$conn->close();
?>
