<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin', 'voucher_editor']);

// Ensure database connection is available for restaurant dropdown etc.
include_once __DIR__ . '/../config.php';
?>

<div class="max-w-7xl mx-auto animate-fade-in pb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- LEFT COLUMN: DONOR MANAGEMENT -->
        <div class="glass-panel overflow-hidden flex flex-col min-h-[700px] relative group">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10 bg-white/80 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-900 text-white flex items-center justify-center shadow-lg shadow-blue-900/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800 tracking-tight">Add New Donor</h3>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Beneficiary Registration</p>
                    </div>
                </div>
                <button id="toggle-donor-view" class="p-3 text-gray-400 hover:text-blue-900 hover:bg-blue-50 rounded-2xl transition-all active:scale-90" title="View Full List">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </button>
            </div>
            
            <!-- Content Area -->
            <div class="p-8 flex-1">
                <form id="hub-donor-form" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Full Name</label>
                            <input type="text" name="name" required placeholder="John Doe"
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative text-wrap">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Email Address <span class="text-blue-400/50 italic">(Optional)</span></label>
                                <input type="email" name="email" placeholder="john@example.com"
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                            </div>
                            <div class="relative text-wrap">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Phone Number <span class="text-blue-400/50 italic">(Optional)</span></label>
                                <input type="tel" name="phone" placeholder="+92 300 1234567"
                                    class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                            </div>
                        </div>

                        <div class="relative text-wrap">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Voucher ID (e.g. 10, 12020, DV-500)</label>
                            <input type="text" name="voucher_id" required placeholder="Enter custom ID..."
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl font-mono text-lg font-black text-blue-600 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        </div>

                        <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100/50">
                            <label class="block text-[10px] font-black text-blue-900 uppercase tracking-widest mb-3 px-1">Assign Partner Restaurants</label>
                            <select name="assigned_restaurants[]" multiple required class="w-full px-5 py-3 bg-white border border-blue-100 rounded-2xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-blue-500/20 custom-scrollbar h-40">
                                <?php
                                $rests = $conn->query("SELECT name FROM restaurants ORDER BY name ASC");
                                while($rest = $rests->fetch_assoc()):
                                ?>
                                <option value="<?php echo htmlspecialchars($rest['name']); ?>"><?php echo htmlspecialchars($rest['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                            <p class="text-[10px] text-blue-400 mt-3 font-black uppercase tracking-tighter italic flex items-center gap-2">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                Hold Ctrl (Windows) / Cmd (Mac) for Multiple
                            </p>
                        </div>
                    </div>
                    
                    <div class="pt-4 pb-2">
                        <button type="submit" class="w-full py-5 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95 flex items-center justify-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-yellow-400 text-blue-900 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </span>
                            Save Donor Record
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: PARTNER MANAGEMENT -->
        <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
        <div class="glass-panel overflow-hidden flex flex-col min-h-[700px] relative group">
             <div class="absolute inset-0 bg-gradient-to-b from-yellow-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

             <!-- Header -->
             <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10 bg-white/80 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-400 text-blue-900 flex items-center justify-center shadow-lg shadow-yellow-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800 tracking-tight">Add Partner</h3>
                        <p class="text-xs font-bold text-yellow-600 uppercase tracking-widest">Network Expansion</p>
                    </div>
                </div>
                 <button id="toggle-restaurant-view" class="p-3 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-2xl transition-all active:scale-90" title="View Partners List">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </button>
            </div>
            
            <!-- Content Area -->
            <div class="p-8 flex-1">
                <form id="hub-restaurant-form" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative text-wrap">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Restaurant Name</label>
                            <input type="text" name="restaurant_name" required placeholder="e.g. Savour Foods"
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-yellow-500 outline-none transition-all font-bold text-gray-700">
                        </div>

                        <div class="relative text-wrap">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 px-1">Address / Branch</label>
                            <textarea name="restaurant_address" rows="3" placeholder="Full branch address..."
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-yellow-500 outline-none transition-all font-bold text-gray-700"></textarea>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 space-y-5">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Offer Configuration</label>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="offer_mode" value="discount" checked class="hidden peer">
                                    <div class="p-4 bg-white border-2 border-transparent peer-checked:border-yellow-500 peer-checked:bg-yellow-50 rounded-2xl text-center transition-all">
                                        <span class="block text-xs font-black text-gray-400 peer-checked:text-yellow-600 uppercase">Discount %</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="offer_mode" value="price" class="hidden peer">
                                    <div class="p-4 bg-white border-2 border-transparent peer-checked:border-yellow-500 peer-checked:bg-yellow-50 rounded-2xl text-center transition-all">
                                        <span class="block text-xs font-black text-gray-400 peer-checked:text-yellow-600 uppercase">Fixed Price</span>
                                    </div>
                                </label>
                            </div>

                            <div id="input-discount" class="relative group">
                                <input type="number" name="discount_percentage" placeholder="0"
                                    class="w-full pl-5 pr-12 py-4 bg-white border border-gray-200 rounded-2xl focus:border-yellow-500 outline-none font-black text-gray-800 text-lg">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 font-black text-gray-300 text-xl group-focus-within:text-yellow-500">%</span>
                            </div>

                            <div id="input-price" class="hidden relative group text-wrap">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-300 text-lg group-focus-within:text-yellow-500">PKR</span>
                                <input type="number" name="custom_price" placeholder="0"
                                    class="w-full pl-16 pr-5 py-4 bg-white border border-gray-200 rounded-2xl focus:border-yellow-500 outline-none font-black text-gray-800 text-lg">
                            </div>
                        </div>

                        <!-- Partner Login Credentials (new) -->
                        <div class="bg-yellow-50/50 p-6 rounded-3xl border border-yellow-100/50 space-y-4">
                            <label class="block text-[10px] font-black text-yellow-900 uppercase tracking-widest px-1">Partner Login Credentials</label>
                            <p class="text-xs text-yellow-700 font-medium px-1 -mt-2">Set credentials for the partner to log in and redeem vouchers.</p>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Username</label>
                                <input type="text" name="partner_username" required placeholder="e.g. meltingspots"
                                    class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:border-yellow-500 outline-none transition-all font-bold text-gray-700">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Password</label>
                                <input type="password" name="partner_password" required placeholder="Create a secure password"
                                    class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:border-yellow-500 outline-none transition-all font-bold text-gray-700">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 pb-2">
                        <button type="submit" class="w-full py-5 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95 flex items-center justify-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-yellow-400 text-blue-900 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </span>
                            Save Partner Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>    </div>
</div>
