<?php
session_start();
if (isset($_SESSION['partner_id'])) {
    header('Location: redeem.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login | Dosti Welfare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 p-8">
        <div class="flex flex-col items-center mb-8">
            <img src="assets/images/Dosti-Logo.png" alt="Dosti Welfare" class="h-20 w-auto object-contain mb-2">
            <div class="h-1 w-12 bg-blue-600 rounded-full"></div>
        </div>
        
        <div class="text-center mb-8">
            <h1 class="text-gray-800 text-3xl font-black tracking-tighter uppercase">Partner Login</h1>
            <p class="text-gray-400 mt-2 font-medium">Redeem vouchers at your location</p>
        </div>
        
        <form id="partner-login-form" class="p-8 space-y-6">
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Username</label>
                <input type="text" name="username" required
                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold text-gray-700">
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold text-gray-700">
            </div>

            <div id="error-msg" class="hidden p-4 bg-red-50 text-red-600 rounded-2xl text-sm font-bold border border-red-100"></div>

                <button type="submit" 
                    class="w-full text-white bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center transition-all transform hover:scale-[1.02] shadow-lg shadow-yellow-500/20">
                    Sign in to Portal
                </button>

                <div class="mt-8 text-center border-t border-gray-100 pt-6">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-3">Administrator Access</p>
                    <a href="login.php" class="text-blue-600 font-bold text-xs hover:underline flex items-center justify-center gap-2">
                        System Login
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </form>
    </div>

    <script>
        document.getElementById('partner-login-form').onsubmit = async (e) => {
            e.preventDefault();
            const form = document.getElementById('partner-login-form');
            const btn = form.querySelector('button[type="submit"]');
            const err = document.getElementById('error-msg');
            
            btn.disabled = true;
            btn.innerText = 'Verifying...';
            err.classList.add('hidden');

            try {
                const formData = new FormData(form);
                formData.append('login_type', 'partner');
                const res = await fetch('api/auth.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = 'redeem.php';
                } else {
                    err.innerText = data.message;
                    err.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerText = 'Sign In to Dashboard';
                }
            } catch (error) {
                console.error(error);
                err.innerText = 'Connection error. Please try again.';
                err.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = 'Sign In to Dashboard';
            }
        };
    </script>
</body>
</html>
