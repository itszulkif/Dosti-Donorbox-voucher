<?php
include '../config.php';

$id = $_GET['id'] ?? null;
if (!$id) exit('Missing ID');

$stmt = $conn->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();

if (!$donor) exit('Donor not found');
?>

<div class="p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight">Update Donor</h3>
            <p class="text-gray-500 text-sm font-medium">Modify record for <?php echo htmlspecialchars($donor['name']); ?></p>
        </div>
        <button onclick="closeModal('modal-container')" class="p-2 text-gray-400 hover:text-red-500 transition-colors bg-gray-50 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <form class="space-y-6">
        <input type="hidden" name="id" value="<?php echo $donor['id']; ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($donor['name']); ?>" required 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($donor['email']); ?>" required 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($donor['phone']); ?>" required 
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Voucher ID (Locked)</label>
                <input type="text" value="<?php echo htmlspecialchars($donor['voucher_id']); ?>" readonly 
                    class="w-full px-5 py-3.5 bg-gray-100 border border-gray-200 rounded-2xl outline-none font-mono font-bold text-blue-600 cursor-not-allowed">
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95">
                Save Changes
            </button>
        </div>
    </form>
</div>
