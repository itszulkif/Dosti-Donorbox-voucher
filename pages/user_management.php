<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
?>

<div class="max-w-7xl mx-auto animate-fade-in pb-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT: Create User Form -->
        <div class="lg:col-span-1">
            <div class="glass-panel p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-900 text-white flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-800">Create User</h3>
                        <p class="text-xs font-bold text-blue-600 uppercase">Add New Admin</p>
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
                        Create User
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
                            <h3 class="text-xl font-black text-gray-800">All Users</h3>
                            <p class="text-xs font-bold text-yellow-600 uppercase">System Administrators</p>
                        </div>
                    </div>
                    <button onclick="loadUsers()" class="p-3 text-gray-400 hover:text-blue-900 hover:bg-blue-50 rounded-2xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>

                <div id="users-container" class="space-y-3">
                    <!-- Users will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load users on page load
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
    btn.innerHTML = '<svg class="animate-spin w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const res = await fetch('api/create_user.php', { method: 'POST', body: formData });
        const result = await res.json();
        
        if (result.success) {
            showToast('User created successfully!', 'success');
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
    container.innerHTML = '<div class="text-center py-8"><svg class="animate-spin w-8 h-8 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
    
    try {
        const res = await fetch('api/get_users.php');
        const result = await res.json();
        
        if (result.success && result.users) {
            if (result.users.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">No users found</p>';
                return;
            }
            
            const roleColors = {
                'super_admin': 'bg-purple-100 text-purple-700 border-purple-200',
                'voucher_editor': 'bg-blue-100 text-blue-700 border-blue-200',
                'box_editor': 'bg-green-100 text-green-700 border-green-200',
                'admin': 'bg-gray-100 text-gray-700 border-gray-200'
            };
            
            const roleNames = {
                'super_admin': 'Super Admin',
                'voucher_editor': 'Voucher Editor',
                'box_editor': 'Box Editor',
                'admin': 'Admin'
            };
            
            container.innerHTML = result.users.map(user => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                            ${user.full_name ? user.full_name.substring(0, 2).toUpperCase() : user.username.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">${user.full_name || user.username}</p>
                            <p class="text-sm text-gray-500">@${user.username}${user.designation ? ' • ' + user.designation : ''}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 rounded-xl text-xs font-bold border ${roleColors[user.role] || roleColors['admin']}">
                            ${roleNames[user.role] || 'Admin'}
                        </span>
                        <span class="text-xs text-gray-400">${new Date(user.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="text-center text-red-500 py-8">Failed to load users</p>';
        }
    } catch (err) {
        container.innerHTML = '<p class="text-center text-red-500 py-8">Connection error</p>';
    }
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-2xl shadow-2xl text-white font-bold z-[9999] animate-fade-in ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
