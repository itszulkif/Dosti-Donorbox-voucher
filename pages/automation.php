<!-- Quill.js CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Campaign Manager</h1>
            <p class="text-gray-500 font-medium">Broadcast personalized messages to your audience</p>
        </div>
        <div class="flex gap-2" id="wizard-steps-indicator">
            <span class="step-indicator active w-10 h-2 rounded-full bg-blue-900 transition-all"></span>
            <span class="step-indicator w-10 h-2 rounded-full bg-gray-200 transition-all"></span>
            <span class="step-indicator w-10 h-2 rounded-full bg-gray-200 transition-all"></span>
        </div>
    </div>

    <!-- Wizard Form -->
    <div class="glass-panel overflow-hidden">
        <form id="campaignForm">
            <!-- Step 1: Audience Selection -->
            <div id="step-1" class="wizard-step p-8 lg:p-12">
                <div class="max-w-2xl mx-auto space-y-10">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-gray-800">Select Your Audience</h2>
                        <p class="text-gray-500 mt-2 font-medium">Who would you like to reach out to today?</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="audience" value="donor" class="peer absolute opacity-0" checked>
                            <div class="p-6 rounded-3xl border-2 border-gray-100 bg-white hover:border-blue-500 peer-checked:border-blue-900 peer-checked:bg-blue-50/30 transition-all group-hover:shadow-xl group-hover:-translate-y-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">DV</div>
                                    <h4 class="font-bold text-gray-800">All Donors</h4>
                                </div>
                                <p class="text-xs text-gray-500 leading-relaxed">Send vouchers, thank you notes, or updates to your voucher contributors.</p>
                            </div>
                            <div class="absolute top-4 right-4 text-blue-900 opacity-0 peer-checked:opacity-100">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="audience" value="shopkeeper" class="peer absolute opacity-0">
                            <div class="p-6 rounded-3xl border-2 border-gray-100 bg-white hover:border-blue-500 peer-checked:border-blue-900 peer-checked:bg-blue-50/30 transition-all group-hover:shadow-xl group-hover:-translate-y-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold">BX</div>
                                    <h4 class="font-bold text-gray-800">All Shopkeepers</h4>
                                </div>
                                <p class="text-xs text-gray-500 leading-relaxed">Communicate collection schedules or news to your donation box partners.</p>
                            </div>
                            <div class="absolute top-4 right-4 text-blue-900 opacity-0 peer-checked:opacity-100">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" onclick="nextStep(2)" class="px-8 py-4 bg-blue-900 text-white font-black rounded-2xl hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/20 active:scale-95 flex items-center gap-3">
                            Next: Design Template
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Template Library & Editor -->
            <div id="step-2" class="wizard-step hidden p-8 lg:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Template Library Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Template Library</h3>
                            <p class="text-xs text-gray-500 font-medium">Select a base or create new</p>
                        </div>
                        <div id="template-list" class="space-y-3">
                            <!-- Templates loaded via JS -->
                            <button type="button" onclick="createNewTemplate()" class="w-full p-4 rounded-2xl border-2 border-dashed border-gray-200 text-gray-400 font-bold text-sm hover:border-blue-500 hover:text-blue-500 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                New Blank Template
                            </button>
                        </div>
                    </div>

                    <!-- Editor Area -->
                    <div class="lg:col-span-2 space-y-6 bg-gray-50/50 p-6 rounded-3xl border border-gray-100">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Email Subject</label>
                            <input type="text" name="subject" id="email-subject" placeholder="Enter your email subject..." 
                                class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:border-blue-900 outline-none transition-all font-bold text-gray-700 mt-2">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Message Content</label>
                            <div class="mt-2 rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-white">
                                <!-- Quill Container -->
                                <div id="quill-editor" style="height: 350px;" class="text-gray-700"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest w-full mb-1">Personalization Tags</span>
                            <span class="tag-btn bg-blue-50 text-blue-700" onclick="insertTag('{{name}}')">{{name}}</span>
                            <span class="tag-btn bg-blue-50 text-blue-700" onclick="insertTag('{{email}}')">{{email}}</span>
                            <span class="tag-btn bg-blue-50 text-blue-700" onclick="insertTag('{{phone}}')">{{phone}}</span>
                            <span id="donor-tags" class="flex gap-2">
                                <span class="tag-btn bg-green-50 text-green-700" onclick="insertTag('{{voucher_id}}')">{{voucher_id}}</span>
                            </span>
                            <span id="box-tags" class="hidden flex gap-2">
                                <span class="tag-btn bg-yellow-50 text-yellow-700" onclick="insertTag('{{shop_name}}')">{{shop_name}}</span>
                                <span class="tag-btn bg-yellow-50 text-yellow-700" onclick="insertTag('{{box_number}}')">{{box_number}}</span>
                            </span>
                        </div>

                        <div class="flex justify-between pt-6 border-t border-gray-200">
                            <button type="button" onclick="nextStep(1)" class="px-6 py-3.5 text-gray-500 font-bold hover:text-gray-800 transition-colors">Back</button>
                            <button type="button" onclick="nextStep(3)" class="px-8 py-3.5 bg-blue-900 text-white font-black rounded-2xl hover:bg-blue-800 transition-all shadow-xl shadow-blue-900/20 active:scale-95 flex items-center gap-3">
                                Review & Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Scheduling & Actions -->
            <div id="step-3" class="wizard-step hidden p-8 lg:p-12">
                <div class="max-w-xl mx-auto space-y-10">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-gray-800">Finalize & Send</h2>
                        <p class="text-gray-500 mt-2 font-medium">Choose when to launch your campaign.</p>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" id="btn-send-now" onclick="setAction('now')" class="action-btn p-8 rounded-3xl border-2 border-blue-900 bg-blue-50/50 text-blue-900 transition-all flex flex-col items-center gap-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="font-black uppercase tracking-widest text-xs">Send Now</span>
                            </button>
                            <button type="button" id="btn-schedule" onclick="setAction('schedule')" class="action-btn p-8 rounded-3xl border-2 border-gray-100 bg-white text-gray-400 hover:border-blue-500 transition-all flex flex-col items-center gap-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-black uppercase tracking-widest text-xs">Schedule</span>
                            </button>
                        </div>

                        <div id="schedule-picker" class="hidden animate-in fade-in slide-in-from-top-2 duration-300">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Execution Time</label>
                            <input type="datetime-local" name="scheduled_at" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-900 outline-none transition-all font-bold text-gray-700 mt-2">
                        </div>

                        <div class="p-6 rounded-2xl bg-yellow-50 border border-yellow-100 flex gap-4">
                            <div class="w-10 h-10 shrink-0 bg-yellow-400/20 text-yellow-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-yellow-800 font-medium leading-relaxed">
                                <b>Personalization is active.</b> Tags like <code class="bg-yellow-400/20 px-1 rounded">{{name}}</code> will be automatically replaced with each recipient's data.
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-between pt-6">
                        <button type="button" onclick="nextStep(2)" class="px-6 py-3.5 text-gray-500 font-bold hover:text-gray-800 transition-colors">Back</button>
                        <button type="submit" class="px-12 py-5 bg-blue-900 text-white font-black rounded-3xl hover:bg-blue-800 transition-all shadow-2xl shadow-blue-900/40 active:scale-95">
                            Launch Campaign
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.tag-btn {
    @apply px-3 py-1.5 text-[10px] font-black rounded-xl uppercase tracking-widest cursor-pointer hover:scale-105 active:scale-95 transition-all shadow-sm;
}
.wizard-step {
    @apply transition-all duration-500;
}
.action-btn.active {
    @apply border-blue-900 bg-blue-50/50 text-blue-900 shadow-xl;
}
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

<script>
let currentStep = 1;
let selectedAction = 'now';
let quill;

// Initialize Quill
document.addEventListener('DOMContentLoaded', () => {
    quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Compose your campaign message here...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    });
});

function nextStep(step) {
    // Basic validation
    if (step === 3 && document.getElementById('email-subject').value.trim() === '') {
        showToast('Please enter a subject', 'error');
        return;
    }

    // Toggle Visibility
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.add('hidden'));
    document.getElementById(`step-${step}`).classList.remove('hidden');

    // Update Indicators
    const indicators = document.querySelectorAll('.step-indicator');
    indicators.forEach((ind, idx) => {
        if (idx < step) ind.classList.add('bg-blue-900');
        else ind.classList.remove('bg-blue-900');
    });

    // Toggle Tag visibility
    const audienceSelect = document.querySelector('input[name="audience"]:checked');
    if (audienceSelect) {
        const audience = audienceSelect.value;
        document.getElementById('donor-tags').classList.toggle('hidden', audience !== 'donor');
        document.getElementById('box-tags').classList.toggle('hidden', audience !== 'shopkeeper');
    }

    currentStep = step;
}

function insertTag(tag) {
    const range = quill.getSelection(true);
    quill.insertText(range.index, tag);
}

function setAction(action) {
    selectedAction = action;
    document.getElementById('btn-send-now').className = action === 'now' 
        ? 'action-btn active p-8 rounded-3xl border-2 border-blue-900 bg-blue-50/50 text-blue-900 flex flex-col items-center gap-3'
        : 'action-btn p-8 rounded-3xl border-2 border-gray-100 bg-white text-gray-400 hover:border-blue-500 flex flex-col items-center gap-3';
    
    document.getElementById('btn-schedule').className = action === 'schedule'
        ? 'action-btn active p-8 rounded-3xl border-2 border-blue-900 bg-blue-50/50 text-blue-900 flex flex-col items-center gap-3'
        : 'action-btn p-8 rounded-3xl border-2 border-gray-100 bg-white text-gray-400 hover:border-blue-500 flex flex-col items-center gap-3';
    
    document.getElementById('schedule-picker').classList.toggle('hidden', action !== 'schedule');
}

// Campaign Library Logic
async function loadTemplates() {
    const list = document.getElementById('template-list');
    try {
        const res = await fetch('api/get_campaign_templates.php');
        const data = await res.json();
        if(data.success) {
            const html = data.templates.map(t => `
                <button type="button" onclick='loadTemplateIntoEditor(${JSON.stringify(t)})' class="w-full p-4 rounded-3xl bg-gray-50/50 hover:bg-white border-2 border-transparent hover:border-blue-900/30 transition-all text-left group">
                    <div class="font-bold text-gray-800 text-sm group-hover:text-blue-900 transition-colors">${t.name}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">${t.audience_type} audience</div>
                </button>
            `).join('');
            if (html) {
                list.innerHTML = html + list.innerHTML;
            }
        }
    } catch(err) { console.error(err); }
}

function loadTemplateIntoEditor(t) {
    document.getElementById('email-subject').value = t.subject;
    quill.root.innerHTML = t.body;
    showToast(`Loaded: ${t.name}`);
}

function createNewTemplate() {
    document.getElementById('email-subject').value = '';
    quill.root.innerHTML = '';
}

// Form Submission
document.getElementById('campaignForm').onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('body', quill.root.innerHTML);
    formData.append('action_type', selectedAction);

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerText = 'Processing...';

    try {
        const res = await fetch('api/send_campaign.php', { method: 'POST', body: formData });
        const data = await res.json();
        if(data.success) {
            showToast(data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToast(data.message, 'error');
            btn.disabled = false;
            btn.innerText = originalText;
        }
    } catch(err) {
        showToast('Connection error', 'error');
        btn.disabled = false;
        btn.innerText = originalText;
    }
};

loadTemplates();
</script>

<style>
.active-glow {
    position: relative;
}
.active-glow::after {
    content: "";
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 20px;
    background: #1e3a8a;
    border-radius: 4px;
}
</style>
