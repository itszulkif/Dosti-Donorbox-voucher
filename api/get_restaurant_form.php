<?php
include '../config.php';

$id = $_GET['id'] ?? null;
$mode = $id ? 'Edit Partner' : 'Add New Partner';
$restaurant = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $restaurant = $stmt->get_result()->fetch_assoc();
}

$name = $restaurant['name'] ?? '';
$address = $restaurant['address'] ?? '';
$discount = $restaurant['discount_percentage'] ?? 0;
$price = $restaurant['custom_price'] ?? 0;
$offerMode = $price > 0 ? 'price' : 'discount';
?>

<div class="p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight"><?php echo $mode; ?></h3>
            <p class="text-gray-500 text-sm font-medium"><?php echo $id ? "Record ID: #".$id : "Complete the details to register a new partner"; ?></p>
        </div>
        <button onclick="closeModal('modal-container')" class="p-2 text-gray-400 hover:text-red-500 transition-colors bg-gray-50 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <form id="partner-form" class="space-y-6">
        <?php if ($id): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Restaurant Name</label>
            <input type="text" name="restaurant_name" value="<?php echo htmlspecialchars($name); ?>" required placeholder="e.g. Savour Foods"
                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
        </div>

        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Address / Branch</label>
            <textarea name="restaurant_address" rows="2" placeholder="Full Address of the location" 
                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-medium text-gray-700"><?php echo htmlspecialchars($address); ?></textarea>
        </div>

        <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100/50 space-y-4">
            <label class="block text-[10px] font-black text-blue-900 uppercase tracking-widest px-1">Offer Configuration</label>
            
            <div class="grid grid-cols-2 gap-4">
                <label class="cursor-pointer group">
                    <input type="radio" name="offer_mode" value="discount" <?php echo $offerMode == 'discount' ? 'checked' : ''; ?> class="hidden peer">
                    <div class="p-4 bg-white border-2 border-transparent peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-2xl text-center transition-all">
                        <span class="block text-xs font-black text-gray-400 peer-checked:text-blue-600 uppercase">Discount %</span>
                    </div>
                </label>
                <label class="cursor-pointer group">
                    <input type="radio" name="offer_mode" value="price" <?php echo $offerMode == 'price' ? 'checked' : ''; ?> class="hidden peer">
                    <div class="p-4 bg-white border-2 border-transparent peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-2xl text-center transition-all">
                        <span class="block text-xs font-black text-gray-400 peer-checked:text-blue-600 uppercase">Fixed Price</span>
                    </div>
                </label>
            </div>

            <div id="modal-input-discount" class="<?php echo $offerMode == 'price' ? 'hidden' : ''; ?>">
                <div class="relative">
                    <input type="number" name="discount_percentage" value="<?php echo $discount; ?>" placeholder="0" 
                        class="w-full pl-5 pr-12 py-3.5 bg-white border border-gray-200 rounded-2xl focus:border-blue-500 outline-none font-black text-gray-800">
                    <span class="absolute right-5 top-1/2 -translate-y-1/2 font-black text-gray-400">%</span>
                </div>
            </div>

            <div id="modal-input-price" class="<?php echo $offerMode == 'discount' ? 'hidden' : ''; ?>">
                <div class="relative">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400">Rs.</span>
                    <input type="number" name="custom_price" value="<?php echo $price; ?>" placeholder="0" 
                        class="w-full pl-14 pr-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:border-blue-500 outline-none font-black text-gray-800">
                </div>
            </div>
        </div>

        <?php if (!$id): ?>
        <!-- Partner Login Credentials (only for new partners) -->
        <div class="bg-yellow-50/50 p-6 rounded-3xl border border-yellow-100/50 space-y-4">
            <label class="block text-[10px] font-black text-yellow-900 uppercase tracking-widest px-1">Partner Login Credentials</label>
            <p class="text-xs text-yellow-700 font-medium px-1 -mt-2">These credentials will be used by the partner to log in and redeem vouchers.</p>
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Username</label>
                <input type="text" name="partner_username" required placeholder="e.g. meltingspots"
                    class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:border-yellow-500 outline-none transition-all font-bold text-gray-700">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Password</label>
                <input type="password" name="partner_password" required placeholder="Create a secure password"
                    class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:border-yellow-500 outline-none transition-all font-bold text-gray-700">
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95">
                <?php echo $id ? 'Update Record' : 'Register Partner'; ?>
            </button>
        </div>
    </form>
</div>

<script>
(function() {
    const radios = document.querySelectorAll('#partner-form input[name="offer_mode"]');
    const discDiv = document.getElementById('modal-input-discount');
    const priceDiv = document.getElementById('modal-input-price');
    
    radios.forEach(r => {
        r.addEventListener('change', (e) => {
            if (e.target.value === 'discount') {
                discDiv.classList.remove('hidden');
                priceDiv.classList.add('hidden');
                priceDiv.querySelector('input').value = '0';
            } else {
                discDiv.classList.add('hidden');
                priceDiv.classList.remove('hidden');
                discDiv.querySelector('input').value = '0';
            }
        });
    });
})();
</script>
