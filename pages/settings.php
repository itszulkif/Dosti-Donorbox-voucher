<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
$tab = $_GET['tab'] ?? 'admins';

$settings_tabs = [
    'admins' => [
        'name' => 'Admin Access',
        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
        'file' => 'admins.php'
    ],
    // Future tabs can be added here
    'general' => [
        'name' => 'General Settings',
        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'file' => 'general_settings.php'
    ],
    'smtp' => [
        'name' => 'Email SMTP',
        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
        'file' => 'smtp_settings.php'
    ],
    'templates' => [
        'name' => 'Email Templates',
        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
        'file' => 'email_templates.php'
    ],
    'email_logs' => [
        'name' => 'Email Logs',
        'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'file' => 'email_logs.php'
    ]
];

if (!array_key_exists($tab, $settings_tabs)) {
    $tab = 'admins';
}
?>

<div class="flex flex-col lg:flex-row gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Settings Sidebar -->
    <aside class="w-full lg:w-72 shrink-0">
        <div class="glass-panel p-4 space-y-1">
            <p class="px-4 pt-2 pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-4">Configuration Hub</p>
            
            <?php foreach ($settings_tabs as $key => $config): ?>
                <a href="index.php?page=settings&tab=<?php echo $key; ?>" 
                   class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 font-bold text-sm
                          <?php echo $tab === $key 
                                ? 'bg-blue-900 text-white shadow-lg shadow-blue-900/20 active-glow' 
                                : 'text-gray-500 hover:bg-blue-50 hover:text-blue-700'; ?>">
                    <span class="<?php echo $tab === $key ? 'text-white' : 'text-gray-400'; ?>">
                        <?php echo $config['icon']; ?>
                    </span>
                    <?php echo $config['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-8 p-6 glass-panel bg-gradient-to-br from-blue-900 to-blue-800 text-white shadow-xl">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h5 class="font-bold mb-1">System Security</h5>
            <p class="text-xs text-blue-100 leading-relaxed opacity-80">Only Super Admins can access and modify these configuration settings. Be careful with changes.</p>
        </div>
    </aside>

    <!-- Settings Content Area -->
    <div class="flex-1">
        <?php 
        $currentFile = $settings_tabs[$tab]['file'];
        if ($currentFile && file_exists(__DIR__ . '/' . $currentFile)) {
            include __DIR__ . '/' . $currentFile;
        } else {
            echo '
            <div class="glass-panel p-20 text-center">
                <div class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center text-blue-300 mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Coming Soon</h3>
                <p class="text-gray-500 max-w-xs mx-auto">This configuration area is currently under development. Stay tuned for updates!</p>
            </div>';
        }
        ?>
    </div>
</div>

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
