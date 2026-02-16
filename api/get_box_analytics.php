<?php
header('Content-Type: application/json');
include '../config.php';

// 1. Total Collection
$totalRes = $conn->query("SELECT SUM(amount) as total FROM donation_visits");
$total = $totalRes->fetch_assoc()['total'] ?? 0;

// 2. Top Performing Shops
$topShopsRes = $conn->query("
    SELECT s.shop_name, SUM(v.amount) as total_amount 
    FROM donation_shops s 
    LEFT JOIN donation_visits v ON s.id = v.shop_id 
    GROUP BY s.id 
    ORDER BY total_amount DESC 
    LIMIT 5
");
$topShops = [];
while($row = $topShopsRes->fetch_assoc()) {
    $topShops[] = $row;
}

// 3. Trends (Dynamic based on filter)
$filter = $_GET['filter'] ?? 'monthly';
$trendQuery = "";

if ($filter === 'daily') {
    // Last 30 Days
    $trendQuery = "
        SELECT DATE_FORMAT(visit_date, '%d %b') as label, SUM(amount) as total 
        FROM donation_visits 
        WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(visit_date) 
        ORDER BY visit_date ASC
    ";
} elseif ($filter === 'weekly') {
    // Last 12 Weeks
    $trendQuery = "
        SELECT CONCAT('W', WEEK(visit_date), ' ', DATE_FORMAT(visit_date, '%Y')) as label, SUM(amount) as total 
        FROM donation_visits 
        WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
        GROUP BY YEARWEEK(visit_date) 
        ORDER BY visit_date ASC
    ";
} else {
    // Monthly (Default - Last 12 Months)
    $trendQuery = "
        SELECT DATE_FORMAT(visit_date, '%b %Y') as label, SUM(amount) as total 
        FROM donation_visits 
        WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(visit_date, '%Y-%m') 
        ORDER BY visit_date ASC
    ";
}

$trendsRes = $conn->query($trendQuery);
$trends = [];
while($row = $trendsRes->fetch_assoc()) {
    $trends[] = $row;
}

// 4. Visit Frequency (Enhanced with Search, Pagination, and Filters)
$search = $_GET['q'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$timeframe = $_GET['tf'] ?? 'all'; // all, today, week, month
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];
$types = "";

if ($search) {
    $whereClauses[] = "(s.shop_name LIKE ? OR s.box_number LIKE ?)";
    $likeSearch = "%$search%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $types .= "ss";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Filter for visits timeframe in the HAVING clause to maintain absolute last_visit accuracy
$havingClause = "";
if ($timeframe === 'today') {
    $havingClause = "HAVING last_visit = CURDATE()";
} elseif ($timeframe === 'week') {
    $havingClause = "HAVING last_visit >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($timeframe === 'month') {
    $havingClause = "HAVING last_visit >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

// Count total matching records involves a subquery now because of HAVING
$countSql = "
    SELECT COUNT(*) as total FROM (
        SELECT s.id, MAX(v.visit_date) as last_visit 
        FROM donation_shops s 
        LEFT JOIN donation_visits v ON s.id = v.shop_id 
        $whereSql
        GROUP BY s.id
        $havingClause
    ) as subquery
";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
$countStmt->close();

// Main frequency query - Always fetch absolute MAX(visit_date)
$freqSql = "
    SELECT 
        s.shop_name, 
        s.box_number,
        MAX(v.visit_date) as last_visit, 
        DATEDIFF(CURDATE(), MAX(v.visit_date)) as days_ago 
    FROM donation_shops s 
    LEFT JOIN donation_visits v ON s.id = v.shop_id 
    $whereSql
    GROUP BY s.id 
    $havingClause
    ORDER BY last_visit ASC
    LIMIT ? OFFSET ?
";

$freqStmt = $conn->prepare($freqSql);
$freqParams = array_merge($params, [$limit, $offset]);
$freqTypes = $types . "ii";
$freqStmt->bind_param($freqTypes, ...$freqParams);
$freqStmt->execute();
$frequencyRes = $freqStmt->get_result();

$frequency = [];
while($row = $frequencyRes->fetch_assoc()) {
    $frequency[] = $row;
}
$freqStmt->close();

echo json_encode([
    'success' => true,
    'total_collection' => $total,
    'top_shops' => $topShops,
    'monthly_trends' => $trends,
    'visit_frequency' => $frequency,
    'pagination' => [
        'total' => $totalRecords,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($totalRecords / max(1, $limit))
    ]
]);

$conn->close();
?>
