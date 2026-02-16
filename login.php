<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dosti Voucher Donor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .login-bg {
            background-color: #f3f4f6;
            background-image: 
                radial-gradient(at 0% 0%, rgba(9, 83, 156, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 203, 5, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="login-bg h-screen flex items-center justify-center p-4">

    <div class="glass-panel w-full max-w-md p-8 md:p-10 animate-fade-in relative overflow-hidden">
        <!-- Decorative Blob -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-400 rounded-full mix-blend-multiply filter blur-2xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-600 rounded-full mix-blend-multiply filter blur-2xl opacity-20 animate-blob animation-delay-2000"></div>

        <div class="relative z-10">
            <div class="text-center mb-10">
                <div class="flex flex-col items-center mb-6">
                    <img src="assets/images/Dosti-Logo.png" alt="Dosti Welfare" class="h-20 w-auto object-contain mb-2">
                    <div class="h-1 w-12 bg-blue-600 rounded-full"></div>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Welcome Back</h1>
                <p class="text-gray-500 text-sm mt-1">Sign in to access your dashboard</p>
            </div>
            
            <form id="login-form" class="space-y-6">
                <div id="error-msg" class="hidden flex items-center p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-100" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span id="error-text">Credential error</span>
                </div>

                <div class="space-y-4">
                    <div class="relative">
                        <input type="text" id="username" name="username" required 
                            class="block px-4 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-white/50 border border-gray-300 rounded-xl appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer transition-all" placeholder=" " />
                        <label for="username" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-transparent px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-2">Username</label>
                    </div>

                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                            class="block px-4 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-white/50 border border-gray-300 rounded-xl appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer transition-all" placeholder=" " />
                        <label for="password" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-transparent px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-2">Password</label>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                          <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300">
                        </div>
                        <label for="remember" class="ml-2 text-sm font-medium text-gray-500">Remember me</label>
                    </div>
                    <a href="#" class="text-sm text-blue-600 hover:underline">Lost Password?</a>
                </div>

                <button type="submit" 
                    class="w-full text-white bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-800 hover:to-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-3.5 text-center transition-all transform hover:scale-[1.02] shadow-lg shadow-blue-500/20">
                    Sign in to Account
                </button>

                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500 font-medium">Are you a restaurant partner?</p>
                    <a href="partner_login.php" class="inline-block mt-2 px-6 py-2.5 bg-yellow-50 text-yellow-700 font-bold text-xs rounded-xl border border-yellow-100/50 hover:bg-yellow-100 transition-all">
                        Partner Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const errorDiv = document.getElementById('error-msg');
            const errorText = document.getElementById('error-text');
            const btn = e.target.querySelector('button[type="submit"]');
            
            // Loading State
            const originalBtnText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg aria-hidden="true" role="status" class="inline w-4 h-4 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/></svg>Signing In...';

            try {
                formData.append('login_type', 'admin');
                const res = await fetch('api/auth.php', { method: 'POST', body: formData });
                const result = await res.json();
                
                await new Promise(r => setTimeout(r, 800)); // Artificial delay for effect

                if (result.success) {
                    window.location.href = 'index.php';
                } else {
                    errorText.textContent = result.message || 'Invalid credentials';
                    errorDiv.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                }
            } catch (err) {
                console.error(err);
                errorText.textContent = 'Connection error. Please try again.';
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
            }
        });
    </script>
</body>
</html>
