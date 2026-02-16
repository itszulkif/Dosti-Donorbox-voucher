<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect partners away from the admin dashboard
if (!empty($_SESSION['partner_id'])) {
    header("Location: redeem.php");
    exit;
}

// Routing Logic
$page = $_GET['page'] ?? 'analytics';
$allowed_pages = [
    'analytics' => 'Analytics Overview',
    'add_donor' => 'Management Hub',
    'donors_list' => 'All Donors',
    'restaurants_list' => 'Restaurants & Partners',
    'box_analytics' => 'Box Analytics',
    'donation_boxes' => 'Shops & Boxes',
    'log_visit' => 'Log Collection',
    'visit_history' => 'Collection History',
    'automation' => 'Task Automation',
    'campaign_list' => 'Campaign Reports',
    'campaign_details' => 'Campaign Impact',
    'campaign_email_logs' => 'Email Logs',
    'settings' => 'System Settings',
    'admins' => 'Admin Access',
    'shop_list' => 'All Donation Shops'
];

if (!array_key_exists($page, $allowed_pages)) {
    $page = 'analytics';
}

$pageTitle = $allowed_pages[$page];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosti Voucher Donor - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
    <!-- Tippy.js for Tooltips -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/shift-away.css" />
</head>
<body class="flex h-screen overflow-hidden text-gray-800 antialiased">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar-premium fixed md:relative inset-y-0 left-0 w-72 text-white z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col font-medium">
        <!-- Logo Area -->
        <div class="h-24 flex items-center px-6 border-b border-white/10">
            <div class="flex items-center justify-center w-full">
                <img src="assets/images/Dosti-Logo.png" alt="Dosti Welfare" class="max-h-16 w-auto object-contain brightness-0 invert">
            </div>
            <button id="close-sidebar" class="md:hidden ml-auto text-white/70 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
       

        <!-- Navigation -->
        <nav class="mt-8 flex-1 px-4 space-y-2">
            <?php 
            $user_role = $_SESSION['admin_role'] ?? 'admin';
            $is_super_admin = ($user_role === 'super_admin');
            $is_voucher_editor = ($user_role === 'voucher_editor');
            $is_box_editor = ($user_role === 'box_editor');
            ?>
            
            <?php if ($is_super_admin): ?>
            <a href="index.php?page=analytics" class="sidebar-link <?php echo $page === 'analytics' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Voucher Overview
            </a>
            <?php endif; ?>
            
            <?php if ($is_super_admin || $is_voucher_editor): ?>
            <a href="index.php?page=add_donor" class="sidebar-link <?php echo $page === 'add_donor' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Management Hub
            </a>
            <?php endif; ?>

            <?php if ($is_super_admin): ?>
            <a href="index.php?page=donors_list" class="sidebar-link <?php echo $page === 'donors_list' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                All Donors
            </a>
           
            <a href="index.php?page=restaurants_list" class="sidebar-link <?php echo $page === 'restaurants_list' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Restaurants & Partners
            </a>

            <p class="px-4 text-xs font-bold text-blue-200/50 uppercase tracking-wider mt-8 mb-2">Donation Boxes</p>
            <a href="index.php?page=box_analytics" class="sidebar-link <?php echo $page === 'box_analytics' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Box Analytics
            </a>
            <a href="index.php?page=donation_boxes" class="sidebar-link <?php echo $page === 'donation_boxes' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Shops & Boxes
            </a>
            <?php endif; ?>
            
            <?php if ($is_super_admin || $is_box_editor): ?>
            <a href="index.php?page=log_visit" class="sidebar-link <?php echo $page === 'log_visit' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Log Collection
            </a>
            <?php endif; ?>
            
            <?php if ($is_super_admin): ?>
            <a href="index.php?page=campaign_list" class="sidebar-link <?php echo ($page === 'campaign_list' || $page === 'campaign_details' || $page === 'campaign_email_logs') ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Campaign Reports
            </a>
           
            
            <a href="index.php?page=settings" class="sidebar-link <?php echo $page === 'settings' ? 'active' : ''; ?> flex items-center px-4 py-3.5 text-sm">
                 <svg class="w-5 h-5 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </a>
            <?php endif; ?>
        </nav>

        <!-- User Profile Area -->
        <div class="p-4 m-4 rounded-xl bg-white/10 backdrop-blur-md border border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white text-blue-900 flex items-center justify-center font-bold shadow-md">
                    <?php 
                    $name = $_SESSION['full_name'] ?? $_SESSION['admin_username'] ?? 'Admin';
                    echo strtoupper(substr($name, 0, 2)); 
                    ?>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars($name); ?></p>
                    <p class="text-xs text-blue-200">
                        <?php 
                        $role_badges = [
                            'super_admin' => 'Super Admin',
                            'voucher_editor' => 'Voucher Editor',
                            'box_editor' => 'Box Editor'
                        ];
                        echo $role_badges[$_SESSION['admin_role']] ?? 'User';
                        ?>
                    </p>
                </div>
            </div>
            <a href="logout.php" class="mt-3 flex items-center justify-center w-full py-2 bg-red-500/20 hover:bg-red-500/30 text-red-100 text-xs font-bold rounded-lg transition-colors border border-red-500/30">
                Log Out
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full relative z-0">
        <!-- Glass Header -->
        <header class="glass-header h-20 flex items-center justify-between px-6 md:px-10 z-20 sticky top-0">
            <div class="flex items-center gap-4">
                 <button id="open-sidebar" class="md:hidden p-2 text-gray-500 hover:text-blue-700 transition-colors rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h2 id="page-title" class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight"><?php echo $pageTitle; ?></h2>
                    <p class="text-xs md:text-sm text-gray-500 hidden md:block">Welcome back to the Dosti Admin Panel</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button class="p-2.5 text-gray-400 hover:text-blue-600 transition-colors bg-white hover:bg-gray-50 rounded-full shadow-sm border border-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
                <div class="h-8 w-px bg-gray-300 mx-2 hidden md:block"></div>
                <div class="text-right hidden md:block">
                    <p class="text-sm font-semibold text-gray-700"><?php echo date('F j, Y'); ?></p>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Container -->
        <div id="main-content" class="flex-1 overflow-y-auto w-full p-4 md:p-8 md:px-10 relative scroll-smooth">
            <?php include "pages/{$page}.php"; ?>
        </div>
    </main>

    <script src="assets/js/table-handler.js"></script>
    <script src="assets/js/app.js?v=<?php echo time(); ?>"></script>

    <!-- Global Modals -->
    <div id="modal-container" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
        <div id="modal-content" class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
            <!-- Modal content injected via JS -->
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 transform transition-all duration-300 scale-95 opacity-0 inline-block">
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Delete</h3>
            <p class="text-gray-500 mb-8">Are you sure you want to remove this record? This action cannot be undone.</p>
            <div class="flex gap-4">
                <button id="cancel-delete" class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">Cancel</button>
                <button id="confirm-delete" class="flex-1 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-600/20 transition-all">Delete Forever</button>
            </div>
        </div>
    </div>
</body>
</html>
