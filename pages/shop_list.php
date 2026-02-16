<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php'; ?>
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">All Donation Shops</h1>
            <p class="text-gray-500 font-medium">Complete registry of all donation box locations</p>
        </div>
        <button onclick="window.location.href='index.php?page=donation_boxes'" class="flex items-center gap-2 px-5 py-2.5 bg-white text-gray-600 font-bold rounded-xl shadow-sm border border-gray-200 hover:bg-gray-50 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Dashboard
        </button>
    </div>

    <!-- Full Shop List -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Master List</h3>
            <div class="flex items-center gap-4">
                <div class="relative inline-block text-left w-auto overflow-visible z-[10]">
                    <button class="export-dropdown-btn inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all shadow-sm active:scale-95" onclick="document.getElementById('shop-export-menu').classList.toggle('hidden')">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="shop-export-menu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 animate-fade-in overflow-hidden border border-gray-100">
                         <div class="py-1">
                            <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">Export All</div>
                            <button onclick="exportData('xlsx', 'shop-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Excel
                            </button>
                             <button onclick="exportData('csv', 'shop-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                                CSV
                            </button>
                             <button onclick="printTable('shop-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Print
                            </button>
                            <div class="px-4 py-2 mt-1 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 border-t">Export Selected</div>
                            <button onclick="exportData('xlsx', 'shop-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Excel
                            </button>
                             <button onclick="exportData('csv', 'shop-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                                CSV
                            </button>
                             <button onclick="printTable('shop-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Print
                            </button>
                        </div>
                    </div>
                </div>
                <div class="relative w-64">
                    <input type="text" id="masterShopSearch" placeholder="Search..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table id="shop-table" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-900 text-white text-[10px] uppercase tracking-widest font-black">
                        <th class="px-6 py-5 rounded-tl-2xl w-16">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" onchange="toggleSelectAll('shop-table', this)" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </div>
                        </th>
                        <th class="px-6 py-5">Box & Shop</th>
                        <th class="px-6 py-5">Contact Details</th>
                        <th class="px-6 py-5">Address</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right rounded-tr-2xl">Actions</th>
                    </tr>
                </thead>
                <tbody id="masterShopTableBody" class="divide-y divide-gray-50">
                    <?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
                    $shops = $conn->query("SELECT * FROM donation_shops ORDER BY created_at DESC");
                    while($shop = $shops->fetch_assoc()):
                    ?>
                    <tr class="rich-table-row group bg-white hover:bg-blue-50/30 transition-all duration-300">
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs">
                                    <?php echo substr($shop['box_number'], -3); ?>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800"><?php echo htmlspecialchars($shop['shop_name']); ?></div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                                        <!-- <?php echo htmlspecialchars($shop['box_number']); ?> -->
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-gray-700"><?php echo htmlspecialchars($shop['contact_person'] ?: '-'); ?></div>
                            <div class="text-xs text-gray-400"><?php echo htmlspecialchars($shop['phone'] ?: '-'); ?></div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-xs text-gray-500 max-w-[200px] truncate" title="<?php echo htmlspecialchars($shop['address']); ?>">
                                <?php echo htmlspecialchars($shop['address'] ?: '-'); ?>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-green-100">Live</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="viewShopHistory(<?php echo $shop['id']; ?>)" class="p-2 bg-white text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-100 shadow-sm transition-all" title="View Collection Log">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542-7z"></path></svg>
                                </button>
                                <button onclick="editShopFromList(<?php echo $shop['id']; ?>)" class="p-2 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Edit Shop">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button onclick="if(confirm('Delete this shop?')) { fetch(`api/delete_shop.php?id=<?php echo $shop['id']; ?>`).then(()=>window.location.reload()); }" class="p-2 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Delete Shop">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Visit History Slide-over -->
<div id="visitHistoryModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeVisitHistory()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl animate-in slide-in-from-right duration-500">
        <div class="flex flex-col h-full">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800" id="histShopName">Visit History</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400 uppercase font-bold tracking-widest" id="histBoxNumber">BOX-000</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-xs text-gray-400 font-medium" id="histDate">Issued: -</span>
                    </div>
                </div>
                <button onclick="closeVisitHistory()" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Grand Total Card -->
            <div class="px-6 pt-6">
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-white shadow-lg shadow-blue-600/20">
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-1">Total Collected</p>
                    <h3 class="text-3xl font-black tracking-tight" id="histGrandTotal">Rs. 0</h3>
                </div>
            </div>

            <div id="visitList" class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Data loaded via JS -->
            </div>
        </div>
    </div>
</div>

<script>
// Reuse the API for search
let masterSearchTimeout = null;
const masterSearch = document.getElementById('masterShopSearch');
const masterTableBody = document.getElementById('masterShopTableBody');

masterSearch.addEventListener('input', function(e) {
    clearTimeout(masterSearchTimeout);
    const term = e.target.value.trim();
    
    // Skeleton
    masterTableBody.innerHTML = `<tr><td colspan="5" class="p-8 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div></td></tr>`;

    masterSearchTimeout = setTimeout(async () => {
        try {
            // We need to modify get_shops or handle the different column count (address added here)
            // api/get_shops.php returns 4 columns. This table has 5. 
            // I should stick to the structure or update API to be generic.
            // For speed, let's just use the API but note the columns.
            // Actually, the API returns full TRs. 
            // The API return format is hardcoded <tr>...</tr> with 4 TDs.
            // This new page has 5 TDs (Address included).
            // To properly support this without breaking the other page, I should likely make a new API `api/get_all_shops_rows.php` or `api/get_shops.php?full=true`.
            // Let's create a new API logic or Client Side filtering if list is small? 
            // The user wants "Functional search filters". 
            // Given the requirement, I'll update `get_shops.php` to support a `type=full` param to include address column.
            
            const res = await fetch(`api/get_shops.php?search=${encodeURIComponent(term)}&type=full`);
            const html = await res.text();
            masterTableBody.innerHTML = html;
        } catch (err) {
            console.error(err);
        }
    }, 400);
});

async function viewShopHistory(shopId) {
    const modal = document.getElementById('visitHistoryModal');
    const list = document.getElementById('visitList');
    
    // Reset contents
    document.getElementById('histShopName').innerText = 'Loading...';
    document.getElementById('histBoxNumber').innerText = '...';
    document.getElementById('histDate').innerText = 'Issued: ...';
    document.getElementById('histGrandTotal').innerText = 'Rs. ...';
    
    modal.classList.remove('hidden');
    list.innerHTML = '<div class="text-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div></div>';

    try {
        const res = await fetch(`api/get_visit_history.php?shop_id=${shopId}`);
        const data = await res.json();
        
        if(data.success) {
            // Update Header Info
            document.getElementById('histShopName').innerText = data.shop.shop_name;
            document.getElementById('histBoxNumber').innerText = data.shop.box_number;
            
            // Format Date
            const date = new Date(data.shop.installation_date);
            document.getElementById('histDate').innerText = 'Issued: ' + date.toLocaleDateString('en-GB', {
                day: 'numeric', month: 'short', year: 'numeric'
            });

            // Update Grand Total
            document.getElementById('histGrandTotal').innerText = 'Rs. ' + parseFloat(data.grand_total).toLocaleString();

            // Setup List
            if(data.visits.length === 0) {
                list.innerHTML = '<div class="text-center py-10 text-gray-400">No collections recorded yet.</div>';
                return;
            }
            
            list.innerHTML = data.visits.map(v => `
                <div class="relative pl-8 pb-8 border-l-2 border-blue-50 last:pb-0 font-medium">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-blue-600 border-4 border-white"></div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">${v.visit_date}</p>
                            <span class="text-blue-600 font-black">Rs. ${parseFloat(v.amount).toLocaleString()}</span>
                        </div>
                        <div class="text-sm text-gray-700">Received from: <b>${v.received_from}</b></div>
                        <div class="text-xs text-gray-400 mt-1 italic">Collected by: ${v.received_by}</div>
                    </div>
                </div>
            `).join('');
        }
    } catch (err) {
        console.error(err);
        list.innerHTML = '<div class="text-center py-10 text-red-500">Failed to load history</div>';
    }
}

function closeVisitHistory() {
    document.getElementById('visitHistoryModal').classList.add('hidden');
}

</script>
