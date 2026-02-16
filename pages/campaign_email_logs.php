<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
?>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Campaign Email Logs</h1>
            <p class="text-gray-500 font-medium">View and manage email records from broadcast campaigns</p>
        </div>
        <button onclick="deleteAllLogs()" class="px-6 py-3 bg-red-600 text-white font-black rounded-2xl shadow-lg shadow-red-600/20 hover:bg-red-700 transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Delete All Logs
        </button>
    </div>

    <!-- Logs Table -->
    <div class="glass-panel overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xl font-bold text-gray-800">Campaign Email History</h3>
            <div class="relative max-w-sm w-full">
                <input type="text" id="logSearch" placeholder="Search recipients or subject..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition-all text-sm font-medium"
                    onkeyup="debounceSearch()">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div id="logsContainer" class="overflow-x-auto">
            <div class="flex items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="p-6 border-t border-gray-50"></div>
    </div>
</div>

<!-- View Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Email Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="detailContent" class="p-6"></div>
    </div>
</div>

<script>
let currentPage = 1;
let searchTimeout = null;

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadLogs(1);
    }, 500);
}

function loadLogs(page = 1) {
    currentPage = page;
    const search = document.getElementById('logSearch').value;
    const container = document.getElementById('logsContainer');

    // Show loading state
    container.innerHTML = `
        <div class="flex items-center justify-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    `;

    fetch(`api/get_email_logs.php?page=${page}&type=campaign&search=${encodeURIComponent(search)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderLogs(data.logs);
                renderPagination(data.pagination);
            }
        })
        .catch(err => {
            console.error('Error loading logs:', err);
            container.innerHTML = '<div class="text-center py-10 text-red-500 font-bold">Failed to load logs</div>';
        });
}

function renderLogs(logs) {
    const container = document.getElementById('logsContainer');
    
    if (logs.length === 0) {
        container.innerHTML = `
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No Email Logs</h3>
                <p class="text-gray-500">No emails have been sent yet</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-blue-900 text-white text-[10px] uppercase tracking-widest font-black">
                    <th class="px-6 py-5">Recipient</th>
                    <th class="px-6 py-5">Subject</th>
                    <th class="px-6 py-5">Type</th>
                    <th class="px-6 py-5 text-center">Status</th>
                    <th class="px-6 py-5">Date</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                ${logs.map(log => `
                    <tr class="group hover:bg-blue-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="font-bold text-gray-800">${log.recipient_name || 'N/A'}</div>
                            <div class="text-xs text-gray-400">${log.recipient_email}</div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-700 max-w-xs truncate">${log.subject}</td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${getTypeColor(log.email_type)}">
                                ${log.email_type}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black ${log.status === 'sent' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100'}">
                                ${log.status === 'sent' ? '✓ SENT' : '✗ FAILED'}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-gray-700">${formatDate(log.sent_at)}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">${formatTime(log.sent_at)}</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick='viewDetails(${JSON.stringify(log)})' class="p-2 bg-white text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button onclick="deleteLog(${log.id})" class="p-2 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-lg border border-gray-100 shadow-sm transition-all" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let pages = '';
    for (let i = 1; i <= pagination.total_pages; i++) {
        pages += `
            <button onclick="loadLogs(${i})" class="px-4 py-2 rounded-lg font-bold text-sm transition-all ${i === pagination.current_page ? 'bg-blue-900 text-white shadow-lg shadow-blue-900/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}">
                ${i}
            </button>
        `;
    }
    
    container.innerHTML = `
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 font-medium">
                Showing page ${pagination.current_page} of ${pagination.total_pages} (${pagination.total_records} total records)
            </p>
            <div class="flex gap-2">
                ${pages}
            </div>
        </div>
    `;
}

function getTypeColor(type) {
    const colors = {
        'voucher': 'bg-blue-50 text-blue-600 border border-blue-100',
        'box': 'bg-purple-50 text-purple-600 border border-purple-100',
        'campaign': 'bg-yellow-50 text-yellow-600 border border-yellow-100',
        'manual': 'bg-gray-50 text-gray-600 border border-gray-100'
    };
    return colors[type] || 'bg-gray-50 text-gray-600 border border-gray-100';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function viewDetails(log) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    
    content.innerHTML = `
        <div class="space-y-6">
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recipient</label>
                <p class="text-gray-800 font-bold text-lg mt-1">${log.recipient_name || 'N/A'} <span class="text-sm text-gray-500">(${log.recipient_email})</span></p>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Subject</label>
                <p class="text-gray-800 font-semibold mt-1">${log.subject}</p>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Body</label>
                <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-700 whitespace-pre-wrap mt-1 border border-gray-100">${log.body || 'N/A'}</div>
            </div>
            <div class="grid grid-cols-3 gap-4 p-4 bg-blue-50/30 rounded-xl border border-blue-100">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Type</label>
                    <p class="text-gray-800 font-bold mt-1">${log.email_type}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status</label>
                    <p class="text-gray-800 font-bold mt-1">${log.status}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sent At</label>
                    <p class="text-gray-800 font-bold mt-1">${formatDate(log.sent_at)}</p>
                </div>
            </div>
            ${log.error_message ? `
                <div class="p-4 bg-red-50 rounded-xl border border-red-200">
                    <label class="text-xs font-bold text-red-500 uppercase tracking-widest">Error Message</label>
                    <div class="text-sm text-red-700 mt-2 font-medium">${log.error_message}</div>
                </div>
            ` : ''}
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function deleteLog(logId) {
    if (!confirm('Are you sure you want to delete this email log?')) return;
    
    const formData = new FormData();
    formData.append('log_id', logId);
    
    fetch('api/delete_email_log.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('✅ Log deleted successfully');
            loadLogs(currentPage);
        } else {
            showToast('❌ Error: ' + data.message);
        }
    });
}

function deleteAllLogs() {
    if (!confirm('Are you sure you want to delete ALL email logs? This action cannot be undone.')) return;
    
    fetch('api/delete_all_email_logs.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('✅ All logs deleted successfully');
                loadLogs(1);
            } else {
                showToast('❌ Error: ' + data.message);
            }
        });
}

function showToast(message) {
    // Using alert for now, can be replaced with a toast library
    alert(message);
}

// Load logs on page load
loadLogs(1);
</script>
