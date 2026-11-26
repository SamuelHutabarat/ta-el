import test from 'node:test';
import assert from 'node:assert/strict';
import { paginateRows } from '../../public/js/table-pagination-core.js';

test('pagination returns the requested page and correct navigation state', () => {
    const rows = Array.from({ length: 25 }, (_, index) => index + 1);

    const first = paginateRows(rows, 1, 10);
    assert.deepEqual(first.rows, rows.slice(0, 10));
    assert.equal(first.page, 1);
    assert.equal(first.totalPages, 3);
    assert.equal(first.hasPrevious, false);
    assert.equal(first.hasNext, true);

    const second = paginateRows(rows, first.page + 1, 10);
    assert.deepEqual(second.rows, rows.slice(10, 20));
    assert.equal(second.hasPrevious, true);
    assert.equal(second.hasNext, true);

    const last = paginateRows(rows, second.page + 1, 10);
    assert.deepEqual(last.rows, rows.slice(20));
    assert.equal(last.hasPrevious, true);
    assert.equal(last.hasNext, false);
});

test('pagination clamps invalid pages and handles empty results', () => {
    const rows = [1, 2, 3];

    assert.equal(paginateRows(rows, 99, 2).page, 2);
    assert.equal(paginateRows(rows, 0, 2).page, 1);
    assert.deepEqual(paginateRows([], 1, 10), {
        rows: [],
        page: 1,
        pageSize: 10,
        totalRows: 0,
        totalPages: 1,
        start: 0,
        end: 0,
        hasPrevious: false,
        hasNext: false,
    });
});
