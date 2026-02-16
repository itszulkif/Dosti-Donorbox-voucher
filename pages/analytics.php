<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php';

// Fetch counts for summary cards
$totalDonorsResult = $conn->query("SELECT COUNT(*) as count FROM donors");
$totalDonors = $totalDonorsResult->fetch_assoc()['count'];

// Calculate dynamic impact based on individual voucher values
$totalDonationsResult = $conn->query("SELECT SUM(voucher_value) as total FROM donors");
$totalDonations = $totalDonationsResult->fetch_assoc()['total'] ?? 0;

// Voucher Usage Data
$redeemedResult = $conn->query("SELECT COUNT(*) as count FROM donors WHERE status = 'Redeemed'");
$redeemedCount = $redeemedResult->fetch_assoc()['count'];
$availableCount = $totalDonors - $redeemedCount;

// Voucher Usage Data
$redeemedResult = $conn->query("SELECT COUNT(*) as count FROM donors WHERE status = 'Redeemed'");
$redeemedCount = $redeemedResult->fetch_assoc()['count'];
$availableCount = $totalDonors - $redeemedCount;
// Legacy restaurant data block removed.
?>

<!-- Analytics Overview Section -->
<div class="space-y-8 animate-fade-in pb-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1 -->
        <div class="glass-panel p-6 flex items-center justify-between hover:scale-[1.02] transition-transform duration-300">
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Donors</p>
                <h3 class="text-4xl font-bold text-gray-800 tracking-tight"><?php echo number_format($totalDonors); ?></h3>
                <p class="text-xs text-green-600 font-medium mt-2 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    +12% from last month
                </p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="glass-panel p-6 flex items-center justify-between hover:scale-[1.02] transition-transform duration-300">
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Impact (PKR)</p>
                <h3 class="text-4xl font-bold text-gray-800 tracking-tight">Rs. <?php echo number_format($totalDonations); ?></h3>
                <p class="text-xs text-gr-600 font-medium mt-2 text-gray-400">
                    Lifetime donations count
                </p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg shadow-yellow-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Donor Registration Trends Chart -->
    <div class="glass-panel p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <h4 class="text-lg font-bold text-gray-800 flex items-center">
                <span class="w-1.5 h-6 bg-blue-600 rounded-full mr-3"></span>
                Donor Registration Trends
            </h4>
            <div class="flex bg-gray-100/80 p-1 rounded-xl w-full md:w-auto overflow-x-auto">
                <button onclick="updateTrendChart('daily')" id="btn-daily" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:text-gray-800 transition-all shadow-sm bg-white">Daily</button>
                <button onclick="updateTrendChart('weekly')" id="btn-weekly" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-semibold text-gray-500 hover:text-gray-800 transition-all hover:bg-white/60">Weekly</button>
                <button onclick="updateTrendChart('monthly')" id="btn-monthly" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-semibold text-gray-500 hover:text-gray-800 transition-all hover:bg-white/60">Monthly</button>
                <button onclick="updateTrendChart('yearly')" id="btn-yearly" class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-semibold text-gray-500 hover:text-gray-800 transition-all hover:bg-white/60">Yearly</button>
            </div>
        </div>
        <div class="relative h-96 w-full">
            <canvas id="registrationTrendChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Voucher Redemption Trends (Moved here to replace Utilization) -->
        <div class="glass-panel p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <h4 class="text-lg font-bold text-gray-800 flex items-center">
                    <span class="w-1.5 h-6 bg-green-500 rounded-full mr-3"></span>
                    Voucher Usage
                </h4>
                <div class="flex bg-gray-100/60 p-1 rounded-lg">
                    <button onclick="updateRedemptionChart('daily')" id="vbtn-daily" class="px-3 py-1 rounded-md text-xs font-semibold text-gray-600 shadow-sm bg-white">D</button>
                    <button onclick="updateRedemptionChart('weekly')" id="vbtn-weekly" class="px-3 py-1 rounded-md text-xs font-semibold text-gray-500 hover:bg-white/40">W</button>
                    <button onclick="updateRedemptionChart('monthly')" id="vbtn-monthly" class="px-3 py-1 rounded-md text-xs font-semibold text-gray-500 hover:bg-white/40">M</button>
                </div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="redemptionTrendChart"></canvas>
            </div>
        </div>

        <!-- Partner Performance Bar Chart -->
        <div class="glass-panel p-8">
            <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-1.5 h-6 bg-yellow-500 rounded-full mr-3"></span>
                Partner Performance
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-6" id="partner-stats-container">
                    <!-- Dynamic Content Loaded via JS -->
                    <div class="animate-pulse space-y-4">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-2 bg-gray-200 rounded"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2 mt-4"></div>
                        <div class="h-2 bg-gray-200 rounded"></div>
                    </div>
                </div>
                
                <div class="h-64 pt-4">
                     <canvas id="restaurantBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Common Chart Options
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b7280';

    // Partner Performance Loader
    let partnerChart;
    const ctxBar = document.getElementById('restaurantBarChart');

    function loadPartnerPerformance() {
        fetch('api/get_partner_performance.php')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('partner-stats-container');
                container.innerHTML = ''; // Clear loader
                
                const labels = [];
                const values = [];
                const colors = [];

                data.forEach(p => {
                    labels.push(p.name);
                    values.push(p.count);
                    colors.push(p.color);

                    // Render Progress Bar Widget
                    const html = `
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">${p.name}</span>
                            <span class="text-sm font-bold text-gray-600">${p.count} Redemptions</span>
                        </div>
                        <div class="w-full bg-gray-200/50 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-1000 ease-out" 
                                 style="width: ${p.percent}%; background-color: ${p.color}"></div>
                        </div>
                    </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                });

                if (data.length === 0) {
                     container.innerHTML = '<p class="text-gray-400 text-sm font-medium py-4">No partners or redemptions found yet.</p>';
                }

                // Update Chart
                if (partnerChart) partnerChart.destroy();
                
                if (ctxBar) {
                    partnerChart = new Chart(ctxBar.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Redeemed',
                                data: values,
                                backgroundColor: colors,
                                borderRadius: 6,
                                barThickness: 24
                            }]
                        },
                        options: {
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                    titleColor: '#1f2937',
                                    bodyColor: '#1f2937',
                                    borderColor: '#e5e7eb',
                                    borderWidth: 1,
                                    padding: 10
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { display: false },
                                    border: { display: false },
                                    ticks: { display: false }
                                },
                                x: {
                                    grid: { display: false },
                                    border: { display: false }
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            })
            .catch(err => console.error('Failed to load partner stats', err));
    }

    // Load immediately
    loadPartnerPerformance();

    // Donor Registration Trend Chart
    let regTrendChart;
    const ctxRegTrend = document.getElementById('registrationTrendChart').getContext('2d');

    function initRegTrendChart(labels, data) {
        if (regTrendChart) {
            regTrendChart.destroy();
        }

        let gradient = ctxRegTrend.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        regTrendChart = new Chart(ctxRegTrend, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'New Donors',
                    data: data,
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6', borderDash: [5, 5] },
                        ticks: { callback: function(value) { if (value % 1 === 0) { return value; } } }
                    },
                    x: { grid: { display: false } }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Voucher Redemption Trend Chart
    let redTrendChart;
    const ctxRedTrend = document.getElementById('redemptionTrendChart').getContext('2d');

    function initRedTrendChart(labels, data) {
        if (redTrendChart) {
            redTrendChart.destroy();
        }

        let gradient = ctxRedTrend.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        redTrendChart = new Chart(ctxRedTrend, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Vouchers Redeemed',
                    data: data,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { display: false }
                    },
                    x: { grid: { display: false } }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    function updateTrendChart(period) {
        document.querySelectorAll('button[id^="btn-"]').forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-sm', 'text-gray-800');
            btn.classList.add('text-gray-500', 'hover:bg-white/60');
        });
        const activeBtn = document.getElementById(`btn-${period}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-500', 'hover:bg-white/60');
            activeBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-800');
        }

        fetch(`api/get_donor_analytics.php?period=${period}`)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.label);
                const counts = data.map(item => item.count);
                initRegTrendChart(labels, counts);
            })
            .catch(error => console.error('Error fetching trend data:', error));
    }

    function updateRedemptionChart(period) {
        document.querySelectorAll('button[id^="vbtn-"]').forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-sm', 'text-gray-800');
            btn.classList.add('text-gray-500', 'hover:bg-white/40');
        });
        const activeBtn = document.getElementById(`vbtn-${period}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-500', 'hover:bg-white/40');
            activeBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-800');
        }

        fetch(`api/get_voucher_analytics.php?period=${period}`)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.label);
                const counts = data.map(item => item.count);
                initRedTrendChart(labels, counts);
            })
            .catch(error => console.error('Error fetching redemption data:', error));
    }

    // Initialize charts
    updateTrendChart('daily');
    updateRedemptionChart('daily');
</script>
