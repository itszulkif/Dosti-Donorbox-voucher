<?php
// Centralized authentication and authorization helper

/**
 * Ensures user is logged in, redirects to login if not
 */
function require_auth() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../login.php");
        exit;
    }
    
    // Redirect partners to their dashboard
    if (!empty($_SESSION['partner_id'])) {
        header("Location: ../redeem.php");
        exit;
    }
}

/**
 * Checks if user has one of the required roles
 * @param array $allowed_roles Array of allowed role names
 */
function require_role($allowed_roles) {
    require_auth();
    
    $user_role = $_SESSION['admin_role'] ?? 'admin';
    
    if (!in_array($user_role, $allowed_roles)) {
        http_response_code(403);
        die('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Access Denied</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 h-screen flex items-center justify-center">
            <div class="text-center">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Access Denied</h1>
                <p class="text-gray-600 mb-6">You do not have permission to access this page.</p>
                <a href="../index.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Return to Dashboard
                </a>
            </div>
        </body>
        </html>
        ');
    }
}

/**
 * Check if user has permission to access a specific page
 * @param string $page Page identifier
 * @return bool
 */
function has_permission($page) {
    $user_role = $_SESSION['admin_role'] ?? 'admin';
    
    // Super admin has access to everything
    if ($user_role === 'super_admin') {
        return true;
    }
    
    // Define page permissions
    $permissions = [
        'voucher_editor' => ['add_donor'],
        'box_editor' => ['log_visit']
    ];
    
    $allowed_pages = $permissions[$user_role] ?? [];
    return in_array($page, $allowed_pages);
}

/**
 * Get user's full name from session
 * @return string
 */
function get_user_name() {
    return $_SESSION['full_name'] ?? $_SESSION['admin_username'] ?? 'User';
}

/**
 * Get user's role display name
 * @return string
 */
function get_role_display() {
    $role = $_SESSION['admin_role'] ?? 'super_admin';
    $displays = [
        'super_admin' => 'Super Admin',
        'voucher_editor' => 'Voucher Editor',
        'box_editor' => 'Box Editor'
    ];
    return $displays[$role] ?? 'User';
}
?>
