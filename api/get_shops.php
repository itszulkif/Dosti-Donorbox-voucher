<?php
include '../config.php';

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM donation_shops WHERE DATE(created_at) = CURDATE()";

if (!empty($search)) {
    $searchTerm = "%" . $conn->real_escape_string($search) . "%";
    $sql .= " AND (shop_name LIKE '$searchTerm' OR box_number LIKE '$searchTerm' OR contact_person LIKE '$searchTerm' OR address LIKE '$searchTerm')";
}

$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($shop = $result->fetch_assoc()) {
        $box_suffix = substr($shop['box_number'], -3);
        $shop_name_safe = htmlspecialchars($shop['shop_name']);
        $shop_name_js = addslashes($shop['shop_name']);
        $box_number_safe = htmlspecialchars($shop['box_number']);
        $contact_safe = htmlspecialchars($shop['contact_person'] ?: '-');
        $phone_safe = htmlspecialchars($shop['phone'] ?: '-');
        
        $address_col = '';
        if (isset($_GET['type']) && $_GET['type'] === 'full') {
            $address_safe = htmlspecialchars($shop['address'] ?: '-');
            $address_col = <<<HTML
            <td class="px-6 py-5">
                <div class="text-xs text-gray-500 max-w-[200px] truncate" title="{$address_safe}">
                    {$address_safe}
                </div>
            </td>
HTML;
        }

        echo <<<HTML
        <tr class="rich-table-row group bg-white hover:bg-blue-50/30 transition-all duration-300">
            <td class="px-6 py-5">
                <div class="flex items-center justify-center">
                    <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                </div>
            </td>
            <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs">
                        {$box_suffix}
                    </div>
                    <div>
                        <div class="font-bold text-gray-800">{$shop_name_safe}</div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                            <!-- {$box_number_safe} -->
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-5">
                <div class="text-sm font-bold text-gray-700">{$contact_safe}</div>
                <div class="text-xs text-gray-400">{$phone_safe}</div>
            </td>
            {$address_col}
            <td class="px-6 py-5">
                <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-green-100">Live</span>
            </td>
            <td class="px-6 py-5 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="viewShopHistory({$shop['id']})" class="p-2 bg-white text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-100 shadow-sm transition-all" title="View Collection Log">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542-7z"></path></svg>
                    </button>
                    <button onclick="editShopFromList({$shop['id']})" class="p-2 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Edit Shop">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button onclick="if(confirm('Delete this shop?')) { fetch(`api/delete_shop.php?id={$shop['id']}`).then(()=>window.location.reload()); }" class="p-2 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Delete Shop">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </td>
        </tr>
HTML;
    }
} else {
    echo '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">No shops found matching your search.</td></tr>';
}
$conn->close();
?>
