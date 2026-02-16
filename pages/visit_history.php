<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php';

// Fetch all visits with shop details
$query = "
    SELECT v.*, s.shop_name, s.box_number 
    FROM donation_visits v 
    JOIN donation_shops s ON v.shop_id = s.id 
    ORDER BY v.visit_date DESC, v.created_at DESC
";
$result = $conn->query($query);
?>

<div class="space-y-6">
    <!-- Action Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-2 animate-in fade-in slide-in-from-top-4 duration-500">
        <!-- Search -->
        <div class="relative w-full md:w-96 group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="visit-search" placeholder="Search by shop, date, or staff..." 
                class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm shadow-blue-900/5 font-medium text-gray-700">
        </div>

        <!-- Export & Tools -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative inline-block text-left w-full md:w-auto overflow-visible">
                <button id="export-dropdown-btn" class="w-full md:w-auto inline-flex justify-center items-center gap-2 px-6 py-3.5 bg-blue-900 text-white font-bold rounded-2xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Report
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <!-- Dropdown Menu -->
                <div id="export-dropdown-menu" class="export-dropdown-menu hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 z-[100] animate-fade-in overflow-hidden border border-gray-100">
                    <div class="p-2">
                        <p class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Export Selected Items</p>
                        <button onclick="exportData('xlsx', 'visit-table', true)" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Selected (Excel)
                        </button>
                        <button onclick="exportData('pdf', 'visit-table', true)" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export Selected (PDF)
                        </button>
                        
                        <div class="h-px bg-gray-100 my-2"></div>
                        
                        <p class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Export Full Report</p>
                        <button onclick="exportData('xlsx', 'visit-table', false)" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export All (Excel)
                        </button>
                        <button onclick="exportData('csv', 'visit-table', false)" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                            Export All (CSV)
                        </button>
                        <button onclick="exportData('pdf', 'visit-table', false)" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export All (PDF)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="glass-panel overflow-hidden border border-gray-100 shadow-xl shadow-blue-900/5 animate-in fade-in duration-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table id="visit-table" class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-blue-900 text-white text-[11px] uppercase tracking-widest font-black">
                        <th class="px-8 py-5 text-left">
                            <input type="checkbox" onchange="toggleSelectAll('visit-table', this)" class="w-4 h-4 rounded border-white/20 bg-white/10 text-yellow-400 focus:ring-0 cursor-pointer transition-all">
                        </th>
                        <th class="px-4 py-5 font-bold">Shop & Box</th>
                        <th class="px-4 py-5 font-bold text-center">Visit Date</th>
                        <th class="px-4 py-5 font-bold text-right">Amount Collected</th>
                        <th class="px-4 py-5 font-bold">Collection Details</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="visit-tbody" class="divide-y divide-gray-100 text-sm">
                    <?php if ($result->num_rows > 0): $count = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="rich-table-row group bg-white hover:bg-blue-50/50 transition-all duration-300">
                                <td class="px-8 py-5">
                                    <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer transition-all">
                                </td>
                                <td class="px-4 py-5">
                                    <div class="font-bold text-gray-800 text-base"><?php echo htmlspecialchars($row['shop_name']); ?></div>
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[10px] font-black uppercase tracking-tighter mt-1">
                                        <?php echo htmlspecialchars($row['box_number']); ?>
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <div class="font-bold text-gray-700"><?php echo date('M d, Y', strtotime($row['visit_date'])); ?></div>
                                    <div class="text-[10px] text-gray-400"><?php echo date('H:i A', strtotime($row['created_at'])); ?></div>
                                </td>
                                <td class="px-4 py-5 text-right">
                                    <span class="text-xl font-black text-blue-900 tracking-tight">
                                        Rs. <?php echo number_format($row['amount'], 2); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-xs text-gray-500 mb-1">From: <b class="text-gray-700"><?php echo htmlspecialchars($row['received_from'] ?: 'Unknown'); ?></b></div>
                                    <div class="text-xs text-gray-500 italic">By: <?php echo htmlspecialchars($row['received_by'] ?: 'System'); ?></div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <button onclick="editVisit(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="Edit Visit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button onclick="deleteVisit(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="Delete Visit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center text-gray-400">
                                No collection visits recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Rows per page</span>
                <select id="rows-per-page-visit" class="bg-white border border-gray-200 text-sm rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-gray-700">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            
            <div id="pagination-info-visit" class="text-sm font-bold text-gray-500">
                Found <span id="total-results-visit" class="text-blue-600"><?php echo $result->num_rows; ?></span> collections
            </div>

            <div class="flex items-center gap-2" id="pagination-controls-visit">
                <!-- Dynamic Pagination -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        initTable('visit-table', 'visit-search', 'rows-per-page-visit', 'pagination-controls-visit');
    });
</script>
