<?php
// This is included in settings.php
?>
<div class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-500">
    <div class="glass-panel p-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h4 class="text-xl font-black text-gray-800 tracking-tight">Email SMTP Configuration</h4>
                <p class="text-sm text-gray-500 font-medium">Configure outgoing mail server settings.</p>
            </div>
        </div>

        <form id="smtp-settings-form" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Host & Port -->
                <div class="space-y-6">
                    <p class="text-xs font-black text-blue-600 uppercase tracking-widest border-l-4 border-blue-600 pl-3">Server Details</p>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">SMTP Host</label>
                        <input type="text" name="smtp_host" placeholder="smtp.gmail.com"
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">SMTP Port</label>
                            <input type="number" name="smtp_port" placeholder="465"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700 font-mono">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Encryption</label>
                            <select name="smtp_encryption" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700 appearance-none cursor-pointer">
                                <option value="none">None</option>
                                <option value="ssl" selected>SSL</option>
                                <option value="tls">TLS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Authentication -->
                <div class="space-y-6">
                    <p class="text-xs font-black text-blue-600 uppercase tracking-widest border-l-4 border-blue-600 pl-3">Authentication</p>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Username / Email</label>
                        <input type="text" name="smtp_user" placeholder="your-email@gmail.com"
                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Password / App Key</label>
                        <div class="relative">
                            <input type="password" name="smtp_pass" placeholder="••••••••••••"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                            <button type="button" onclick="togglePass(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sender Info -->
                <div class="md:col-span-2 space-y-6 mt-4">
                    <p class="text-xs font-black text-blue-600 uppercase tracking-widest border-l-4 border-blue-600 pl-3">Sender Appearance</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">From Name</label>
                            <input type="text" name="smtp_from_name" placeholder="Dosti Welfare"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">From Email</label>
                            <input type="email" name="smtp_from_email" placeholder="noreply@dostiwelfare.org"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-700">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex justify-end gap-4">
                <button type="button" onclick="testConnection()" class="px-6 py-3.5 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                    Test Connection
                </button>
                <button type="submit" class="px-10 py-3.5 bg-blue-900 text-white font-black rounded-2xl hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/20 active:scale-95 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePass(btn) {
    const input = btn.previousElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        btn.classList.add('text-blue-600');
    } else {
        input.type = 'password';
        btn.classList.remove('text-blue-600');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Load existing settings
    fetch('api/get_settings.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const settings = data.settings;
                const form = document.getElementById('smtp-settings-form');
                
                // Map settings to form fields
                Object.keys(settings).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = settings[key];
                    }
                });
            }
        });

    // Save Settings
    document.getElementById('smtp-settings-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        // Single save is not supported by our current API, so we loop or update API
        // Let's loop for now to reuse update_settings.php or implement batch
        const promises = [];
        formData.forEach((value, key) => {
            const fd = new FormData();
            fd.append('setting_key', key);
            fd.append('setting_value', value);
            promises.push(fetch('api/update_settings.php', { method: 'POST', body: fd }));
        });

        Promise.all(promises)
            .then(() => alert('SMTP Settings updated successfully!'))
            .catch(err => alert('Error updating settings: ' + err));
    });
});

function testConnection() {
    const form = document.getElementById('smtp-settings-form');
    const formData = new FormData(form);
    const btn = document.querySelector('button[onclick="testConnection()"]');
    const originalText = btn.innerHTML;
    const debugContainer = document.getElementById('smtp-debug-log') || createDebugContainer(btn);

    // Basic client-side validation
    if (!formData.get('smtp_host') || !formData.get('smtp_user') || !formData.get('smtp_pass')) {
        alert('Please fill in Host, User, and Password fields before testing.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';
    debugContainer.classList.add('hidden');
    debugContainer.innerHTML = '';

    fetch('api/test_smtp.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Error: ' + data.message + '\n\nCheck the debug log below the button for details.');
        }
        
        if (data.debug) {
            debugContainer.classList.remove('hidden');
            debugContainer.innerHTML = '<pre class="text-xs font-mono bg-gray-900 text-green-400 p-4 rounded-xl overflow-x-auto mt-4 max-h-64 overflow-y-auto">' + data.debug + '</pre>';
        }
    })
    .catch(err => {
        alert('Network or Server Error: ' + err);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function createDebugContainer(btn) {
    const div = document.createElement('div');
    div.id = 'smtp-debug-log';
    div.className = 'hidden w-full';
    // Insert after the button's container (the flex div)
    btn.parentElement.after(div);
    return div;
}
</script>
