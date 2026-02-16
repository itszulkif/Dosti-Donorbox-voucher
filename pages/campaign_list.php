<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);

include __DIR__ . '/../config.php'; ?>
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Email Campaigns</h1>
            <p class="text-gray-500 font-medium">Performance history of your broadcasted messages</p>
        </div>
        <button onclick="window.location.href='index.php?page=automation'" class="px-6 py-3 bg-blue-900 text-white font-black rounded-2xl shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            New Campaign
        </button>
    </div>

    <!-- Campaigns Table -->
    <div class="glass-panel overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Completed Campaigns</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-900 text-white text-[10px] uppercase tracking-widest font-black">
                        <th class="px-6 py-5">Date Sent</th>
                        <th class="px-6 py-5">Campaign Subject</th>
                        <th class="px-6 py-5">Audience</th>
                        <th class="px-6 py-5 text-center">Sent</th>
                        <th class="px-6 py-5 text-center">Opens</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $campaigns = $conn->query("
                        SELECT c.*, 
                        (SELECT COUNT(DISTINCT recipient_email) FROM campaign_opens WHERE campaign_id = c.id) as unique_opens 
                        FROM email_campaigns c 
                        ORDER BY sent_at DESC
                    ");
                    
                    if ($campaigns->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">
                                No campaigns launched yet.
                            </td>
                        </tr>
                    <?php endif;
                    
                    while($c = $campaigns->fetch_assoc()):
                        $open_rate = $c['sent_count'] > 0 ? round(($c['unique_opens'] / $c['sent_count']) * 100, 1) : 0;
                    ?>
                    <tr class="group hover:bg-blue-50/30 transition-all cursor-pointer" onclick="window.location.href='index.php?page=campaign_details&id=<?php echo $c['id']; ?>'">
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-gray-700"><?php echo date('M d, Y', strtotime($c['sent_at'])); ?></div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase"><?php echo date('h:i A', strtotime($c['sent_at'])); ?></div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="font-bold text-gray-800 line-clamp-1"><?php echo htmlspecialchars($c['subject']); ?></div>
                        </td>
                        <td class="px-6 py-5 text-sm">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?php echo $c['audience_type'] === 'donor' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-yellow-50 text-yellow-600 border border-yellow-100'; ?>">
                                <?php echo $c['audience_type']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center font-bold text-gray-700">
                            <?php echo number_format($c['sent_count']); ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="text-sm font-bold text-blue-600"><?php echo $c['unique_opens']; ?></div>
                            <div class="text-[10px] text-gray-400 font-bold"><?php echo $open_rate; ?>% Rate</div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="window.location.href='index.php?page=campaign_details&id=<?php echo $c['id']; ?>'" class="p-2 bg-white text-blue-900 border border-gray-100 rounded-xl shadow-sm hover:scale-110 transition-all" title="View Analytics">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </button>
                                <button onclick="deleteCampaign(<?php echo $c['id']; ?>)" class="p-2 bg-white text-red-500 border border-gray-100 rounded-xl shadow-sm hover:bg-red-500 hover:text-white transition-all" title="Delete Campaign">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function deleteCampaign(campaignId) {
    if (!confirm('Are you sure you want to delete this campaign? This will also delete all associated analytics data and cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('campaign_id', campaignId);
    
    fetch('api/delete_campaign.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Campaign deleted successfully');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to delete campaign');
    });
}
</script>
