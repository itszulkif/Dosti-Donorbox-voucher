<?php
require_once __DIR__ . '/SimpleMailer.php';
require_once __DIR__ . '/email_logger.php';

class CampaignSender {
    private $conn;
    private $settings;
    private $baseUrl;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->loadSettings();
        $this->detectBaseUrl();
    }

    private function loadSettings() {
        $settingsResult = $this->conn->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'smtp_%'");
        $this->settings = [];
        while ($row = $settingsResult->fetch_assoc()) {
            $this->settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    private function detectBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Check if running from CLI
        if (php_sapi_name() === 'cli') {
            // Default to extensive guess or hardcoded if needed, but for now assuming localhost structure or configured
            // In production, this should ideally be a setting
            $this->baseUrl = "http://localhost/Dosti_Voucher_Donors"; 
        } else {
            $script_path = $_SERVER['PHP_SELF'];
            $base_dir = dirname(dirname($script_path));
            $this->baseUrl = $protocol . $host . $base_dir;
        }
    }

    public function sendCampaign($campaign_id, $subject, $body, $recipients) {
        if (empty($this->settings['smtp_host'])) {
            return ['success' => false, 'message' => 'SMTP not configured'];
        }

        $sent_count = 0;
        $fail_count = 0;

        foreach ($recipients as $person) {
            $personal_subject = $subject;
            $personal_body = $body;

            // Replace Tags
            $tags = [
                '{{name}}' => $person['name'] ?? '',
                '{{email}}' => $person['email'] ?? '',
                '{{phone}}' => $person['phone'] ?? '',
                '{{voucher_id}}' => $person['voucher_id'] ?? '',
                '{{shop_name}}' => $person['shop_name'] ?? '',
                '{{box_number}}' => $person['box_number'] ?? '',
            ];

            foreach ($tags as $tag => $val) {
                $personal_subject = str_replace($tag, $val, $personal_subject);
                $personal_body = str_replace($tag, $val, $personal_body);
            }

            // --- TRACKING LOGIC ---
            // 1. Open Tracking (Pixel)
            $tracking_pixel = "<img src='{$this->baseUrl}/api/track_open.php?c={$campaign_id}&e=" . urlencode($person['email']) . "' width='1' height='1' style='display:none' />";
            if (strpos($personal_body, '</body>') !== false) {
                $personal_body = str_replace('</body>', $tracking_pixel . '</body>', $personal_body);
            } else {
                $personal_body .= $tracking_pixel;
            }

            // 2. Click Tracking (Link Rewriting)
            $personal_body = preg_replace_callback('/<a\b[^>]*href=["\']([^"\']+)["\'][^>]*>/i', function($matches) use ($campaign_id, $person) {
                $original_url = $matches[1];
                // Don't track anchors or mailto
                if (strpos($original_url, '#') === 0 || strpos($original_url, 'mailto:') === 0) {
                    return $matches[0];
                }
                $tracking_url = "{$this->baseUrl}/api/track_click.php?c={$campaign_id}&e=" . urlencode($person['email']) . "&u=" . urlencode($original_url);
                return str_replace($original_url, $tracking_url, $matches[0]);
            }, $personal_body);

            $mail = new SimpleMailer();
            $mail->setHost($this->settings['smtp_host']);
            $mail->setPort($this->settings['smtp_port'] ?? 465);
            $mail->setUsername($this->settings['smtp_user']);
            $mail->setPassword($this->settings['smtp_pass']);
            $mail->setEncryption($this->settings['smtp_encryption'] ?? 'ssl');
            $mail->setFrom($this->settings['smtp_from_email'] ?? $this->settings['smtp_user'], $this->settings['smtp_from_name'] ?? 'Dosti Welfare');
            $mail->addAddress($person['email'], $person['name']);
            $mail->setSubject($personal_subject);
            $mail->setBody($personal_body);

            if ($mail->send()) {
                $sent_count++;
                logEmail($this->conn, $person['email'], $person['name'], $personal_subject, $personal_body, 'sent', null, 'campaign', $campaign_id);
            } else {
                $fail_count++;
                logEmail($this->conn, $person['email'], $person['name'], $personal_subject, $personal_body, 'failed', $mail->getError(), 'campaign', $campaign_id);
            }
        }

        return ['success' => true, 'sent' => $sent_count, 'failed' => $fail_count];
    }
}
?>
