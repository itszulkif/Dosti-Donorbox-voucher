<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
?>

<div class="glass-panel p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-gray-800 mb-1">Email Logs</h2>
            <p class="text-sm text-gray-500">View and manage all sent email records</p>
        </div>
        <button onclick="deleteAllLogs()" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-red-600/20 hover:shadow-red-600/40">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Delete All Logs
        </button>
    </div>

    <!-- Logs Table -->
    <div class="glass-panel p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 border-b border-gray-50 pb-6">
            <h3 class="text-xl font-bold text-gray-800">Email Delivery Records</h3>
            <div class="relative max-w-sm w-full">
                <input type="text" id="logSearch" placeholder="Search logs..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:bg-white focus:border-blue-900 outline-none transition-all text-sm font-medium"
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
        <div id="paginationContainer" class="mt-6"></div>
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

    fetch(`api/get_email_logs.php?page=${page}&search=${encodeURIComponent(search)}`)
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
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 text-left">
                    <th class="pb-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="pb-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="pb-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="pb-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="pb-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="pb-3 px-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                ${logs.map(log => `
                    <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                        <td class="py-4 px-4">
                            <div class="font-semibold text-gray-800">${log.recipient_name || 'N/A'}</div>
                            <div class="text-xs text-gray-500">${log.recipient_email}</div>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-700 max-w-xs truncate">${log.subject}</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold ${getTypeColor(log.email_type)}">
                                ${log.email_type}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold ${log.status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                ${log.status === 'sent' ? '✓ Sent' : '✗ Failed'}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-sm text-gray-600">${formatDate(log.sent_at)}</td>
                        <td class="py-4 px-4 text-right space-x-2">
                            <button onclick='viewDetails(${JSON.stringify(log)})' class="text-blue-600 hover:text-blue-700 font-semibold text-sm">View</button>
                            <button onclick="deleteLog(${log.id})" class="text-red-600 hover:text-red-700 font-semibold text-sm">Delete</button>
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
            <button onclick="loadLogs(${i})" class="px-4 py-2 rounded-lg font-bold text-sm ${i === pagination.current_page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}">
                ${i}
            </button>
        `;
    }
    
    container.innerHTML = `
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">
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
        'voucher': 'bg-blue-100 text-blue-700',
        'box': 'bg-purple-100 text-purple-700',
        'campaign': 'bg-yellow-100 text-yellow-700',
        'manual': 'bg-gray-100 text-gray-700'
    };
    return colors[type] || 'bg-gray-100 text-gray-700';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

function viewDetails(log) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    
    content.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Recipient</label>
                <p class="text-gray-800 font-semibold">${log.recipient_name || 'N/A'} (${log.recipient_email})</p>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Subject</label>
                <p class="text-gray-800">${log.subject}</p>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Body</label>
                <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-700 whitespace-pre-wrap">${log.body || 'N/A'}</div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Type</label>
                    <p class="text-gray-800">${log.email_type}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Status</label>
                    <p class="text-gray-800">${log.status}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Sent At</label>
                    <p class="text-gray-800">${formatDate(log.sent_at)}</p>
                </div>
            </div>
            ${log.error_message ? `
                <div>
                    <label class="text-xs font-bold text-red-500 uppercase">Error Message</label>
                    <div class="p-4 bg-red-50 rounded-xl text-sm text-red-700">${log.error_message}</div>
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
            alert('Log deleted successfully');
            loadLogs(currentPage);
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deleteAllLogs() {
    if (!confirm('Are you sure you want to delete ALL email logs? This action cannot be undone.')) return;
    
    fetch('api/delete_all_email_logs.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('All logs deleted successfully');
                loadLogs(1);
            } else {
                alert('Error: ' + data.message);
            }
        });
}

// Load logs on page load
loadLogs(1);
</script>
