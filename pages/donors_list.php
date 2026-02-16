<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php';

// Fetch all donors and their restaurant usage
$query = "
    SELECT 
        d.*, 
        GROUP_CONCAT(CONCAT(do.restaurant_name, '|||', do.status, '|||', IFNULL(do.redeemed_at, '')) SEPARATOR '###') as assignments
    FROM donors d
    LEFT JOIN donor_offers do ON d.id = do.donor_id
    GROUP BY d.id
    ORDER BY d.created_at DESC
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
            <input type="text" id="donor-search" placeholder="Search by name, voucher, or contact..." 
                class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm shadow-blue-900/5 font-medium text-gray-700">
        </div>

        <!-- Export & Tools -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative inline-block text-left w-full md:w-auto overflow-visible">
                <button id="export-dropdown-btn" class="export-dropdown-btn w-full md:w-auto inline-flex justify-center items-center gap-2 px-6 py-3.5 bg-blue-900 text-white font-bold rounded-2xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Report
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <!-- Dropdown Menu -->
                <div id="export-dropdown-menu" class="export-dropdown-menu hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 z-[100] animate-fade-in overflow-hidden border border-gray-100">
                    <div class="py-1">
                        <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">Export All</div>
                        <button onclick="exportData('xlsx', 'donor-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel
                        </button>
                        <button onclick="exportData('csv', 'donor-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                            Export CSV
                        </button>
                        <button onclick="exportData('pdf', 'donor-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export PDF
                        </button>
                        <button onclick="printTable('donor-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print All
                        </button>
                        
                        <div class="px-4 py-2 mt-1 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 border-t">Export Selected</div>
                         <button onclick="exportData('xlsx', 'donor-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel Selected
                        </button>
                         <button onclick="exportData('csv', 'donor-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                            CSV Selected
                        </button>
                        <button onclick="exportData('pdf', 'donor-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            PDF Selected
                        </button>
                        <button onclick="printTable('donor-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="glass-panel overflow-hidden border border-gray-100 shadow-xl shadow-blue-900/5 animate-in fade-in duration-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table id="donor-table" class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-blue-900 text-white text-[11px] uppercase tracking-widest font-black">
                        <th class="px-6 py-5 w-16">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" onchange="toggleSelectAll('donor-table', this)" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </div>
                        </th>
                        <th class="px-4 py-5 w-16">#</th>
                        <th class="px-4 py-5 font-bold">Donor Details</th>
                        <th class="px-4 py-5 font-bold">Contact Info</th>
                        <th class="px-4 py-5 font-bold">Voucher ID</th>
                        <th class="px-4 py-5 font-bold text-center">Offer Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="donor-tbody" class="divide-y divide-gray-100 text-sm">
                    <?php if ($result->num_rows > 0): $count = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $assignments = !empty($row['assignments']) ? explode('###', $row['assignments']) : [];
                        ?>
                            <tr class="rich-table-row group bg-white hover:bg-blue-50/50 transition-all duration-300">
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-gray-400 font-mono text-xs"><?php echo $count++; ?></td>
                                <td class="px-4 py-5">
                                    <div class="font-bold text-gray-800 text-base"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div class="text-[11px] font-medium text-gray-400 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg text-xs font-bold border border-gray-100 group-hover:bg-white transition-colors">
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <?php echo htmlspecialchars($row['phone']); ?>
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <span class="font-black text-blue-600 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100 group-hover:bg-white transition-colors">
                                        <?php echo htmlspecialchars($row['voucher_id']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="flex items-center justify-center gap-3">
                                        <?php 
                                        foreach ($assignments as $assign):
                                            $parts = explode('|||', $assign);
                                            $r_name = $parts[0] ?? 'N/A';
                                            $r_status = $parts[1] ?? 'Pending';
                                            $redeemed_at = $parts[2] ?? '';
                                            $is_redeemed = ($r_status === 'Redeemed');
                                            
                                            $tooltip_content = $is_redeemed 
                                                ? 'Used on: ' . date('d M Y', strtotime($redeemed_at)) . ' | Time: ' . date('H:i', strtotime($redeemed_at))
                                                : 'Status: Pending Redemption';
                                        ?>
                                            <div class="flex flex-col items-center min-w-[80px]">
                                                <span class="text-[10px] font-bold text-gray-500 mb-1.5 whitespace-nowrap px-2 py-0.5 bg-gray-100 rounded-md"><?php echo htmlspecialchars($r_name); ?></span>
                                                <?php if ($is_redeemed): ?>
                                                    <div class="w-8 h-8 rounded-xl bg-green-500 text-white flex items-center justify-center shadow-lg shadow-green-500/20 cursor-pointer tippy-tooltip" data-tippy-content="<?php echo $tooltip_content; ?>">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded-xl bg-gray-200 text-gray-500 flex items-center justify-center border border-gray-300 shadow-sm cursor-help tippy-tooltip" data-tippy-content="<?php echo $tooltip_content; ?>">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <button onclick="viewDonorDetails(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-blue-900 hover:bg-blue-900 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <button onclick="editDonor(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="Edit Donor">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button onclick="deleteDonor(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="Delete Donor">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-200 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-800">No Donors Found</h4>
                                    <p class="text-gray-400 text-sm">Start by adding your first donor to the database.</p>
                                </div>
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
                <select id="rows-per-page" class="bg-white border border-gray-200 text-sm rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-gray-700">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            
            <div id="pagination-info" class="text-sm font-bold text-gray-500">
                Found <span id="total-results" class="text-blue-600"><?php echo $result->num_rows; ?></span> entries
            </div>

            <div class="flex items-center gap-2" id="pagination-controls">
                <!-- Dynamic Pagination -->
            </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initTable('donor-table', 'donor-search', 'rows-per-page', 'pagination-controls');
        
        // Initialize Tippy
        tippy('.tippy-tooltip', {
            animation: 'shift-away',
            theme: 'light',
            arrow: true
        });
    });
</script>
        </div>
    </div>
</div>
