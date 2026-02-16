<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin', 'box_editor']);

include_once __DIR__ . '/../config.php';
?>
<div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="text-center">
        <h1 class="text-4xl font-black text-gray-800 tracking-tight">Log Box Collection</h1>
        <p class="text-gray-500 font-medium mt-2">Record a new donation visit and amount collected</p>
    </div>

    <!-- Main Logic Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 p-8 md:p-12 border border-gray-100">
        <form id="visitForm" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Box Number Search -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4 px-1">Enter Box Number</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <svg class="w-6 h-6 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="box_number_input" name="box_number" required placeholder="Type Box Number (e.g. BOX-101)..." 
                            class="w-full pl-16 pr-8 py-5 bg-gray-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white rounded-3xl text-lg font-bold text-gray-800 outline-none transition-all shadow-sm">
                    </div>
                    <!-- Display found shop name -->
                    <div id="shop_display" class="mt-4 px-8 py-4 bg-blue-50/50 rounded-2xl hidden animate-in slide-in-from-top-2">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                            <p class="text-sm font-bold text-blue-900">Registered Shop: <span id="shop_name_span" class="text-lg ml-1">---</span></p>
                        </div>
                    </div>
                </div>

                <!-- Date & Amounts -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-1">Collection Date</label>
                        <input type="date" name="visit_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white rounded-2xl font-bold text-gray-700 outline-none transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-1">Amount 1 (PKR)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 font-black text-gray-400 text-xs">Rs.</span>
                                <input type="number" name="amount_1" value="0" step="0.01" class="amount-input w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white rounded-2xl font-black text-gray-800 outline-none transition-all text-xl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-1">Amount 2 (PKR)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 font-black text-gray-400 text-xs">Rs.</span>
                                <input type="number" name="amount_2" value="0" step="0.01" class="amount-input w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white rounded-2xl font-black text-gray-800 outline-none transition-all text-xl">
                            </div>
                        </div>
                    </div>
                    <!-- Total display -->
                    <div class="px-6 py-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 relative overflow-hidden">
                        <div class="flex justify-between items-center relative z-10">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Collection</span>
                            <div class="flex items-center gap-3">
                                <!-- Status Indicator -->
                                <div id="amount_status_badge" class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-black uppercase tracking-wider scale-0 transition-transform duration-300">
                                    Mismatched
                                </div>
                                <span class="text-2xl font-black text-blue-600">Rs. <span id="total_amount_display">0.00</span></span>
                            </div>
                        </div>
                        <input type="hidden" name="amount" id="total_amount_input" value="0">
                        <!-- Progress layer -->
                        <div id="amount_match_overlay" class="absolute inset-0 bg-green-50 z-0 opacity-0 transition-opacity duration-300"></div>
                    </div>
                </div>

                <!-- Persons Involved -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-1">Receiver Details (Shop Rep)</label>
                        <input type="text" name="received_from" placeholder="Name of Shopkeeper" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white rounded-2xl font-bold text-gray-700 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-1">Collected By (NGO Staff)</label>
                        <input type="text" name="received_by" placeholder="Your Name" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500/20 focus:bg-white rounded-2xl font-bold text-gray-700 outline-none transition-all">
                    </div>
                </div>
            </div>

            <button type="submit" id="submit_btn" disabled class="w-full py-6 mt-6 bg-gray-200 text-gray-400 font-black text-xl rounded-3xl shadow-xl transition-all cursor-not-allowed">
                Confirm & Log Visit
            </button>
        </form>
    </div>

    <!-- Recent Logs Mini-Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-bold text-gray-800">Recently Logged Visits</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50/50 text-[10px] uppercase tracking-widest text-gray-400 font-bold">
                    <tr>
                        <th class="px-6 py-4">Shop Name</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $logs = $conn->query("SELECT v.*, s.shop_name FROM donation_visits v JOIN donation_shops s ON v.shop_id = s.id ORDER BY v.created_at DESC LIMIT 5");
                    while($log = $logs->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="px-6 py-4 font-bold text-gray-700"><?php echo htmlspecialchars($log['shop_name']); ?></td>
                        <td class="px-6 py-4 text-gray-500"><?php echo date('M d, Y', strtotime($log['visit_date'])); ?></td>
                        <td class="px-6 py-4 font-black text-blue-600">Rs. <?php echo number_format($log['amount']); ?></td>
                        <td class="px-6 py-4 text-gray-400"><?php echo htmlspecialchars($log['received_by']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($logs->num_rows == 0): ?>
                                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No recent visits recorded.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

const boxInput = document.getElementById('box_number_input');
const shopDisplay = document.getElementById('shop_display');
const shopNameSpan = document.getElementById('shop_name_span');
const submitBtn = document.getElementById('submit_btn');
const amountInputs = document.querySelectorAll('.amount-input');
const totalDisplay = document.getElementById('total_amount_display');
const totalInput = document.getElementById('total_amount_input');

// Handle Box Number Search
boxInput.addEventListener('input', debounce(async (e) => {
    const boxNum = e.target.value.trim();
    if (boxNum.length < 1) {
        hideShop();
        return;
    }

    try {
        const res = await fetch(`api/find_box.php?box_number=${encodeURIComponent(boxNum)}`);
        const data = await res.json();
        if (data.success) {
            showShop(data.shop_name);
        } else {
            hideShop();
        }
    } catch (err) {
        console.error(err);
        hideShop();
    }
}, 500));

// Calculate Total Amount & Verify Matching
amountInputs.forEach(input => {
    input.addEventListener('input', () => {
        const val1 = parseFloat(amountInputs[0].value) || 0;
        const val2 = parseFloat(amountInputs[1].value) || 0;
        
        totalDisplay.innerText = val1.toLocaleString('en-US', { minimumFractionDigits: 2 });
        totalInput.value = val1;
        
        validateSequence();
    });
});

function validateSequence() {
    const val1 = parseFloat(amountInputs[0].value) || 0;
    const val2 = parseFloat(amountInputs[1].value) || 0;
    const badge = document.getElementById('amount_status_badge');
    const overlay = document.getElementById('amount_match_overlay');
    const hasShop = !shopDisplay.classList.contains('hidden');

    if (val1 > 0 && val1 === val2) {
        // MATCH
        badge.innerText = "Verified Match";
        badge.classList.remove('bg-red-100', 'text-red-600', 'scale-0');
        badge.classList.add('bg-green-100', 'text-green-600', 'scale-100');
        overlay.classList.add('opacity-100');
        
        if (hasShop) enableSubmit();
        else disableSubmit();
    } else {
        // MISMATCH or ZERO
        if (val1 === 0 && val2 === 0) {
            badge.classList.add('scale-0');
        } else {
            badge.innerText = val1 === val2 ? "Zero Amount" : "Mismatched";
            badge.classList.remove('bg-green-100', 'text-green-600', 'scale-0');
            badge.classList.add('bg-red-100', 'text-red-600', 'scale-100');
        }
        overlay.classList.remove('opacity-100');
        disableSubmit();
    }
}

function enableSubmit() {
    submitBtn.disabled = false;
    submitBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
    submitBtn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'hover:shadow-2xl');
}

function disableSubmit() {
    submitBtn.disabled = true;
    submitBtn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
    submitBtn.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'hover:shadow-2xl');
}

// Update show/hide shop to include validation check
function showShop(name) {
    shopNameSpan.innerText = name;
    shopDisplay.classList.remove('hidden');
    validateSequence();
}

function hideShop() {
    shopDisplay.classList.add('hidden');
    disableSubmit();
}

document.getElementById('visitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const btn = document.getElementById('submit_btn');
    btn.disabled = true;
    btn.innerText = 'Processing...';

    try {
        const res = await fetch('api/save_visit.php', { method: 'POST', body: formData });
        const data = await res.json();
        if(data.success) {
            alert('Collection Logged Successfully!');
            window.location.reload(); 
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerText = 'Confirm & Log Visit';
        }
    } catch (err) {
        console.error(err);
        btn.disabled = false;
        btn.innerText = 'Confirm & Log Visit';
    }
});
</script>
