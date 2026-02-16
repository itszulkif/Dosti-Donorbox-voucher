<?php
header('Content-Type: application/json');
include '../config.php';

// Fetch all restaurants and their redemption counts
$query = "
    SELECT 
        r.name as restaurant_name,
        COUNT(CASE WHEN do.status = 'Redeemed' THEN 1 END) as redemption_count
    FROM 
        restaurants r
    LEFT JOIN 
        donor_offers do ON r.name = do.restaurant_name
    GROUP BY 
        r.id
    ORDER BY 
        redemption_count DESC
";

$result = $conn->query($query);
$data = [];
$totalRedemptions = 0;

// Colors for the chart/bars
$colors = [
    '#2563eb', // blue-600
    '#3b82f6', // blue-500
    '#60a5fa', // blue-400
    '#93c5fd', // blue-300
    '#eabb30', // yellow-500 (highlight)
    '#10b981', // green-500
    '#8b5cf6', // violet-500
    '#ec4899', // pink-500
];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $count = (int)$row['redemption_count'];
        $totalRedemptions += $count;
        $data[] = [
            'name' => $row['restaurant_name'],
            'count' => $count,
            // We'll calculate percentage properly below
        ];
    }
}

// Assign colors and calculate percentages
foreach ($data as $index => &$item) {
    $item['percent'] = ($totalRedemptions > 0) ? round(($item['count'] / $totalRedemptions) * 100, 1) : 0;
    $item['color'] = $colors[$index % count($colors)];
}

echo json_encode($data);
?>
