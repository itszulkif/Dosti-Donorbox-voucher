<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php';

// Fetch all restaurants
$result = $conn->query("SELECT * FROM restaurants ORDER BY created_at DESC");
?>

<div class="space-y-6">
    <!-- Action Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-2 animate-in fade-in slide-in-from-top-4 duration-500">
        <!-- Search -->
        <div class="relative w-full md:w-96 group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="restaurant-search" placeholder="Search by name, address, or offer..." 
                class="w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm shadow-blue-900/5 font-medium text-gray-700">
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Export Report Dropdown -->
            <div class="relative inline-block text-left w-auto overflow-visible">
                <button class="export-dropdown-btn inline-flex justify-center items-center gap-2 px-6 py-3.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-2xl hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export
                </button>
                <div class="export-dropdown-menu hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 z-[100] animate-fade-in overflow-hidden border border-gray-100">
                    <div class="py-1">
                        <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">Export All</div>
                        <button onclick="exportData('xlsx', 'restaurant-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel (.xlsx)
                        </button>
                        <button onclick="exportData('csv', 'restaurant-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                            CSV (.csv)
                        </button>
                        <button onclick="exportData('pdf', 'restaurant-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            PDF (.pdf)
                        </button>
                        <button onclick="printTable('restaurant-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print List
                        </button>
                        
                        <div class="px-4 py-2 mt-1 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 border-t">Export Selected</div>
                         <button onclick="exportData('xlsx', 'restaurant-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Excel Selected
                        </button>
                         <button onclick="exportData('csv', 'restaurant-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V7m0 0H18a2 2 0 01-2-2V3"></path></svg>
                            CSV Selected
                        </button>
                        <button onclick="exportData('pdf', 'restaurant-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            PDF Selected
                        </button>
                        <button onclick="printTable('restaurant-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
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
            <table id="restaurant-table" class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-blue-900 text-white text-[11px] uppercase tracking-widest font-black">
                        <th class="px-6 py-5 w-16">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" onchange="toggleSelectAll('restaurant-table', this)" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </div>
                        </th>
                        <th class="px-4 py-5 w-16">#</th>
                        <th class="px-4 py-5">Partner Name</th>
                        <th class="px-4 py-5">Address / Branch</th>
                        <th class="px-4 py-5 text-center">Offer Type</th>
                        <th class="px-4 py-5 text-right w-40">Value</th>
                        <th class="px-8 py-5 text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody id="restaurant-tbody" class="divide-y divide-gray-100 text-sm">
                    <?php if ($result->num_rows > 0): $count = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="rich-table-row group bg-white hover:bg-blue-50/50 transition-all duration-300" data-restaurant-id="<?php echo (int)$row['id']; ?>">
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-gray-400 font-mono text-xs"><?php echo $count++; ?></td>
                                <td class="px-4 py-5 font-bold text-gray-800 text-base">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </td>
                                <td class="px-4 py-5 text-gray-500 font-medium">
                                    <?php echo htmlspecialchars($row['address'] ?: 'Main Branch'); ?>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <?php if ($row['discount_percentage'] > 0): ?>
                                        <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black rounded-full uppercase tracking-wider border border-green-100">Discount</span>
                                    <?php elseif ($row['custom_price'] > 0): ?>
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-full uppercase tracking-wider border border-blue-100">Flat Rate</span>
                                    <?php else: ?>
                                        <span class="text-gray-300">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-5 text-right">
                                    <?php if ($row['discount_percentage'] > 0): ?>
                                        <span class="text-xl font-black text-green-600 tracking-tighter"><?php echo $row['discount_percentage']; ?>%</span>
                                    <?php elseif ($row['custom_price'] > 0): ?>
                                        <span class="text-xl font-black text-blue-900 tracking-tighter">Rs. <?php echo number_format($row['custom_price']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-300">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="editRestaurant(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="Edit Partner">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button onclick="deleteRestaurant(<?php echo $row['id']; ?>)" class="p-2.5 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-xl shadow-sm border border-gray-100 transition-all active:scale-95" title="Delete Partner">
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
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-800">No Partners Registered</h4>
                                    <p class="text-gray-400 text-sm">Grow your network by adding your first partner restaurant.</p>
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
                <select id="rows-per-page-rest" class="bg-white border border-gray-200 text-sm rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-gray-700">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            
            <div id="pagination-info-rest" class="text-sm font-bold text-gray-500">
                Found <span id="total-results-rest" class="text-blue-600"><?php echo $result->num_rows; ?></span> partners
            </div>

            <div class="flex items-center gap-2" id="pagination-controls-rest">
                <!-- Dynamic Pagination -->
            </div>
        </div>
    </div>
</div>
