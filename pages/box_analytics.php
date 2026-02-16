<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['super_admin']);
include __DIR__ . '/../config.php';
?>
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-black text-gray-800 tracking-tight">Box Analytics</h1>
        <p class="text-gray-500 font-medium">Performance and trends for donation collections</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-8 rounded-[2rem] text-white shadow-xl shadow-blue-600/20">
            <p class="text-blue-100 text-xs font-bold uppercase tracking-[0.2em] mb-3">Total Collection</p>
            <div class="flex items-baseline gap-2">
                <span class="text-blue-200 text-xl font-bold">Rs.</span>
                <h2 id="total-pk-collection" class="text-4xl font-black tracking-tighter">0</h2>
            </div>
            <div class="mt-4 flex items-center gap-2 text-blue-200 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                <span>Growth active</span>
            </div>
        </div>
        
        <div class="bg-white p-7 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">Avg per Visit</p>
            <h3 id="avg-per-visit" class="text-2xl font-black text-gray-800 mt-2">Rs. 0</h3>
            <div class="h-1.5 w-full bg-gray-50 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-yellow-400 w-2/3"></div>
            </div>
        </div>

        <div class="bg-white p-7 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">Active Boxes</p>
            <h3 id="active-boxes-count" class="text-2xl font-black text-gray-800 mt-2">0 Locations</h3>
            <p class="text-xs text-green-500 font-bold mt-2 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-green-500"></span> 100% Active
            </p>
        </div>

        <div class="bg-white p-7 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">Top Shop Share</p>
            <h3 id="top-shop-share" class="text-2xl font-black text-gray-800 mt-2">0%</h3>
            <p class="text-xs text-gray-400 mt-2">of total collection</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Trend -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-gray-800">Collection Trends</h3>
                <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
                    <button onclick="updateTrend('daily')" class="filter-btn px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all hover:bg-white hover:shadow-sm text-gray-500" data-filter="daily">Daily</button>
                    <button onclick="updateTrend('weekly')" class="filter-btn px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all hover:bg-white hover:shadow-sm text-gray-500" data-filter="weekly">Weekly</button>
                    <button onclick="updateTrend('monthly')" class="filter-btn active px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all bg-white shadow-sm text-blue-600" data-filter="monthly">Monthly</button>
                </div>
            </div>
            <div class="h-72">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Top Shops -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-gray-800">Top Performing Shops</h3>
                <span class="px-3 py-1 bg-yellow-50 text-yellow-600 text-[10px] font-bold rounded-full uppercase">Ranking</span>
            </div>
            <div class="h-72">
                <canvas id="topsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Frequency Tracker -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Visit Frequency Tracker</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Time since last collection</p>
            </div>
            
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Search -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="freq-search" placeholder="Search shop or box..." 
                        class="pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-medium text-gray-700 w-full md:w-64">
                </div>

                <!-- Time Filter -->
                <select id="freq-time-filter" class="pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-gray-600 appearance-none cursor-pointer">
                    <option value="all">All Time</option>
                    <option value="today">Today Only</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                        <th class="px-8 py-5">Shop & Box</th>
                        <th class="px-8 py-5 text-center">Last Visit</th>
                        <th class="px-8 py-5 text-center">Days Elapsed</th>
                        <th class="px-8 py-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody id="frequencyTable" class="divide-y divide-gray-50 font-medium">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rows</span>
                <select id="freq-limit" class="bg-white border border-gray-200 text-xs rounded-lg px-2 py-1 focus:outline-none font-bold text-gray-700">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
            
            <div id="freq-pagination-info" class="text-xs font-bold text-gray-400">
                Showing <span id="freq-range" class="text-gray-700">0-0</span> of <span id="freq-total" class="text-gray-700">0</span> shops
            </div>

            <div class="flex items-center gap-1" id="freq-pagination-controls">
                <!-- Dynamic Pagination -->
            </div>
        </div>
    </div>
</div>

<script>
let currentFreqPage = 1;
let currentFreqLimit = 10;
let currentFreqSearch = '';
let currentFreqTF = 'all';

async function initAnalytics() {
    try {
        const res = await fetch(`api/get_box_analytics.php?q=${currentFreqSearch}&page=${currentFreqPage}&limit=${currentFreqLimit}&tf=${currentFreqTF}`);
        const data = await res.json();
        
        if(data.success) {
            // Update Stats (only on first load or if needed)
            document.getElementById('total-pk-collection').innerText = parseFloat(data.total_collection).toLocaleString();
            document.getElementById('active-boxes-count').innerText = data.pagination.total + ' Locations';
            
            if(data.total_collection > 0) {
                // Approximate avg based on total collection and total shops
                const avg = parseFloat(data.total_collection) / (data.pagination.total || 1);
                document.getElementById('avg-per-visit').innerText = 'Rs. ' + Math.round(avg).toLocaleString();
                
                if(data.top_shops.length > 0) {
                    const topShare = (parseFloat(data.top_shops[0].total_amount) / parseFloat(data.total_collection)) * 100;
                    document.getElementById('top-shop-share').innerText = Math.round(topShare) + '%';
                }
            }

            // Render Trends and Tops (only on first load or filter change)
            renderTrendChart(data.monthly_trends);
            renderTopsChart(data.top_shops);

            // Render Frequency Table
            renderFrequencyTable(data);
        }
    } catch (err) {
        console.error(err);
    }
}

async function loadFrequencyData() {
    try {
        const res = await fetch(`api/get_box_analytics.php?q=${currentFreqSearch}&page=${currentFreqPage}&limit=${currentFreqLimit}&tf=${currentFreqTF}`);
        const data = await res.json();
        if(data.success) {
            renderFrequencyTable(data);
        }
    } catch (err) {
        console.error(err);
    }
}

function renderFrequencyTable(data) {
    const table = document.getElementById('frequencyTable');
    if (data.visit_frequency.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="4" class="px-8 py-20 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-200 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800">No visits recorded</h4>
                        <p class="text-gray-400 text-sm">No shops match your current filters or timeframe.</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('freq-range').innerText = '0-0';
        document.getElementById('freq-total').innerText = '0';
        document.getElementById('freq-pagination-controls').innerHTML = '';
        return;
    }

    table.innerHTML = data.visit_frequency.map(f => {
        let statusClass = 'bg-green-100 text-green-600';
        let statusText = 'Recent';
        
        // Accurate Status Calculation
        if(f.days_ago === null || f.days_ago > 30) { 
            statusClass = 'bg-yellow-100 text-yellow-600'; 
            statusText = 'Due Soon'; 
        }
        if(f.days_ago > 60 || f.last_visit === null) { 
            statusClass = 'bg-red-100 text-red-600'; 
            statusText = 'Overdue'; 
        }
        
        // Grammar Fix: 1 Day vs X Days
        const daysText = f.days_ago !== null 
            ? (f.days_ago === 1 ? '1 Day' : f.days_ago + ' Days') 
            : '-';
        
        return `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-8 py-5">
                <div class="font-bold text-gray-800">${f.shop_name}</div>
                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Box: ${f.box_number || '-'}</div>
            </td>
            <td class="px-8 py-5 text-center text-gray-500">${f.last_visit || '<span class="text-gray-300 italic">No visits recorded</span>'}</td>
            <td class="px-8 py-5 text-center font-black text-gray-400">${daysText}</td>
            <td class="px-8 py-5 text-right">
                <span class="px-3 py-1 ${statusClass} text-[10px] font-black uppercase rounded-full tracking-widest">${statusText}</span>
            </td>
        </tr>
        `;
    }).join('');

    // Update Pagination Info
    const start = (data.pagination.page - 1) * data.pagination.limit + 1;
    const end = Math.min(start + data.pagination.limit - 1, data.pagination.total);
    document.getElementById('freq-range').innerText = `${start}-${end}`;
    document.getElementById('freq-total').innerText = data.pagination.total;

    // Render Pagination Controls
    renderPaginationControls(data.pagination);
}

function renderPaginationControls(pagination) {
    const container = document.getElementById('freq-pagination-controls');
    let html = '';
    
    // Previous
    html += `<button onclick="changeFreqPage(${pagination.page - 1})" ${pagination.page === 1 ? 'disabled' : ''} class="p-2 rounded-xl hover:bg-white text-gray-400 disabled:opacity-30 transition-all border border-transparent hover:border-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    </button>`;

    // Page Numbers (Simplified logic)
    for(let i = 1; i <= pagination.total_pages; i++) {
        if (i === 1 || i === pagination.total_pages || (i >= pagination.page - 1 && i <= pagination.page + 1)) {
            html += `<button onclick="changeFreqPage(${i})" class="w-8 h-8 rounded-xl text-xs font-bold transition-all ${i === pagination.page ? 'bg-blue-900 text-white shadow-lg shadow-blue-900/20' : 'text-gray-500 hover:bg-white hover:border-gray-200 border border-transparent'}">${i}</button>`;
        } else if (i === pagination.page - 2 || i === pagination.page + 2) {
            html += `<span class="px-1 text-gray-400 text-xs">...</span>`;
        }
    }

    // Next
    html += `<button onclick="changeFreqPage(${pagination.page + 1})" ${pagination.page === pagination.total_pages ? 'disabled' : ''} class="p-2 rounded-xl hover:bg-white text-gray-400 disabled:opacity-30 transition-all border border-transparent hover:border-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </button>`;

    container.innerHTML = html;
}

window.changeFreqPage = (page) => {
    currentFreqPage = page;
    loadFrequencyData();
};

function renderTopsChart(tops) {
    const topsCtx = document.getElementById('topsChart').getContext('2d');
    if (window.topsChartInstance) window.topsChartInstance.destroy();
    
    window.topsChartInstance = new Chart(topsCtx, {
        type: 'bar',
        data: {
            labels: tops.map(s => s.shop_name),
            datasets: [{
                label: 'Total Collected',
                data: tops.map(s => s.total_amount),
                backgroundColor: '#ffcb05',
                borderRadius: 12,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Helper to Render/Update Trend Chart
let trendChartInstance = null;
function renderTrendChart(trends) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    if (trendChartInstance) {
        trendChartInstance.destroy();
    }

    trendChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trends.map(t => t.label),
            datasets: [{
                label: 'PKR Collected',
                data: trends.map(t => t.total),
                borderColor: '#09539c',
                backgroundColor: (context) => {
                    const chartCtx = context.chart.ctx;
                    const gradient = chartCtx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(9, 83, 156, 0.2)');
                    gradient.addColorStop(1, 'rgba(9, 83, 156, 0)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#09539c',
                pointBorderWidth: 2,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rs. ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f3f4f6' },
                    border: { display: false },
                    ticks: { callback: (val) => 'Rs. ' + val/1000 + 'k' }
                },
                x: { 
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });
}

// Function to handle filter clicks
async function updateTrend(filter) {
    // Update active button state
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if(btn.dataset.filter === filter) {
            btn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
            btn.classList.remove('text-gray-500');
        } else {
            btn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
            btn.classList.add('text-gray-500');
        }
    });

    try {
        const res = await fetch(`api/get_box_analytics.php?filter=${filter}&q=${currentFreqSearch}&page=1&limit=${currentFreqLimit}&tf=${currentFreqTF}`);
        const data = await res.json();
        if(data.success) {
            renderTrendChart(data.monthly_trends);
        }
    } catch(err) {
        console.error(err);
    }
}

// Event Listeners for Frequency Tracker
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('freq-search');
    const timeFilter = document.getElementById('freq-time-filter');
    const limitSelect = document.getElementById('freq-limit');

    let debounceTimer;
    searchInput.oninput = (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentFreqSearch = e.target.value;
            currentFreqPage = 1;
            loadFrequencyData();
        }, 500);
    };

    timeFilter.onchange = (e) => {
        currentFreqTF = e.target.value;
        currentFreqPage = 1;
        loadFrequencyData();
    };

    limitSelect.onchange = (e) => {
        currentFreqLimit = e.target.value;
        currentFreqPage = 1;
        loadFrequencyData();
    };

    initAnalytics();
});
</script>
