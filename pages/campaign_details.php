<?php 
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php'; 

$campaign_id = $_GET['id'] ?? null;
if (!$campaign_id) {
    echo "<div class='p-8 text-center text-red-500 font-bold'>Invalid Campaign ID</div>";
    return;
}

// Fetch Campaign Data
$stmt = $conn->prepare("SELECT * FROM email_campaigns WHERE id = ?");
$stmt->bind_param("i", $campaign_id);
$stmt->execute();
$campaign = $stmt->get_result()->fetch_assoc();
if (!$campaign) {
    echo "<div class='p-8 text-center text-red-500 font-bold'>Campaign not found</div>";
    return;
}

// Fetch Unique Opens
$openStmt = $conn->prepare("SELECT COUNT(DISTINCT recipient_email) as count FROM campaign_opens WHERE campaign_id = ?");
$openStmt->bind_param("i", $campaign_id);
$openStmt->execute();
$unique_opens = $openStmt->get_result()->fetch_assoc()['count'];

// Fetch Total Clicks
$clickStmt = $conn->prepare("SELECT COUNT(*) as count FROM campaign_clicks WHERE campaign_id = ?");
$clickStmt->bind_param("i", $campaign_id);
$clickStmt->execute();
$total_clicks = $clickStmt->get_result()->fetch_assoc()['count'];

// Fetch Link Analytics
$linkStmt = $conn->prepare("SELECT url, COUNT(*) as count FROM campaign_clicks WHERE campaign_id = ? GROUP BY url ORDER BY count DESC");
$linkStmt->bind_param("i", $campaign_id);
$linkStmt->execute();
$links = $linkStmt->get_result();

$unopened = max(0, $campaign['sent_count'] - $unique_opens);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Back & Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-2">
            <button onclick="window.location.href='index.php?page=campaign_list'" class="flex items-center gap-2 text-xs font-black text-blue-900 uppercase tracking-widest hover:translate-x-[-4px] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Campaigns
            </button>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight line-clamp-1"><?php echo htmlspecialchars($campaign['subject']); ?></h1>
            <p class="text-gray-500 font-medium">Sent on <?php echo date('M d, Y \a\t h:i A', strtotime($campaign['sent_at'])); ?></p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-panel p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Sent</p>
                <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($campaign['sent_count']); ?></h3>
            </div>
        </div>
        <div class="glass-panel p-6 flex items-center gap-4 border-l-4 border-l-green-500">
            <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Unique Opens</p>
                <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($unique_opens); ?></h3>
            </div>
        </div>
        <div class="glass-panel p-6 flex items-center gap-4 border-l-4 border-l-orange-500">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Clicks</p>
                <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($total_clicks); ?></h3>
            </div>
        </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Open Rate Visual -->
        <div class="lg:col-span-1 glass-panel p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Open Rate Distribution</h3>
            <div class="relative aspect-square">
                <canvas id="openRateChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2 text-gray-500"><span class="w-3 h-3 rounded-full bg-blue-900"></span> Opened</span>
                    <span class="font-black text-gray-800"><?php echo $unique_opens; ?></span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2 text-gray-500"><span class="w-3 h-3 rounded-full bg-gray-200"></span> Unopened</span>
                    <span class="font-black text-gray-800"><?php echo $unopened; ?></span>
                </div>
            </div>
        </div>

        <!-- Link Analytics -->
        <div class="lg:col-span-2 glass-panel overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                <h3 class="text-lg font-bold text-gray-800">Link Performance</h3>
                <p class="text-xs text-gray-500 font-medium">Tracking which URLs are getting the most clicks</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Destination URL</th>
                            <th class="px-6 py-4 text-center">Clicks</th>
                            <th class="px-6 py-4 text-right">% Contribution</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php 
                        if ($links->num_rows === 0): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium italic">
                                    No click data available for this campaign.
                                </td>
                            </tr>
                        <?php endif;

                        while($l = $links->fetch_assoc()): 
                            $percentage = $total_clicks > 0 ? round(($l['count'] / $total_clicks) * 100, 1) : 0;
                        ?>
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-blue-900 truncate max-w-xs md:max-w-md">
                                    <?php echo htmlspecialchars($l['url']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg font-black text-xs">
                                    <?php echo $l['count']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1">
                                    <div class="bg-blue-900 h-1.5 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400"><?php echo $percentage; ?>%</span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Email Logs Section -->
    <div class="glass-panel overflow-hidden mt-8">
        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Email Delivery Logs</h3>
                <p class="text-xs text-gray-500 font-medium">Individual email records for this campaign</p>
            </div>
            <div class="relative max-w-sm w-full">
                <input type="text" id="logSearch" placeholder="Search recipients or subject..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition-all text-sm font-medium"
                    onkeyup="debounceSearch()">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
        
        <div id="campaignLogsContainer" class="overflow-x-auto">
            <div class="flex items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        </div>

        <!-- Pagination -->
        <div id="logsPaginationContainer" class="p-6 border-t border-gray-50"></div>
    </div>
</div>

<script>
let currentLogPage = 1;
const campaignId = <?php echo $campaign_id; ?>;
let searchTimeout = null;

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadCampaignLogs(1);
    }, 500);
}

// Load campaign-specific email logs
function loadCampaignLogs(page = 1) {
    currentLogPage = page;
    const search = document.getElementById('logSearch').value;
    const container = document.getElementById('campaignLogsContainer');
    
    // Show loading state
    container.innerHTML = `
        <div class="flex items-center justify-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    `;

    fetch(`api/get_email_logs.php?page=${page}&campaign_id=${campaignId}&search=${encodeURIComponent(search)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderCampaignLogs(data.logs);
                renderLogsPagination(data.pagination);
            }
        })
        .catch(err => {
            console.error('Error loading logs:', err);
            container.innerHTML = '<div class="text-center py-10 text-red-500 font-bold">Failed to load logs</div>';
        });
}

function renderCampaignLogs(logs) {
    const container = document.getElementById('campaignLogsContainer');
    
    if (logs.length === 0) {
        container.innerHTML = `
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No Email Logs</h3>
                <p class="text-gray-500">No email delivery records found matching your criteria</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <table class="w-full text-left">
            <thead>
                <tr class="bg-blue-900 text-white text-[10px] uppercase tracking-widest font-black">
                    <th class="px-6 py-5">Recipient</th>
                    <th class="px-6 py-5">Subject</th>
                    <th class="px-6 py-5 text-center">Status</th>
                    <th class="px-6 py-5">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                ${logs.map(log => `
                    <tr class="hover:bg-blue-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="font-bold text-gray-800">${log.recipient_name || 'N/A'}</div>
                            <div class="text-xs text-gray-400">${log.recipient_email}</div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-700 max-w-xs truncate">${log.subject}</td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black ${log.status === 'sent' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100'}">
                                ${log.status === 'sent' ? '✓ SENT' : '✗ FAILED'}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-gray-700">${formatLogDate(log.sent_at)}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">${formatLogTime(log.sent_at)}</div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderLogsPagination(pagination) {
    const container = document.getElementById('logsPaginationContainer');
    
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let pages = '';
    for (let i = 1; i <= pagination.total_pages; i++) {
        pages += `
            <button onclick="loadCampaignLogs(${i})" class="px-4 py-2 rounded-lg font-bold text-sm transition-all ${i === pagination.current_page ? 'bg-blue-900 text-white shadow-lg shadow-blue-900/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}">
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

function formatLogDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatLogTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

// Load logs on page load
loadCampaignLogs(1);

const ctx = document.getElementById('openRateChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Opened', 'Unopened'],
        datasets: [{
            data: [<?php echo $unique_opens; ?>, <?php echo $unopened; ?>],
            backgroundColor: ['#1e3a8a', '#e5e7eb'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: true,
                padding: 12,
                backgroundColor: 'rgba(30, 58, 138, 0.9)',
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 12 }
            }
        }
    }
});
</script>
