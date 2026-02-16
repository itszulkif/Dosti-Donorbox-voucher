<?php
include '../config.php';

$id = $_GET['id'] ?? null;
if (!$id) exit('Missing ID');

$stmt = $conn->prepare("SELECT v.*, s.shop_name FROM donation_visits v JOIN donation_shops s ON v.shop_id = s.id WHERE v.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$visit = $stmt->get_result()->fetch_assoc();

if (!$visit) exit('Visit not found');
?>

<div class="p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight">Update Collection</h3>
            <p class="text-gray-500 text-sm font-medium">Recorded at <?php echo htmlspecialchars($visit['shop_name']); ?></p>
        </div>
        <button onclick="closeModal('modal-container')" class="p-2 text-gray-400 hover:text-red-500 transition-colors bg-gray-50 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <form class="space-y-6">
        <input type="hidden" name="id" value="<?php echo $visit['id']; ?>">
        
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Amount Collected (PKR)</label>
            <div class="relative">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400">Rs.</span>
                <input type="number" name="amount" value="<?php echo $visit['amount']; ?>" step="0.01" required 
                    class="w-full pl-14 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-black text-xl text-gray-800">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Collection Date</label>
                <input type="date" name="visit_date" value="<?php echo $visit['visit_date']; ?>" required 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Received From</label>
                <input type="text" name="received_from" value="<?php echo htmlspecialchars($visit['received_from']); ?>" required 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Collected By</label>
            <input type="text" name="received_by" value="<?php echo htmlspecialchars($visit['received_by']); ?>" required 
                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95">
                Save Changes
            </button>
        </div>
    </form>
</div>
