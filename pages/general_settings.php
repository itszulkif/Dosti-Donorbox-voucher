<?php
// This is included in settings.php
?>
<div class="space-y-6">
    <div class="glass-panel p-8">
        <h4 class="text-xl font-bold text-gray-800 mb-2">Voucher Configuration</h4>
        <p class="text-sm text-gray-500 mb-8 border-b border-gray-100 pb-4">Manage the base value of vouchers throughout the system.</p>

        <form id="general-settings-form" class="space-y-6 max-w-md">
            <!-- Voucher Price -->
            <div class="space-y-2">
                <label class="text-sm font-black text-gray-700 uppercase tracking-widest">Base Voucher Price (PKR)</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-400 font-bold">Rs.</span>
                    </div>
                    <input type="number" id="voucher_price" name="voucher_price" step="0.01" 
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-xl text-blue-900 shadow-inner"
                        placeholder="500.00">
                </div>
                <p class="text-[10px] text-gray-400 font-medium italic">All new donor entries will automatically use this value.</p>
            </div>

            <div class="pt-4 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-3.5 bg-blue-900 text-white font-black rounded-2xl hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/20 active:scale-95 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="p-6 bg-yellow-50/50 border border-yellow-100 rounded-2xl flex gap-4">
        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <h5 class="font-bold text-yellow-800 mb-1">Impact on Analytics</h5>
            <p class="text-sm text-yellow-700 leading-relaxed opacity-80">Changing the price only affects <b>new</b> records. Historical data remains accurate according to the price at the time of entry.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Load current settings
    fetch('api/get_settings.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.settings.voucher_price) {
                document.getElementById('voucher_price').value = data.settings.voucher_price;
            }
        });

    // Handle form submission
    document.getElementById('general-settings-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const price = document.getElementById('voucher_price').value;
        
        const formData = new FormData();
        formData.append('setting_key', 'voucher_price');
        formData.append('setting_value', price);

        fetch('api/update_settings.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Settings saved successfully!', 'success');
            } else {
                alert('Error: ' + data.message);
            }
        });
    });
});

function showToast(message, type) {
    // Using a simple alert for now, but keeping the toast function signature for future UI polish
    alert(message);
}
</script>
