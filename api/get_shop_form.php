<?php
include '../config.php';
$id = $_GET['id'] ?? null;
$shop = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM donation_shops WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $shop = $stmt->get_result()->fetch_assoc();
}
?>
<div class="bg-white p-8 rounded-3xl max-w-lg mx-auto relative">
    <button onclick="closeModal('modal-container')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
    
    <h3 class="text-2xl font-black text-gray-800 mb-2"><?php echo $shop ? 'Edit Location' : 'Register Location'; ?></h3>
    <p class="text-sm text-gray-500 font-medium mb-6"><?php echo $shop ? 'Update details for this donation box.' : 'Add a new donation box location.'; ?></p>

    <form id="modal-shop-form" class="space-y-4 text-left">
        <input type="hidden" name="id" value="<?php echo $shop['id'] ?? ''; ?>">
        
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Box Number</label>
            <input type="text" name="box_number" required value="<?php echo htmlspecialchars($shop['box_number'] ?? ''); ?>" placeholder="e.g. BOX-101" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Shop Name</label>
            <input type="text" name="shop_name" required value="<?php echo htmlspecialchars($shop['shop_name'] ?? ''); ?>" placeholder="Full Name of Shop" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Email Address <span class="text-[10px] lowercase font-normal opacity-70">(Optional)</span></label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($shop['email'] ?? ''); ?>" placeholder="e.g. shop@example.com" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Installation Date</label>
            <input type="date" name="installation_date" value="<?php echo htmlspecialchars($shop['installation_date'] ?? date('Y-m-d')); ?>" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Contact Person</label>
                <input type="text" name="contact_person" value="<?php echo htmlspecialchars($shop['contact_person'] ?? ''); ?>" placeholder="Name" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($shop['phone'] ?? ''); ?>" placeholder="Contact No" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Address</label>
            <textarea name="address" rows="2" placeholder="Full Address" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all"><?php echo htmlspecialchars($shop['address'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="w-full py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all active:scale-95 mt-4">
            <?php echo $shop ? 'Update Location' : 'Save Registration'; ?>
        </button>
    </form>
</div>
