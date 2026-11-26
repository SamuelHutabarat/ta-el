import { paginateRows } from './table-pagination-core.js';

const pageSizes = [10, 25, 50];

function createElement(tag, className, text) {
    const element = document.createElement(tag);
    if (className) element.className = className;
    if (text !== undefined) element.textContent = text;
    return element;
}

function normalizeValue(value) {
    return value.replace(/\s+/g, ' ').trim().toLocaleLowerCase('id-ID');
}

function compareCells(left, right) {
    const leftText = normalizeValue(left.textContent);
    const rightText = normalizeValue(right.textContent);
    const numberPattern = /\d/;
    const leftNumber = Number(leftText.replace(/[^\d,-]/g, '').replace(/\./g, '').replace(',', '.'));
    const rightNumber = Number(rightText.replace(/[^\d,-]/g, '').replace(/\./g, '').replace(',', '.'));

    if (numberPattern.test(leftText) && numberPattern.test(rightText) && Number.isFinite(leftNumber) && Number.isFinite(rightNumber)) {
        return leftNumber - rightNumber;
    }

    return leftText.localeCompare(rightText, 'id', { numeric: true, sensitivity: 'base' });
}

function initializeTable(table) {
    if (table.dataset.paginationReady === 'true') return;

    const body = table.tBodies[0];
    if (!body) return;

    const originalRows = Array.from(body.rows);
    if (originalRows.length === 0) return;

    table.dataset.paginationReady = 'true';
    table.classList.add('client-paginated-table');

    const wrapper = createElement('div', 'client-table-wrapper');
    const toolbar = createElement('div', 'client-table-toolbar');
    const searchEnabled = table.dataset.search !== 'false';
    const searchInput = searchEnabled ? document.createElement('input') : null;

    if (searchInput) {
        searchInput.type = 'search';
        searchInput.className = 'client-table-search';
        searchInput.placeholder = 'Cari data...';
        searchInput.setAttribute('aria-label', 'Cari dalam tabel');
        const searchLabel = createElement('label', 'client-table-search-label', 'Cari');
        searchLabel.append(searchInput);
        toolbar.append(searchLabel);
    }

    const pageSizeSelect = document.createElement('select');
    pageSizeSelect.className = 'client-table-page-size';
    pageSizeSelect.setAttribute('aria-label', 'Jumlah baris per halaman');
    for (const size of pageSizes) {
        const option = document.createElement('option');
        option.value = String(size);
        option.textContent = `${size} baris`;
        pageSizeSelect.append(option);
    }
    pageSizeSelect.value = table.dataset.pageLength || '10';
    if (!pageSizes.includes(Number(pageSizeSelect.value))) pageSizeSelect.value = '10';
    toolbar.append(pageSizeSelect);

    const footer = createElement('div', 'client-table-footer');
    const info = createElement('span', 'client-table-info');
    info.setAttribute('aria-live', 'polite');
    const navigation = createElement('div', 'client-table-navigation');
    const previous = createElement('button', 'client-table-page-button', 'Sebelumnya');
    previous.type = 'button';
    const next = createElement('button', 'client-table-page-button', 'Berikutnya');
    next.type = 'button';
    navigation.append(previous, next);
    footer.append(info, navigation);

    table.insertAdjacentElement('beforebegin', wrapper);
    wrapper.append(toolbar, table, footer);

    let currentPage = 1;
    let currentSort = null;
    let filteredRows = originalRows;

    const update = () => {
        const page = paginateRows(filteredRows, currentPage, pageSizeSelect.value);
        currentPage = page.page;
        const visibleRows = new Set(page.rows);

        for (const row of originalRows) row.hidden = !visibleRows.has(row);

        let emptyRow = body.querySelector('[data-client-empty-row]');
        if (page.totalRows === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.dataset.clientEmptyRow = 'true';
                const cell = document.createElement('td');
                cell.colSpan = Math.max(1, table.tHead?.rows[0]?.cells.length || 1);
                cell.className = 'client-table-empty';
                cell.textContent = 'Tidak ada data yang cocok.';
                emptyRow.append(cell);
                body.append(emptyRow);
            }
        } else {
            emptyRow?.remove();
        }

        info.textContent = `Menampilkan ${page.start} sampai ${page.end} dari ${page.totalRows} data`;
        previous.disabled = !page.hasPrevious;
        next.disabled = !page.hasNext;
    };

    const applySearch = () => {
        const term = normalizeValue(searchInput?.value || '');
        filteredRows = originalRows.filter((row) => !term || normalizeValue(row.textContent).includes(term));

        if (currentSort) {
            const { columnIndex, direction } = currentSort;
            filteredRows.sort((left, right) => {
                const result = compareCells(left.cells[columnIndex] || createElement('td', '', ''), right.cells[columnIndex] || createElement('td', '', ''));
                return direction === 'asc' ? result : -result;
            });
        }

        currentPage = 1;
        update();
    };

    searchInput?.addEventListener('input', applySearch);
    pageSizeSelect.addEventListener('change', () => {
        currentPage = 1;
        update();
    });
    previous.addEventListener('click', () => {
        currentPage -= 1;
        update();
    });
    next.addEventListener('click', () => {
        currentPage += 1;
        update();
    });

    const headers = table.tHead?.rows[0]?.cells || [];
    Array.from(headers).forEach((header, columnIndex) => {
        if (normalizeValue(header.textContent) === 'aksi' || header.dataset.sortable === 'false') return;
        header.classList.add('client-table-sortable');
        header.tabIndex = 0;
        header.setAttribute('role', 'button');
        header.setAttribute('aria-label', `Urutkan berdasarkan ${header.textContent.trim()}`);

        const sort = () => {
            const direction = currentSort?.columnIndex === columnIndex && currentSort.direction === 'asc' ? 'desc' : 'asc';
            currentSort = { columnIndex, direction };
            headers.forEach((cell) => cell.removeAttribute('aria-sort'));
            header.setAttribute('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
            applySearch();
        };

        header.addEventListener('click', sort);
        header.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                sort();
            }
        });
    });

    update();
}

function initializeTables() {
    document.querySelectorAll('table.table-coffee[id]').forEach(initializeTable);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTables, { once: true });
} else {
    initializeTables();
}
