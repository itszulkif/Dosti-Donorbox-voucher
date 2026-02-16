<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php'; ?>
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Shops & Boxes</h1>
            <p class="text-gray-500 font-medium">Register and manage donation box locations</p>
        </div>
        <!-- Register Button Removed as per request -->
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $totalShops = $conn->query("SELECT COUNT(*) as count FROM donation_shops")->fetch_assoc()['count'];
        $recentShops = $conn->query("SELECT COUNT(*) as count FROM donation_shops WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
        ?>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Shops</p>
                <h3 class="text-2xl font-black text-gray-800"><?php echo $totalShops; ?></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">New (30 Days)</p>
                <h3 class="text-2xl font-black text-gray-800"><?php echo $recentShops; ?></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Shop Form -->
        <div id="add-shop-form" class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sticky top-24">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="form-title" class="text-xl font-bold text-gray-800">Register Location</h3>
                    <button id="cancel-edit" onclick="resetForm()" class="hidden text-xs font-bold text-red-500 uppercase tracking-wider hover:bg-red-50 px-2 py-1 rounded-lg transition-colors">Cancel Edit</button>
                </div>
                <form id="shopForm" class="space-y-4">
                    <input type="hidden" name="id" id="shop_id">
                    <div id="form-error" class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert"></div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Box Number</label>
                        <input type="text" name="box_number" required placeholder="e.g. BOX-101" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Shop Name</label>
                        <input type="text" name="shop_name" required placeholder="Full Name of Shop" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Email Address <span class="text-[10px] lowercase font-normal opacity-70">(Optional)</span></label>
                        <input type="email" name="email" placeholder="e.g. shop@example.com" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Installation Date</label>
                        <input type="date" name="installation_date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Contact Person</label>
                            <input type="text" name="contact_person" placeholder="Name" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Phone</label>
                            <input type="text" name="phone" placeholder="Contact No" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Address</label>
                        <textarea name="address" rows="2" placeholder="Full Address" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all"></textarea>
                    </div>
                    <button type="submit" class="w-full py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all active:scale-95">
                        Register Location
                    </button>
                </form>
            </div>
        </div>

        <!-- Shop List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800">Today's Registered Locations</h3>
                    <div class="flex gap-2">
                        <!-- Export Dropdown -->
                        <div class="relative inline-block text-left w-auto overflow-visible z-[10]">
                            <button class="inline-flex justify-center items-center gap-2 px-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all shadow-sm h-10" onclick="document.getElementById('boxes-export-menu').classList.toggle('hidden')">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </button>
                            <div id="boxes-export-menu" class="hidden absolute right-0 mt-2 w-48 rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 animate-fade-in overflow-hidden border border-gray-100">
                                 <div class="py-1">
                                    <button onclick="exportData('xlsx', 'shop-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">Excel</button>
                                     <button onclick="exportData('csv', 'shop-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">CSV</button>
                                     <button onclick="printTable('shop-table')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">Print</button>
                                    <div class="px-4 py-2 mt-1 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 border-t">Selected</div>
                                    <button onclick="exportData('xlsx', 'shop-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">Excel</button>
                                     <button onclick="exportData('csv', 'shop-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">CSV</button>
                                     <button onclick="printTable('shop-table', true)" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">Print</button>
                                </div>
                            </div>
                        </div>

                        <div class="relative flex-1">
                            <input type="text" id="shopSearch" placeholder="Search recent..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none h-10">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button onclick="window.location.href='index.php?page=shop_list'" class="px-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm h-10" title="View Full List">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table id="shop-table" class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-blue-900 text-white text-[10px] uppercase tracking-widest font-black">
                                <th class="px-6 py-5 w-16">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" onchange="toggleSelectAll('shop-table', this)" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </div>
                                </th>
                                <th class="px-6 py-5">Box & Shop</th>
                                <th class="px-6 py-5">Contact</th>
                                <th class="px-6 py-5">Status</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="shopTableBody" class="divide-y divide-gray-50">
                            <?php
                            $shops = $conn->query("SELECT * FROM donation_shops WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC");
                            while($shop = $shops->fetch_assoc()):
                            ?>
                            <tr class="rich-table-row group bg-white hover:bg-blue-50/30 transition-all duration-300">
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </div>
                                </td>
                                <td class="px-6 py-5" onclick="viewVisitHistory(<?php echo $shop['id']; ?>, '<?php echo addslashes($shop['shop_name']); ?>')">
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
                                <td class="px-6 py-5" onclick="viewVisitHistory(<?php echo $shop['id']; ?>, '<?php echo addslashes($shop['shop_name']); ?>')">
                                    <div class="text-sm font-bold text-gray-700"><?php echo htmlspecialchars($shop['contact_person'] ?: '-'); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo htmlspecialchars($shop['phone'] ?: '-'); ?></div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-green-100">Live</span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="editShopFromList(<?php echo $shop['id']; ?>)" class="p-2 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Edit Shop">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button onclick="deleteShop(<?php echo $shop['id']; ?>)" class="p-2 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Delete Shop">
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
    </div>
</div>

<!-- Visit History Slide-over (Hidden by default) -->
<div id="visitHistoryModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeVisitHistory()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl animate-in slide-in-from-right duration-500">
        <div class="flex flex-col h-full">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800" id="histShopName">Visit History</h3>
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-widest mt-1">Collection Timeline</p>
                </div>
                <button onclick="closeVisitHistory()" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="visitList" class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Data loaded via JS -->
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('shopForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const btn = e.target.querySelector('button');
    const errorDiv = document.getElementById('form-error');
    
    // Reset error
    errorDiv.classList.add('hidden');
    errorDiv.innerText = '';
    
    btn.disabled = true;
    btn.innerText = 'Registering...';

    try {
        const res = await fetch('api/save_shop.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        if(data.success) {
            // Check email status from save_shop.php response
            if (data.email_status === 'Sent Successfully') {
                showToast('✅ Shop Registered & Email Sent!');
            } else if (data.email_status === 'Not Sent') {
                showToast('✅ Shop Registered Successfully!');
            } else if (data.email_status && data.email_status.startsWith('Failed')) {
                showToast('⚠️ Shop Registered, but Email Failed');
            } else {
                showToast('✅ Shop Registered Successfully!');
            }
            
            // Clean up
            resetForm(); 
            fetchShops(); 
        } else {
            errorDiv.innerText = data.message || 'Error saving shop';
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        console.error(err);
        errorDiv.innerText = 'Error: ' + err.message;
        errorDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Register Location';
    }
});

async function viewVisitHistory(shopId, shopName) {
    document.getElementById('histShopName').innerText = shopName;
    document.getElementById('visitHistoryModal').classList.remove('hidden');
    const list = document.getElementById('visitList');
    list.innerHTML = '<div class="text-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div></div>';

    try {
        const res = await fetch(`api/get_visit_history.php?shop_id=${shopId}`);
        const data = await res.json();
        if(data.success) {
            if(data.visits.length === 0) {
                list.innerHTML = '<div class="text-center py-10 text-gray-400">No visits recorded yet.</div>';
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
    }
}

function closeVisitHistory() {
    document.getElementById('visitHistoryModal').classList.add('hidden');
}


// Delete Shop Function
function deleteShop(id) {
    if(confirm('Are you sure you want to delete this shop?')) {
        fetch(`api/delete_shop.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to delete shop');
                }
            })
            .catch(err => console.error(err));
    }
}

// Reset Form Function
function resetForm() {
    const form = document.getElementById('shopForm');
    form.reset();
    form.querySelector('[name="id"]').value = '';
    
    document.getElementById('form-title').innerText = 'Register Location';
    document.getElementById('cancel-edit').classList.add('hidden');
    form.querySelector('button[type="submit"]').innerText = 'Save Registration';
    document.querySelector('#add-shop-form > div').classList.remove('ring-2', 'ring-blue-500');
}

// Dynamic Search & Sort
let searchTimeout = null;
const searchInput = document.getElementById('shopSearch');
const tableBody = document.getElementById('shopTableBody');

searchInput.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const term = e.target.value.trim();
    
    // Show skeleton/loading state
    tableBody.innerHTML = `
        <tr>
            <td colspan="4" class="p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
            </td>
        </tr>`;

    searchTimeout = setTimeout(() => {
        fetchShops(term);
    }, 400); // Debounce
});

async function fetchShops(search = '') {
    try {
        const res = await fetch(`api/get_shops.php?search=${encodeURIComponent(search)}`);
        const html = await res.text();
        tableBody.innerHTML = html;
    } catch (err) {
        console.error(err);
        tableBody.innerHTML = '<tr><td colspan="4" class="text-center p-4 text-red-500">Failed to load data</td></tr>';
    }
}
</script>
