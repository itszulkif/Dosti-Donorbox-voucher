<?php
include __DIR__ . '/../config.php';

header('Content-Type: application/json');

$period = $_GET['period'] ?? 'daily';
$data = [];

switch ($period) {
    case 'weekly':
        // Last 8 weeks
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = date('Y-m-d', strtotime("-$i weeks Monday this week"));
            $weekEnd = date('Y-m-d', strtotime("-$i weeks Sunday this week"));
            $query = "SELECT COUNT(*) as count FROM voucher_usage WHERE used_at BETWEEN '$weekStart 00:00:00' AND '$weekEnd 23:59:59'";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            $data[] = [
                'label' => 'Week ' . date('W', strtotime($weekStart)),
                'date' => $weekStart,
                'count' => (int)$row['count']
            ];
        }
        break;

    case 'monthly':
        // Last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));
            $query = "SELECT COUNT(*) as count FROM voucher_usage WHERE DATE_FORMAT(used_at, '%Y-%m') = '$month'";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            $data[] = [
                'label' => $monthName,
                'count' => (int)$row['count']
            ];
        }
        break;

    case 'daily':
    default:
        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $query = "SELECT COUNT(*) as count FROM voucher_usage WHERE DATE(used_at) = '$date'";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            $data[] = [
                'label' => date('D', strtotime($date)), // Day name like Mon, Tue
                'full_date' => $date,
                'count' => (int)$row['count']
            ];
        }
        break;
}

echo json_encode($data);
?>
