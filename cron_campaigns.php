<?php
// cron_campaigns.php - Process Scheduled Campaigns
// Run this script via Cron Job or Windows Task Scheduler every minute
// Example: * * * * * php /path/to/cron_campaigns.php

// Ensure we are in the correct directory for relative includes if run from CLI
chdir(__DIR__);

require_once 'config.php';
require_once 'lib/CampaignSender.php';

// 1. Find Pending Campaigns due for sending
$now = date('Y-m-d H:i:s');
$query = "SELECT sc.id, sc.template_id, sc.audience_type, sc.scheduled_at 
          FROM scheduled_campaigns sc 
          WHERE sc.scheduled_at <= ? AND sc.status = 'pending' 
          LIMIT 5"; 

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $now);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Nothing to process
    echo "No pending campaigns due.\n";
    exit;
}

$sender = new CampaignSender($conn);

while ($job = $result->fetch_assoc()) {
    echo "Processing Job ID: " . $job['id'] . "\n";
    
    // Mark as processing (optional, but good for safety if we had a 'processing' status)
    // For now we just process immediately.

    // 2. Fetch Template Data
    $tplStmt = $conn->prepare("SELECT subject, body FROM campaign_templates WHERE id = ?");
    $tplStmt->bind_param("i", $job['template_id']);
    $tplStmt->execute();
    $template = $tplStmt->get_result()->fetch_assoc();
    $tplStmt->close();

    if (!$template) {
        echo "Error: Template ID {$job['template_id']} not found. Marking job as failed.\n";
        $upd = $conn->prepare("UPDATE scheduled_campaigns SET status = 'failed' WHERE id = ?");
        $upd->bind_param("i", $job['id']);
        $upd->execute();
        continue;
    }

    // 3. Create Campaign Record (for Analytics)
    // We create a new entry in email_campaigns for this specific run
    $campStmt = $conn->prepare("INSERT INTO email_campaigns (subject, body, audience_type, sent_at) VALUES (?, ?, ?, NOW())");
    $campStmt->bind_param("sss", $template['subject'], $template['body'], $job['audience_type']);
    $campStmt->execute();
    $campaign_id = $campStmt->insert_id;
    $campStmt->close();

    echo "Created Campaign ID: $campaign_id\n";

    // 4. Fetch Recipients
    $recipients = [];
    if ($job['audience_type'] === 'shopkeeper') {
        $res = $conn->query("SELECT contact_person as name, email, phone, shop_name, box_number FROM donation_shops WHERE email != '' AND email IS NOT NULL");
    } else {
        // Default to donors
        $res = $conn->query("SELECT name, email, phone, voucher_id FROM donors WHERE email != '' AND email IS NOT NULL");
    }

    while ($row = $res->fetch_assoc()) {
        $recipients[] = $row;
    }

    if (empty($recipients)) {
        echo "No recipients found. Marking as completed.\n";
        $status = 'sent'; // Technically sent to 0 people
    } else {
        // 5. Send Emails
        $sendResult = $sender->sendCampaign($campaign_id, $template['subject'], $template['body'], $recipients);
        
        // Update Campaign Stats
        $updCamp = $conn->prepare("UPDATE email_campaigns SET sent_count = ?, fail_count = ? WHERE id = ?");
        $updCamp->bind_param("iii", $sendResult['sent'], $sendResult['failed'], $campaign_id);
        $updCamp->execute();
        $updCamp->close();
        
        echo "Sent: {$sendResult['sent']}, Failed: {$sendResult['failed']}\n";
        $status = 'sent';
    }

    // 6. Update Schedule Status
    $updJob = $conn->prepare("UPDATE scheduled_campaigns SET status = ? WHERE id = ?");
    $updJob->bind_param("si", $status, $job['id']);
    $updJob->execute();
    $updJob->close();
    
    echo "Job {$job['id']} completed.\n";
}

echo "Done.\n";
?>
