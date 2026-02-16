/**
 * Dosti Application Logic
 * Refactored for robust AJAX loading and global event handling
 */

// Global state for sidebar and UI
const UI = {
    mainContent: null,
    pageTitle: null,
    sidebarLinks: null,
    loader: null,
    titles: {
        'analytics': 'Analytics Overview',
        'add_donor': 'Management Hub',
        'donors_list': 'Donors List',
        'restaurants_list': 'Partner Restaurants',
        'donation_boxes': 'Donation Box Locations',
        'log_visit': 'Log Box Collection',
        'visit_history': 'Collection History',
        'box_analytics': 'Donation Box Analytics',
        'shop_list': 'All Donation Shops'
    }
};

// Initialize everything on load
document.addEventListener('DOMContentLoaded', () => {
    // Get page from URL query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 'analytics';

    // Update UI State
    UI.currentPage = currentPage;

    // Initialize Sidebar Toggle
    initSidebar();

    // Initialize Page-Specific Logic
    initCurrentPage(currentPage);
});

/**
 * Sidebar and Responsive Toggles
 */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');

    window.toggleSidebar = (open) => {
        if (open) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    };

    if (openBtn) openBtn.onclick = () => toggleSidebar(true);
    if (closeBtn) closeBtn.onclick = () => toggleSidebar(false);
    if (overlay) overlay.onclick = () => toggleSidebar(false);
}

function initCurrentPage(page) {
    // Shared table initialization
    if (page === 'donors_list') {
        initTable('donor-table', 'donor-search', 'rows-per-page', 'pagination-controls');
    } else if (page === 'visit_history') {
        initTable('visit-table', 'visit-search', 'rows-per-page-visit', 'pagination-controls-visit');
    } else if (page === 'restaurants_list') {
        initTable('restaurant-table', 'restaurant-search', 'rows-per-page-rest', 'pagination-controls-rest');
    } else if (page === 'add_donor') {
        initAddDonorHub();
    }
}

/**
 * Management Hub (Add Donor/Restaurant page) Logic
 */
function initAddDonorHub() {
    const donorForm = document.getElementById('hub-donor-form');
    const restForm = document.getElementById('hub-restaurant-form');
    const toggleDonor = document.getElementById('toggle-donor-view');
    const toggleRest = document.getElementById('toggle-restaurant-view');

    // Hub Internal Navigation
    if (toggleDonor) toggleDonor.onclick = () => window.location.href = 'index.php?page=donors_list';
    if (toggleRest) toggleRest.onclick = () => window.location.href = 'index.php?page=restaurants_list';

    if (donorForm) {
        donorForm.onsubmit = async (e) => {
            e.preventDefault();
            const btn = donorForm.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            const emailInput = donorForm.querySelector('input[name="email"]');
            const hasEmail = emailInput && emailInput.value.trim().length > 0;

            btn.disabled = true;
            btn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div> Saving...';

            try {
                const res = await fetch('api/save_donor.php', { method: 'POST', body: new FormData(donorForm) });
                const data = await res.json();

                if (data.success) {
                    if (hasEmail) {
                        btn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div> Sending Confirmation...';

                        try {
                            const emailFormData = new FormData();
                            emailFormData.append('donor_id', data.donor_id);

                            const emailRes = await fetch('api/send_voucher_email.php', { method: 'POST', body: emailFormData });
                            const emailData = await emailRes.json();

                            if (emailData.success) {
                                showToast('✅ Saved & Email Sent!', 'success');
                            } else {
                                showToast('⚠️ Saved, but Email Failed: ' + emailData.message, 'error');
                            }
                        } catch (emailErr) {
                            console.error(emailErr);
                            showToast('⚠️ Saved, but Email Failed to Connect', 'error');
                        }
                    } else {
                        showToast('✅ Success! Donor record has been created.', 'success');
                    }

                    donorForm.reset();
                    // Refresh the page after a short delay to show updated data
                    setTimeout(() => window.location.href = 'index.php?page=add_donor', 1500);
                } else {
                    showToast('❌ Error: ' + data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('❌ Failed to connect to server', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = originalText;
        };
    }

    if (restForm) {
        // Offer type toggle
        const radios = restForm.querySelectorAll('input[name="offer_mode"]');
        radios.forEach(r => r.onchange = (e) => {
            const isDiscount = e.target.value === 'discount';
            document.getElementById('input-discount').classList.toggle('hidden', !isDiscount);
            document.getElementById('input-price').classList.toggle('hidden', isDiscount);

            // Clear opposite field
            if (isDiscount) restForm.querySelector('input[name="custom_price"]').value = '0';
            else restForm.querySelector('input[name="discount_percentage"]').value = '0';
        });

        restForm.onsubmit = async (e) => {
            e.preventDefault();
            const btn = restForm.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div> Saving...';

            try {
                const res = await fetch('api/save_restaurant.php', { method: 'POST', body: new FormData(restForm) });
                const data = await res.json();
                if (data.success) {
                    showToast('✅ Success! Partner restaurant registered.', 'success');
                    restForm.reset();
                    // Refresh the page to update the restaurant dropdown
                    setTimeout(() => loadPage('add_donor'), 1500);
                } else {
                    showToast('❌ Error: ' + data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('❌ Failed to connect to server', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = originalText;
        };
    }
}

/**
 * GLOBAL ACTION HANDLERS
 * Attaching to window ensures they work with inline onclick="..."
 */

window.showModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        const content = modal.querySelector('div');
        if (content) content.classList.add('scale-100', 'opacity-100'), content.classList.remove('scale-95', 'opacity-0');
    }, 10);
};

window.closeModal = (id) => {
    const modal = document.getElementById(id);
    if (!modal) return;
    const content = modal.querySelector('div');
    if (content) content.classList.remove('scale-100', 'opacity-100'), content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
};

// Global delegated listener for export dropdowns (supports multiple dropdowns per page)
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.export-dropdown-btn');

    if (btn) {
        e.stopPropagation();

        // Toggle only the menu that belongs to this button/container
        const container = btn.closest('.relative, .inline-block, div');
        const menu = container ? container.querySelector('.export-dropdown-menu') : null;

        if (menu) {
            const isHidden = menu.classList.contains('hidden');

            // Hide all other export menus first
            document.querySelectorAll('.export-dropdown-menu').forEach(m => m.classList.add('hidden'));

            // Toggle this one
            if (isHidden) {
                menu.classList.remove('hidden');
            }
        }
    } else {
        // Clicked outside any export button: close all menus
        document.querySelectorAll('.export-dropdown-menu').forEach(m => m.classList.add('hidden'));
    }
});

// Partner Management
window.addPartner = () => openPartnerModal();
window.editRestaurant = (id) => openPartnerModal(id);
window.deleteRestaurant = (id) => {
    // Capture the row so we can remove it without a full reload
    const row = document.querySelector(`tr[data-restaurant-id="${id}"]`);

    confirmAction(`api/delete_restaurant.php?id=${id}`, () => {
        if (row && row.parentElement) {
            row.parentElement.removeChild(row);
        }

        // Re-initialize table state so search, pagination, and exports
        // immediately reflect the updated list without page reload.
        initTable('restaurant-table', 'restaurant-search', 'rows-per-page-rest', 'pagination-controls-rest');
    });
};

window.editDonor = (id) => openEditModal(`api/get_donor_details.php?id=${id}`, 'api/update_donor.php', 'index.php?page=donors_list');
// Note: deleteDonor logic is now inline above or needs update if using global delete
window.deleteDonor = (id) => confirmAction(`api/delete_donor.php?id=${id}`, () => window.location.href = 'index.php?page=donors_list');

// Visit Management
window.editVisit = (id) => openEditModal(`api/get_visit_details.php?id=${id}`, 'api/update_visit.php', 'index.php?page=visit_history');
window.deleteVisit = (id) => confirmAction(`api/delete_visit.php?id=${id}`, () => window.location.href = 'index.php?page=visit_history');

// Shop Management (Modal)
window.editShopFromList = (id) => openEditModal(`api/get_shop_form.php?id=${id}`, 'api/update_shop.php', window.location.href);
window.deleteShop = (id) => confirmAction(`api/delete_shop.php?id=${id}`, () => window.location.reload());

/**
 * Helper: General Delete Confirmation
 */
function confirmAction(url, callback) {
    showModal('delete-modal');
    const confirmBtn = document.getElementById('confirm-delete');
    const cancelBtn = document.getElementById('cancel-delete');

    cancelBtn.onclick = () => closeModal('delete-modal');
    confirmBtn.onclick = async () => {
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Processing...';
        try {
            const res = await fetch(url);
            const data = await res.json();
            if (data.success) {
                closeModal('delete-modal');
                // If callback is a function, call it. If it was a 'page' string, navigate.
                if (typeof callback === 'function') callback();
                else window.location.href = callback;
            } else alert(data.message);
        } catch (err) { console.error(err); }
        confirmBtn.disabled = false;
        confirmBtn.innerText = 'Delete Forever';
    };
}

/**
 * Helper: General Edit Modal Opener
 */
async function openEditModal(url, updateUrl, refreshPage) {
    const content = document.getElementById('modal-content');
    content.innerHTML = '<div class="p-20 text-center"><div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mx-auto"></div></div>';
    showModal('modal-container');

    try {
        const res = await fetch(url);
        content.innerHTML = await res.text();

        const form = content.querySelector('form');
        if (form) {
            form.onsubmit = async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerText = 'Saving...';
                try {
                    const saveRes = await fetch(updateUrl, { method: 'POST', body: new FormData(form) });
                    const result = await saveRes.json();
                    if (result.success) {
                        closeModal('modal-container');
                        if (refreshPage.includes('index.php') || refreshPage.includes('http')) {
                            window.location.href = refreshPage;
                        } else {
                            window.location.reload();
                        }
                    } else alert(result.message);
                } catch (err) { console.error(err); }
                btn.disabled = false;
            };
        }
    } catch (err) { console.error(err); }
}

/**
 * Specific: Partner Modal (Complex Form with own script)
 */
async function openPartnerModal(id = null) {
    const content = document.getElementById('modal-content');
    content.innerHTML = '<div class="p-20 text-center"><div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mx-auto"></div></div>';
    showModal('modal-container');

    try {
        const url = id ? `api/get_restaurant_form.php?id=${id}` : `api/get_restaurant_form.php`;
        const res = await fetch(url);
        content.innerHTML = await res.text();
        // Trigger the internal script for offer mode toggling
        const scripts = content.querySelectorAll('script');
        scripts.forEach(s => eval(s.textContent));

        const form = document.getElementById('partner-form');
        if (form) {
            form.onsubmit = async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const isEdit = form.querySelector('input[name="id"]');
                btn.disabled = true;

                try {
                    const api = isEdit ? 'api/update_restaurant.php' : 'api/save_restaurant.php';
                    const saveRes = await fetch(api, { method: 'POST', body: new FormData(form) });
                    const result = await saveRes.json();
                    if (result.success) {
                        closeModal('modal-container');
                        window.location.href = 'index.php?page=restaurants_list';
                    } else alert(result.message);
                } catch (err) { console.error(err); }
                btn.disabled = false;
            };
        }
    } catch (err) { console.error(err); }
}

/**
 * Toast Notification System
 */
window.showToast = (message, type = 'success') => {
    const existingToast = document.getElementById('toast-notification');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.id = 'toast-notification';
    toast.className = `fixed top-6 right-6 px-6 py-4 rounded-2xl shadow-2xl z-50 transform transition-all duration-500 flex items-center gap-3 font-bold ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
    toast.innerHTML = `
        <span class="text-lg">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-white/80 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;

    document.body.appendChild(toast);
    setTimeout(() => toast.style.opacity = '1', 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 500);
    }, 5000);
};

/**
 * Detailed Redemption History Modal
 */
window.viewDonorDetails = async (id) => {
    const content = document.getElementById('modal-content');
    content.innerHTML = '<div class="p-20 text-center"><div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mx-auto"></div></div>';
    showModal('modal-container');

    try {
        const res = await fetch(`api/get_redemption_history.php?donor_id=${id}`);
        const data = await res.json();

        if (data.success) {
            let offersHtml = '';
            data.offers.forEach(offer => {
                const isRedeemed = offer.status === 'Redeemed';
                const timeStr = offer.redeemed_at ? new Date(offer.redeemed_at).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false }).replace(',', '') : 'N/A';

                offersHtml += `
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:bg-white transition-all">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800">${offer.restaurant_name}</span>
                            <span class="text-[11px] text-gray-400 font-medium">Type: ${offer.offer_type === 'percentage' ? offer.offer_value + '%' : 'Fixed $' + offer.offer_value}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tight ${isRedeemed ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500'}">
                                ${offer.status}
                            </span>
                            ${isRedeemed ? `<span class="text-[10px] text-blue-600 font-bold mt-1">${timeStr}</span>` : ''}
                        </div>
                    </div>
                `;
            });

            content.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Donor Details</p>
                            <h3 class="text-2xl font-bold text-gray-800">${data.donor.name}</h3>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-lg border border-blue-100">Voucher: ${data.donor.voucher_id}</span>
                            </div>
                        </div>
                        <button onclick="closeModal('modal-container')" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">Redemption Assignments</p>
                        <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                            ${offersHtml || '<div class="py-10 text-center font-bold text-gray-400">No assignments found for this donor.</div>'}
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <button onclick="closeModal('modal-container')" class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-2xl transition-all">
                            Close Details
                        </button>
                    </div>
                </div>
            `;
        } else {
            alert(data.message);
            closeModal('modal-container');
        }
    } catch (err) {
        console.error(err);
        alert('Failed to load donor details');
        closeModal('modal-container');
    }
};
