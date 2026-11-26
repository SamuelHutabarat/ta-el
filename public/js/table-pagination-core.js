export function paginateRows(rows, requestedPage, requestedPageSize) {
    const pageSize = Math.max(1, Number.parseInt(requestedPageSize, 10) || 10);
    const totalRows = rows.length;
    const totalPages = Math.max(1, Math.ceil(totalRows / pageSize));
    const requested = Number.parseInt(requestedPage, 10) || 1;
    const page = Math.min(totalPages, Math.max(1, requested));
    const startIndex = (page - 1) * pageSize;
    const endIndex = Math.min(startIndex + pageSize, totalRows);

    return {
        rows: rows.slice(startIndex, endIndex),
        page,
        pageSize,
        totalRows,
        totalPages,
        start: totalRows === 0 ? 0 : startIndex + 1,
        end: endIndex,
        hasPrevious: page > 1,
        hasNext: page < totalPages,
    };
}
