<?php
// This is included in settings.php
?>
<!-- Quill.js CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<div class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-500">
    <div class="glass-panel p-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <div>
                    <h4 class="text-xl font-black text-gray-800 tracking-tight">Email Template Designer</h4>
                    <p class="text-sm text-gray-500 font-medium">Customise the emails sent to your donors and partners.</p>
                </div>
            </div>
            
            <div class="flex bg-gray-100 p-1.5 rounded-2xl">
                <button onclick="switchTemplateTab('voucher')" id="tab-btn-voucher" class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white text-blue-900 shadow-sm">Voucher system</button>
                <button onclick="switchTemplateTab('box')" id="tab-btn-box" class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-gray-500 hover:text-gray-700">Box communication</button>
            </div>
        </div>

        <!-- Voucher Template Form -->
        <form id="template-form-voucher" class="template-form space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Email Subject</label>
                <input type="text" name="email_template_voucher_subject" placeholder="Your Dosti Voucher: {voucher_id}"
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition-all font-bold text-gray-700">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Message Body</label>
                <input type="hidden" name="email_template_voucher_body">
                <div class="mt-2 rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                    <div id="voucher-editor" style="height: 300px;"></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest w-full mb-1">Available Placeholders</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('voucher', '{name}')">{name}</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('voucher', '{voucher_id}')">{voucher_id}</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('voucher', '{phone}')">{phone}</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('voucher', '{email}')">{email}</span>
            </div>

            <div class="pt-6 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-3.5 bg-blue-900 text-white font-black rounded-2xl hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/20 active:scale-95 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Voucher Template
                </button>
            </div>
        </form>

        <!-- Box Template Form (Hidden by default) -->
        <form id="template-form-box" class="template-form space-y-6 hidden">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Email Subject</label>
                <input type="text" name="email_template_box_subject" placeholder="Collection Scheduled for {shop_name}"
                    class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition-all font-bold text-gray-700">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Message Body</label>
                <input type="hidden" name="email_template_box_body">
                <div class="mt-2 rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                    <div id="box-editor" style="height: 300px;"></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest w-full mb-1">Available Placeholders</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('box', '{shop_name}')">{shop_name}</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('box', '{contact_person}')">{contact_person}</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg cursor-pointer hover:bg-indigo-100" onclick="insertPlaceholder('box', '{box_number}')">{box_number}</span>
            </div>

            <div class="pt-6 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Box Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let voucherQuill, boxQuill;

function switchTemplateTab(type) {
    document.querySelectorAll('.template-form').forEach(f => f.classList.add('hidden'));
    document.getElementById('template-form-' + type).classList.remove('hidden');
    
    document.getElementById('tab-btn-voucher').className = type === 'voucher' 
        ? 'px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white text-blue-900 shadow-sm'
        : 'px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-gray-500 hover:text-gray-700';
        
    document.getElementById('tab-btn-box').className = type === 'box' 
        ? 'px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all bg-white text-indigo-900 shadow-sm'
        : 'px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-gray-500 hover:text-gray-700';
}

function insertPlaceholder(type, tag) {
    const quill = type === 'voucher' ? voucherQuill : boxQuill;
    const range = quill.getSelection(true);
    quill.insertText(range.index, tag);
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Quill Editors
    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'clean']
    ];

    voucherQuill = new Quill('#voucher-editor', {
        theme: 'snow',
        placeholder: 'Compose your voucher email...',
        modules: { toolbar: toolbarOptions }
    });

    boxQuill = new Quill('#box-editor', {
        theme: 'snow',
        placeholder: 'Compose your box collection email...',
        modules: { toolbar: toolbarOptions }
    });

    // Load current templates
    fetch('api/get_settings.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const settings = data.settings;
                Object.keys(settings).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) input.value = settings[key];
                    
                    // Populate Quill editors
                    if (key === 'email_template_voucher_body') {
                        voucherQuill.root.innerHTML = settings[key];
                    }
                    if (key === 'email_template_box_body') {
                        boxQuill.root.innerHTML = settings[key];
                    }
                });
            }
        });

    // Handle form submissions
    document.querySelectorAll('.template-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Sync Quill content to hidden inputs
            if (form.id === 'template-form-voucher') {
                document.querySelector('[name="email_template_voucher_body"]').value = voucherQuill.root.innerHTML;
            } else if (form.id === 'template-form-box') {
                document.querySelector('[name="email_template_box_body"]').value = boxQuill.root.innerHTML;
            }

            const formData = new FormData(e.target);
            const promises = [];
            
            formData.forEach((value, key) => {
                const fd = new FormData();
                fd.append('setting_key', key);
                fd.append('setting_value', value);
                promises.push(fetch('api/update_settings.php', { method: 'POST', body: fd }));
            });

            Promise.all(promises)
                .then(() => alert('Template saved successfully!'))
                .catch(err => alert('Error: ' + err));
        });
    });
});
</script>

<style>
/* Quill Reset to Match Design */
.ql-toolbar.ql-snow {
    @apply border-gray-200 border-x-0 border-t-0 bg-gray-50/50 py-3;
}
.ql-container.ql-snow {
    @apply border-0 font-medium text-gray-700;
}
.ql-editor {
    @apply min-h-[300px];
}
</style>
