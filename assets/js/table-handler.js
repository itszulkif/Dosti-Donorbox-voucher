/**
 * Dosti Data Table Handler
 * Handles real-time search, pagination, and multi-format exports
 */

let tableStates = {};

function initTable(tableId, searchId, rowsPerPageId, paginationId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    const allRows = Array.from(tbody.querySelectorAll('tr:not(.no-results)'));
    if (allRows.length === 0 && !tbody.querySelector('.no-results')) {
        // Still init state so searches work even on empty tables
    }

    tableStates[tableId] = {
        allRows: allRows,
        filteredRows: allRows,
        currentPage: 1,
        rowsPerPage: parseInt(document.getElementById(rowsPerPageId)?.value || 10),
        paginationId: paginationId
    };

    // Event Listeners
    const searchInput = document.getElementById(searchId);
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            handleSearch(tableId, e.target.value);
        });
    }

    document.getElementById(rowsPerPageId)?.addEventListener('change', (e) => {
        tableStates[tableId].rowsPerPage = parseInt(e.target.value);
        tableStates[tableId].currentPage = 1;
        renderTable(tableId);
    });

    // Initial Render
    renderTable(tableId);
}

function handleSearch(tableId, term) {
    const state = tableStates[tableId];
    term = term.toLowerCase();

    state.filteredRows = state.allRows.filter(row => {
        return row.textContent.toLowerCase().includes(term);
    });

    state.currentPage = 1;
    renderTable(tableId);
}

function renderTable(tableId) {
    const state = tableStates[tableId];
    const tbody = document.getElementById(tableId).querySelector('tbody');

    // Clear current view
    tbody.innerHTML = '';

    if (state.filteredRows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="px-8 py-20 text-center text-gray-400">No results match your search.</td></tr>`;
        updatePagination(tableId, 0);
        return;
    }

    const start = (state.currentPage - 1) * state.rowsPerPage;
    const end = start + state.rowsPerPage;
    const paginatedRows = state.filteredRows.slice(start, end);

    paginatedRows.forEach(row => tbody.appendChild(row));

    updatePagination(tableId, state.filteredRows.length);
}

function updatePagination(tableId, totalFiltered) {
    const state = tableStates[tableId];
    const container = document.getElementById(state.paginationId);
    if (!container) return;

    const totalPages = Math.ceil(totalFiltered / state.rowsPerPage);
    container.innerHTML = '';

    // Prev Button
    const prevBtn = document.createElement('button');
    prevBtn.className = `p-2 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-blue-600 transition-all ${state.currentPage === 1 ? 'opacity-50 pointer-events-none' : ''}`;
    prevBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>`;
    prevBtn.onclick = () => { state.currentPage--; renderTable(tableId); };
    container.appendChild(prevBtn);

    // Page Numbers (Simple version)
    const pageDisplay = document.createElement('div');
    pageDisplay.className = 'flex gap-1';

    for (let i = 1; i <= totalPages; i++) {
        if (totalPages > 5 && i > 2 && i < totalPages - 1 && Math.abs(i - state.currentPage) > 1) {
            if (i === 3 || i === totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'w-10 h-10 flex items-center justify-center text-gray-400';
                dots.innerText = '...';
                pageDisplay.appendChild(dots);
            }
            continue;
        }

        const btn = document.createElement('button');
        btn.className = `w-10 h-10 rounded-xl font-bold transition-all ${state.currentPage === i ? 'bg-blue-900 text-white shadow-lg shadow-blue-900/20' : 'bg-white border border-gray-200 text-gray-500 hover:border-blue-300'}`;
        btn.innerText = i;
        btn.onclick = () => { state.currentPage = i; renderTable(tableId); };
        pageDisplay.appendChild(btn);
    }
    container.appendChild(pageDisplay);

    // Next Button
    const nextBtn = document.createElement('button');
    nextBtn.className = `p-2 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-blue-600 transition-all ${state.currentPage === totalPages || totalPages === 0 ? 'opacity-50 pointer-events-none' : ''}`;
    nextBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>`;
    nextBtn.onclick = () => { state.currentPage++; renderTable(tableId); };
    container.appendChild(nextBtn);

    // Update info
    let totalEl = document.getElementById('total-results');
    if (tableId === 'visit-table') totalEl = document.getElementById('total-results-visit');
    if (tableId === 'restaurant-table') totalEl = document.getElementById('total-results-rest');

    if (totalEl) totalEl.innerText = totalFiltered;
}

// --- EXPORT FUNCTIONS ---

function toggleSelectAll(tableId, sourceCheckbox) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector('tbody');
    const checkboxes = tbody.querySelectorAll('input[type="checkbox"].row-checkbox');

    checkboxes.forEach(cb => cb.checked = sourceCheckbox.checked);
}

function exportData(type, tableId, onlySelected = false) {
    if (typeof XLSX === 'undefined' && (type === 'xlsx' || type === 'csv')) {
        alert("Excel export library failed to load. Please check your internet connection.");
        return;
    }
    if (typeof window.jspdf === 'undefined' && type === 'pdf') {
        alert("PDF export library failed to load. Please check your internet connection.");
        return;
    }

    const table = document.getElementById(tableId);
    const data = getTableData(table, onlySelected);

    if (data.length <= 1) { // Only header
        alert("No items selected/found to export.");
        return;
    }

    const filename = `${tableId}_${new Date().toISOString().slice(0, 10)}`;

    if (type === 'xlsx') {
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Report");
        XLSX.writeFile(wb, `${filename}.xlsx`);
    } else if (type === 'csv') {
        const ws = XLSX.utils.aoa_to_sheet(data);
        const csv = XLSX.utils.sheet_to_csv(ws);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `${filename}.csv`;
        link.click();
    } else if (type === 'pdf') {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');

        doc.setFontSize(18);
        doc.text("Dosti Welfare Report", 14, 22);
        doc.setFontSize(11);
        doc.setTextColor(100);
        doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 30);

        doc.autoTable({
            head: [data[0]],
            body: data.slice(1),
            startY: 40,
            theme: 'grid',
            headStyles: { fillColor: [9, 83, 156], textColor: [255, 255, 255], fontStyle: 'bold' },
            styles: { fontSize: 8, cellPadding: 3 },
            alternateRowStyles: { fillColor: [245, 247, 250] }
        });
        doc.save(`${filename}.pdf`);
    }
}

function getTableData(table, onlySelected = false) {
    const data = [];
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');

    headerCells.forEach((th, i) => {
        // Skip Checkbox (index 0 if it has input), # (index 1?), Actions
        // Assuming Checkbox is index 0. # is index 1.
        // Actually, let's just skip columns that contain inputs or are named 'Actions'
        if (th.querySelector('input') || th.textContent.trim().toLowerCase() === 'actions') return;
        headers.push(th.textContent.trim());
    });
    data.push(headers);

    // Filter rows
    const tableId = table.id;
    let rows;
    if (tableStates[tableId]) {
        const state = tableStates[tableId];
        // If searching, use filtered rows. If not, use all.
        // But for 'Selected', we must check the checkbox in the DOM row.
        // Since state.filteredRows refers to DOM elements (hopefully still valid), we can use them.
        rows = (state.filteredRows && state.filteredRows.length > 0)
            ? state.filteredRows
            : state.allRows;
    } else {
        rows = Array.from(table.querySelectorAll('tbody tr'));
    }

    if (onlySelected) {
        rows = rows.filter(row => {
            const cb = row.querySelector('input[type="checkbox"]');
            return cb && cb.checked;
        });
    }

    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach((td, i) => {
            // Skip Checkbox and Actions
            // The logic must align with Header skipping.
            // If header skipped index 0, we must skip index 0.

            // Check if corresponding header was skipped
            if (headerCells[i].querySelector('input') || headerCells[i].textContent.trim().toLowerCase() === 'actions') return;

            // Special handling for Status icons - extract title or content
            let text = "";
            const icons = td.querySelectorAll('[title]');

            if (icons.length > 0) {
                text = Array.from(icons).map(icon => icon.getAttribute('title')).join(', ');
            } else {
                // Extract text from all nodes, preserving line breaks for Name + Email columns
                // Filter out empty nodes and trim
                const textParts = Array.from(td.childNodes)
                    .map(node => node.textContent.trim())
                    .filter(txt => txt.length > 0);

                text = textParts.join(" | "); // Use a pipe or space for cleaner separation in Excel/CSV
            }
            rowData.push(text);
        });
        data.push(rowData);
    });
    return data;
}

function printTable(tableId, onlySelected = false) {
    const table = document.getElementById(tableId);
    const data = getTableData(table, onlySelected);

    if (data.length <= 1) { // Only header
        alert("No items selected/found to print.");
        return;
    }

    let printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Print Report</title>');
    printWindow.document.write('<style>body{font-family:sans-serif;padding:40px}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #ddd;padding:12px;text-align:left}th{background:#09539c;color:white}h1{color:#09539c}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>Dosti Welfare - Data Report</h1>');
    printWindow.document.write('<p>Generated on: ' + new Date().toLocaleString() + '</p>');

    let tableHtml = '<table><thead><tr>';
    data[0].forEach(h => tableHtml += `<th>${h}</th>`);
    tableHtml += '</tr></thead><tbody>';

    data.slice(1).forEach(row => {
        tableHtml += '<tr>';
        row.forEach(c => tableHtml += `<td>${c}</td>`);
        tableHtml += '</tr>';
    });

    tableHtml += '</tbody></table></body></html>';
    printWindow.document.write(tableHtml);
    printWindow.document.close();
    printWindow.print();
}

// --- GLOBAL EXPORT MAPPING ---
window.exportData = exportData;
window.printTable = printTable;
window.toggleSelectAll = toggleSelectAll;
