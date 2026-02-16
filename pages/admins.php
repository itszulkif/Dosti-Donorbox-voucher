<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
?>

<div class="max-w-7xl mx-auto animate-fade-in pb-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT: Create User Form -->
        <div class="lg:col-span-1">
            <div class="glass-panel p-8 sticky top-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-900 text-white flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800">Assign Role</h3>
                        <p class="text-xs font-bold text-blue-600 uppercase">Manage Access</p>
                    </div>
                </div>

                <form id="create-user-form" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Full Name</label>
                        <input type="text" name="full_name" required placeholder="John Doe"
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Designation <span class="text-gray-400 italic lowercase font-normal">(Optional)</span></label>
                        <input type="text" name="designation" placeholder="e.g. Manager, Assistant"
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Username</label>
                        <input type="text" name="username" required placeholder="johndoe"
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Password</label>
                        <input type="password" name="password" required placeholder="Minimum 6 characters"
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Role</label>
                        <select name="role" required
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                            <option value="">Select Role...</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="voucher_editor">Voucher Editor</option>
                            <option value="box_editor">Box Editor</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-4 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create Account
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT: User List -->
        <div class="lg:col-span-2">
            <div class="glass-panel p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-yellow-400 text-blue-900 flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-800">User Directory</h3>
                            <p class="text-xs font-bold text-yellow-600 uppercase">System & Partners</p>
                        </div>
                    </div>
                    <button onclick="loadUsers()" class="p-3 text-gray-400 hover:text-blue-900 hover:bg-blue-50 rounded-2xl transition-all" title="Refresh List">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>

                <div id="users-container" class="space-y-6">
                    <!-- Users will be loaded here by loadUsers() -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm animate-fade-in">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="glass-panel w-full max-w-md p-8 animate-in zoom-in-95 duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-800">Update User</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="edit-user-form" class="space-y-4">
                <input type="hidden" name="id" id="edit-id">
                
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="full_name" id="edit-full-name" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Designation</label>
                    <input type="text" name="designation" id="edit-designation"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Role</label>
                    <select name="role" id="edit-role" required
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                        <option value="super_admin">Super Admin</option>
                        <option value="voucher_editor">Voucher Editor</option>
                        <option value="box_editor">Box Editor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">New Password <span class="italic text-[8px]">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" placeholder="At least 6 characters"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-blue-900 text-white font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-800 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
});

// Create user form submission
document.getElementById('create-user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...</span>';
    
    try {
        const res = await fetch('api/create_user.php', { method: 'POST', body: formData });
        const result = await res.json();
        
        if (result.success) {
            showToast('Account created successfully!', 'success');
            e.target.reset();
            loadUsers();
        } else {
            showToast(result.message || 'Failed to create user', 'error');
        }
    } catch (err) {
        showToast('Connection error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Load users list
async function loadUsers() {
    const container = document.getElementById('users-container');
    container.innerHTML = '<div class="text-center py-20"><svg class="animate-spin w-12 h-12 mx-auto text-blue-600 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="mt-4 text-gray-400 font-bold uppercase tracking-widest text-[10px]">Updating Database...</p></div>';
    
    try {
        const res = await fetch('api/get_users.php');
        const result = await res.json();
        
        if (result.success && result.users) {
            if (result.users.length === 0) {
                container.innerHTML = '<div class="text-center py-20 border-2 border-dashed border-gray-100 rounded-3xl"><p class="text-gray-400 font-bold">No active accounts found.</p></div>';
                return;
            }
            
            const roleColors = {
                'super_admin': 'bg-purple-100 text-purple-700 border-purple-200',
                'voucher_editor': 'bg-blue-100 text-blue-700 border-blue-200',
                'box_editor': 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'admin': 'bg-slate-100 text-slate-700 border-slate-200',
                'partner': 'bg-orange-100 text-orange-700 border-orange-200'
            };
            
            const roleNames = {
                'super_admin': 'Super Admin',
                'voucher_editor': 'Voucher Editor',
                'box_editor': 'Box Editor',
                'admin': 'Admin',
                'partner': 'Restaurant Partner'
            };
            
            const admins = result.users.filter(u => !u.restaurant_id);
            const partners = result.users.filter(u => u.restaurant_id);
            
            let html = '';
            
            if (admins.length > 0) {
                html += `
                <div class="space-y-3">
                    <h4 class="px-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Internal Staff</h4>
                    ${admins.map(user => renderUserRow(user, roleColors, roleNames)).join('')}
                </div>`;
            }
            
            if (partners.length > 0) {
                html += `
                <div class="space-y-3 pt-4">
                    <h4 class="px-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Restaurant Partners</h4>
                    ${partners.map(user => renderUserRow(user, roleColors, roleNames)).join('')}
                </div>`;
            }
            
            container.innerHTML = html;
        }
    } catch (err) {
        container.innerHTML = '<div class="text-center py-20 bg-red-50 rounded-3xl"><p class="text-red-600 font-bold">Failed to connect to directory.</p></div>';
    }
}

function renderUserRow(user, roleColors, roleNames) {
    const isPartner = !!user.restaurant_id;
    const displayName = user.full_name || user.restaurant_name || user.username;
    const initial = displayName.charAt(0).toUpperCase();
    
    return `
    <div class="group relative flex items-center justify-between p-4 bg-white border border-gray-100 rounded-3xl hover:border-blue-200 hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-500 overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl ${isPartner ? 'bg-orange-500' : 'bg-blue-900'} text-white flex items-center justify-center font-black text-lg shadow-lg">
                ${initial}
            </div>
            <div>
                <p class="font-black text-gray-800 text-base leading-tight">${displayName}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">@${user.username}</span>
                    ${user.designation ? `<span class="w-1 h-1 bg-gray-200 rounded-full"></span><span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">${user.designation}</span>` : ''}
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex flex-col items-end">
                <span class="px-3 py-1 rounded-full text-[9px] font-black border uppercase tracking-widest ${roleColors[user.role] || roleColors['admin']}">
                    ${isPartner ? 'Partner Login' : roleNames[user.role]}
                </span>
                <span class="text-[8px] font-bold text-gray-300 mt-1 uppercase">Created ${new Date(user.created_at).toLocaleDateString()}</span>
            </div>
            
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
               
                <button onclick="deleteUser(${user.id})" class="p-2.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Wipe Account">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    </div>`;
}

// Edit Modal Logic
function openEditModal(user) {
    document.getElementById('edit-id').value = user.id;
    document.getElementById('edit-full-name').value = user.full_name || '';
    document.getElementById('edit-designation').value = user.designation || '';
    document.getElementById('edit-role').value = user.role || 'admin';
    
    const modal = document.getElementById('edit-user-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('edit-user-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('edit-user-form').reset();
}

document.getElementById('edit-user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const btn = e.target.querySelector('button[type="submit"]');
    
    btn.disabled = true;
    btn.innerHTML = 'Updating...';
    
    try {
        const res = await fetch('api/update_user.php', { method: 'POST', body: formData });
        const result = await res.json();
        
        if (result.success) {
            showToast('Changes saved successfully', 'success');
            closeEditModal();
            loadUsers();
        } else {
            showToast(result.message || 'Update failed', 'error');
        }
    } catch (err) {
        showToast('Connection error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Save Changes';
    }
});

// Delete Logic
async function deleteUser(id) {
    if (!confirm('Are you absolutely sure? This account will be wiped permanently.')) return;
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const res = await fetch('api/delete_user.php', { method: 'POST', body: formData });
        const result = await res.json();
        
        if (result.success) {
            showToast('Account removed', 'success');
            loadUsers();
        } else {
            showToast(result.message || 'Action failed', 'error');
        }
    } catch (err) {
        showToast('Connection error', 'error');
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-8 left-1/2 transform -translate-x-1/2 px-8 py-4 rounded-3xl shadow-2xl text-white font-black z-[9999] animate-in slide-in-from-bottom-2 duration-300 ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('animate-out', 'fade-out', 'slide-out-to-bottom-2');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
