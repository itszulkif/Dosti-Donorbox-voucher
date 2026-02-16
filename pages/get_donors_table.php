<?php
include __DIR__ . '/../config.php';

$result = $conn->query("SELECT * FROM donors ORDER BY created_at DESC LIMIT 50");
?>
<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr class="bg-gray-100 text-gray-600">
                <th class="p-3 font-semibold">Name</th>
                <th class="p-3 font-semibold">Voucher</th>
                <th class="p-3 font-semibold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-3">
                    <div class="font-medium"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['phone']); ?></div>
                </td>
                <td class="p-3 font-mono text-blue-600"><?php echo htmlspecialchars($row['voucher_id']); ?></td>
                <td class="p-3">
                    <span class="px-2 py-1 text-xs rounded-full <?php echo $row['status'] == 'Active' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
