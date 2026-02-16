<?php
session_start();
include 'config.php';

// Require login
if (!isset($_SESSION['admin_id'])) {
    header('Location: partner_login.php');
    exit;
}

// Check if partner
$partner_id = $_SESSION['partner_id'] ?? null;
$is_partner = !empty($partner_id);
$restaurant_name = '';

if ($is_partner) {
    $stmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $partner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $restaurant_name = $row['name'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Redemption | Dosti Welfare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #09539c;
            --action-yellow: #ffcb05;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .bg-royal-blue { background-color: var(--primary-blue); }
        .text-royal-blue { color: var(--primary-blue); }
        .bg-golden-yellow { background-color: var(--action-yellow); }
        .border-royal-blue { border-color: var(--primary-blue); }
        
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            z-index: 100;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-blur: 8px;
        }

        .checkmark-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #4bb543;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #4bb543;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 50px #4bb543; } }

        .btn-primary {
            background-color: var(--primary-blue);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:active {
            transform: scale(0.98);
        }
        .btn-yellow {
            background-color: var(--action-yellow);
            color: #000;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .btn-yellow:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center p-4">

    <!-- Header / Logo -->
    <div class="w-full max-w-md flex flex-col items-center mt-8 mb-10">
        <img src="assets/images/Dosti-Logo.png" alt="Dosti Welfare" class="h-24 w-auto object-contain mb-4">
        <p class="text-gray-500 font-medium text-sm">Voucher Redemption Portal</p>
        
        <?php if ($restaurant_name): ?>
            <div class="mt-2 px-4 py-1.5 bg-royal-blue/5 border border-royal-blue/10 rounded-full">
                <p class="text-royal-blue font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <?php echo htmlspecialchars($restaurant_name); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="mt-4 flex flex-wrap justify-center gap-4">
            <?php if (!$is_partner): ?>
            <a href="index.php" class="text-royal-blue text-xs font-bold hover:underline flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Admin Dashboard
            </a>
            <?php endif; ?>
            
            <a href="logout.php" class="text-red-500 text-xs font-bold hover:underline flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V7a3 3 0 00-6 0v1"></path></svg>
                Logout Account
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-blue-900/5 p-6 border border-gray-100">
        
        <!-- Input Section -->
        <div id="input-section">
            <label for="voucher_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">Voucher ID</label>
            <div class="relative">
                <input type="text" id="voucher_id" placeholder="Enter ID (e.g. 10, 12020, V-1)" 
                    class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-royal-blue/30 focus:bg-white rounded-2xl text-2xl font-bold text-gray-800 outline-none transition-all placeholder:text-gray-300">
            </div>
            <button id="btn-check" class="w-full mt-4 btn-primary py-4 rounded-2xl font-bold text-lg shadow-lg shadow-blue-600/20">
                Check Status
            </button>
        </div>

        <!-- Skeleton Loader (Hidden by default) -->
        <div id="skeleton-loader" class="hidden mt-8 space-y-4">
            <div class="h-24 w-full skeleton rounded-2xl"></div>
            <div class="h-14 w-full skeleton rounded-2xl"></div>
            <div class="h-16 w-full skeleton rounded-2xl"></div>
        </div>

        <!-- Result Card (Hidden by default) -->
        <div id="result-card" class="hidden mt-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div id="status-container" class="p-5 rounded-2xl border-2 mb-4">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Donor Information</p>
                    <span id="status-badge" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter"></span>
                </div>
                <h3 id="donor-name" class="text-xl font-bold text-gray-800"></h3>
            </div>

            <div id="redemption-form" class="hidden transition-all duration-300">
                <?php if (!$is_partner): ?>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-1">Partner Redemption Status</label>
                    <div id="assignment-list" class="space-y-2">
                        <!-- Dynamic List -->
                    </div>
                </div>

                <label for="restaurant" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Redeem At</label>
                <div class="relative group">
                    <select id="restaurant" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl font-bold text-gray-700 outline-none focus:border-royal-blue/20 transition-all appearance-none cursor-pointer">
                        <option value="" disabled selected>Choose assigned location...</option>
                    </select>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <?php endif; ?>

                <button id="btn-redeem" class="w-full mt-6 btn-yellow py-4 rounded-2xl text-lg shadow-lg shadow-yellow-500/20">
                    Confirm Redemption
                </button>
            </div>
            
            <div id="error-container" class="hidden p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-sm font-medium text-center">
            </div>
        </div>
    </div>

    <!-- Success Overlay -->
    <div id="success-overlay" class="success-overlay">
        <svg class="checkmark-circle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark-circle-inner" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
        <h2 class="mt-8 text-3xl font-black text-gray-800 text-center px-6">Redemption Successful!</h2>
        <p id="success-msg" class="mt-3 text-gray-500 font-medium text-center px-10"></p>
        <button onclick="location.reload()" class="mt-10 px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition-all">
            Done
        </button>
    </div>

    <!-- Footer -->
    <div class="mt-auto py-8 text-gray-400 text-xs font-medium">
        &copy; <?php echo date('Y'); ?> Dosti Welfare Donation Management
    </div>

    <script>
        const isPartner = <?php echo $is_partner ? 'true' : 'false'; ?>;
        const btnCheck = document.getElementById('btn-check');
        const btnRedeem = document.getElementById('btn-redeem');
        const voucherInput = document.getElementById('voucher_id');
        const skeletonLoader = document.getElementById('skeleton-loader');
        const resultCard = document.getElementById('result-card');
        const statusContainer = document.getElementById('status-container');
        const statusBadge = document.getElementById('status-badge');
        const donorName = document.getElementById('donor-name');
        const redemptionForm = document.getElementById('redemption-form');
        const errorContainer = document.getElementById('error-container');
        const successOverlay = document.getElementById('success-overlay');
        const successMsg = document.getElementById('success-msg');

        btnCheck.addEventListener('click', async () => {
            const voucherId = voucherInput.value.trim();
            if (!voucherId) return;

            // Reset and show loader
            resultCard.classList.add('hidden');
            errorContainer.classList.add('hidden');
            skeletonLoader.classList.remove('hidden');
            btnCheck.disabled = true;
            btnCheck.classList.add('opacity-50');

            try {
                // Use exactly what user entered (API handles fallback)
                const response = await fetch(`api/verify_voucher.php?voucher_id=${voucherId}`);
                const result = await response.json();

                setTimeout(() => { // Small delay for feel
                    skeletonLoader.classList.add('hidden');
                    btnCheck.disabled = false;
                    btnCheck.classList.remove('opacity-50');

                    if (result.success) {
                        resultCard.classList.remove('hidden');
                        donorName.innerText = result.data.name;
                        
                        if (isPartner) {
                            // PARTNER VIEW: No dropdown, check assignments for this restaurant
                            if (result.data.assignments && result.data.assignments.length > 0) {
                                const hasAvailable = result.data.assignments.some(a => a.status !== 'Redeemed');
                                if (hasAvailable) {
                                    statusContainer.className = 'p-5 rounded-2xl border-2 mb-4 border-green-100 bg-green-50/50';
                                    statusBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-green-500 text-white';
                                    statusBadge.innerText = 'Available for Redemption';
                                    redemptionForm.classList.remove('hidden');
                                } else {
                                    statusContainer.className = 'p-5 rounded-2xl border-2 mb-4 border-red-100 bg-red-50/50';
                                    statusBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-red-500 text-white';
                                    statusBadge.innerText = 'Already Redeemed';
                                    const redeemedAt = result.data.assignments.find(a => a.status === 'Redeemed')?.redeemed_at;
                                    const timeStr = redeemedAt ? new Date(redeemedAt).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false }).replace(',', '') : '';
                                    errorContainer.innerText = `This voucher has already been redeemed${timeStr ? ' on ' + timeStr : ''}.`;
                                    errorContainer.classList.remove('hidden');
                                }
                            } else {
                                errorContainer.innerText = "This voucher is not assigned to your restaurant.";
                                errorContainer.classList.remove('hidden');
                                statusContainer.classList.add('hidden');
                            }
                        } else {
                            // ADMIN VIEW: Show dropdown and assignment list
                            const assignList = document.getElementById('assignment-list');
                            const restSelect = document.getElementById('restaurant');
                            assignList.innerHTML = '';
                            restSelect.innerHTML = '<option value="" disabled selected>Choose assigned location...</option>';
                            
                            if (result.data.assignments && result.data.assignments.length > 0) {
                                result.data.assignments.forEach(assign => {
                                    const isRedeemed = assign.status === 'Redeemed';
                                    const timeStr = assign.redeemed_at ? new Date(assign.redeemed_at).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false }).replace(',', '') : '';
                                    assignList.innerHTML += `
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-700">${assign.restaurant_name}</span>
                                                ${isRedeemed ? `<span class="text-[10px] text-gray-400 font-medium">Used: ${timeStr}</span>` : ''}
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase ${isRedeemed ? 'bg-red-500 text-white' : 'bg-green-500 text-white'}">
                                                ${isRedeemed ? 'Redeemed' : 'Available'}
                                            </span>
                                        </div>
                                    `;
                                    if (!isRedeemed) {
                                        restSelect.innerHTML += `<option value="${assign.restaurant_name}">${assign.restaurant_name}</option>`;
                                    }
                                });

                                statusContainer.className = 'p-5 rounded-2xl border-2 mb-4 border-blue-100 bg-blue-50/50';
                                statusBadge.className = 'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-blue-500 text-white';
                                statusBadge.innerText = 'Multi-Partner Voucher';
                                redemptionForm.classList.remove('hidden');
                            } else {
                                errorContainer.innerText = "No restaurants assigned to this voucher.";
                                errorContainer.classList.remove('hidden');
                                statusContainer.classList.add('hidden');
                            }
                        }
                    } else {
                        resultCard.classList.remove('hidden');
                        errorContainer.innerText = result.message;
                        errorContainer.classList.remove('hidden');
                        statusContainer.classList.add('hidden');
                        redemptionForm.classList.add('hidden');
                    }
                }, 800);

            } catch (error) {
                console.error(error);
                skeletonLoader.classList.add('hidden');
                btnCheck.disabled = false;
                errorContainer.innerText = "Connection Error. Please try again.";
                errorContainer.classList.remove('hidden');
                resultCard.classList.remove('hidden');
            }
        });

        btnRedeem.addEventListener('click', async () => {
            const voucherId = voucherInput.value.trim();
            let restaurant = null;

            if (!isPartner) {
                restaurant = document.getElementById('restaurant').value;
                if (!restaurant) {
                    alert('Please select a restaurant');
                    return;
                }
            }

            btnRedeem.disabled = true;
            btnRedeem.classList.add('opacity-50');
            btnRedeem.innerText = 'Processing...';

            try {
                const formData = new FormData();
                formData.append('voucher_id', voucherId);
                if (!isPartner) {
                    formData.append('restaurant_id', restaurant);
                }

                const response = await fetch('api/redeem_voucher.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    successMsg.innerText = result.message || 'Voucher Successfully Redeemed!';
                    successOverlay.style.display = 'flex';
                } else {
                    alert(result.message);
                    btnRedeem.disabled = false;
                    btnRedeem.classList.remove('opacity-50');
                    btnRedeem.innerText = 'Confirm Redemption';
                }
            } catch (error) {
                console.error(error);
                alert("Connection Error. Please try again.");
                btnRedeem.disabled = false;
                btnRedeem.classList.remove('opacity-50');
                btnRedeem.innerText = 'Confirm Redemption';
            }
        });
    </script>
</body>
</html>
