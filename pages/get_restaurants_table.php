<?php
include __DIR__ . '/../config.php';

$result = $conn->query("SELECT * FROM restaurants ORDER BY created_at DESC");
?>
<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr class="bg-yellow-50 text-yellow-800">
                <th class="p-3 font-semibold">Restaurant</th>
                <th class="p-3 font-semibold">Offer</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-3">
                    <div class="font-medium"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['address']); ?></div>
                </td>
                <td class="p-3">
                    <?php if ($row['discount_percentage'] > 0): ?>
                        <span class="text-green-600 font-bold"><?php echo $row['discount_percentage']; ?>% Off</span>
                    <?php elseif ($row['custom_price'] > 0): ?>
                        <span class="text-blue-600 font-bold">Rs. <?php echo number_format($row['custom_price']); ?></span>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
