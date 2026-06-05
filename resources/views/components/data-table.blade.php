@props([
    'id' => 'data-table-' . bin2hex(random_bytes(4)),
    'columns' => [],
    'rows' => [],
    'searchPlaceholder' => 'Search...',
    'headerBg' => 'warning',
    'showRowNumbers' => true,
    'emptyMessage' => 'No data found.',
])

@php
    $columns = is_array($columns) ? $columns : $columns->toArray();
@endphp

<div class="data-table-wrapper" id="{{ $id }}-wrapper">
    <div class="mb-3">
        <input type="text"
               class="form-control"
               id="{{ $id }}-search"
               placeholder="{{ $searchPlaceholder }}"
               autocomplete="off">
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm mb-0 data-table" id="{{ $id }}" data-table-id="{{ $id }}">
            <thead>
                <tr>
                    @if($showRowNumbers)
                        <th class="text-nowrap bg-{{ $headerBg }} bg-opacity-75 fw-bold" style="width: 40px;">#</th>
                    @endif
                    @foreach($columns as $col)
                        @php
                            $colKey = is_array($col) ? ($col['key'] ?? $col['label'] ?? '') : $col;
                            $colLabel = is_array($col) ? ($col['label'] ?? $colKey) : $col;
                            $sortable = is_array($col) ? ($col['sortable'] ?? true) : true;
                        @endphp
                        <th class="text-nowrap bg-{{ $headerBg }} bg-opacity-75 fw-bold {{ $sortable ? 'sortable' : '' }}"
                            data-key="{{ $colKey }}"
                            data-sortable="{{ $sortable ? '1' : '0' }}">
                            {{ $colLabel }}
                            @if($sortable)
                                <span class="sort-icon float-end">↕</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                    <tr data-row-index="{{ $i }}">
                        @if($showRowNumbers)
                            <td class="text-muted">{{ $i + 1 }}</td>
                        @endif
                        @foreach($columns as $col)
                            @php
                                $colKey = is_array($col) ? ($col['key'] ?? $col['label'] ?? '') : $col;
                                if (is_object($row) && isset($row->data) && is_array($row->data)) {
                                    $value = $row->data[$colKey] ?? '';
                                } else {
                                    $value = is_object($row) ? ($row->{$colKey} ?? '') : ($row[$colKey] ?? '');
                                }
                                if (is_numeric($value)) {
                                    if (str_contains($colKey, 'weightage') || str_contains($colKey, 'pct')) {
                                        $value = number_format((float) $value, 2);
                                    } elseif ($colKey === 'return') {
                                        $value = number_format((float) $value, 2) . '%';
                                    }
                                }
                            @endphp
                            <td data-key="{{ $colKey }}">{{ $value ?: '—' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="{{ $id }}-empty" class="text-muted text-center py-4 d-none">{{ $emptyMessage }}</div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.data-table').forEach(function(table) {
        var id = table.getAttribute('data-table-id');
        var searchInput = document.getElementById(id + '-search');
        var emptyEl = document.getElementById(id + '-empty');
        var tbody = table.querySelector('tbody');
        var rows = Array.from(tbody.querySelectorAll('tr'));
        var sortCol = null;
        var sortDir = 1;

        function filterRows() {
            var term = (searchInput?.value || '').toLowerCase();
            var visible = 0;
            rows.forEach(function(tr) {
                var text = tr.textContent.toLowerCase();
                var show = !term || text.includes(term);
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (emptyEl) emptyEl.classList.toggle('d-none', visible > 0);
        }

        function getCellValue(tr, colIdx) {
            var cells = tr.querySelectorAll('td');
            var cell = cells[colIdx];
            if (!cell) return '';
            var text = cell.textContent.trim();
            var num = parseFloat(text.replace(/,/g, ''));
            return isNaN(num) ? text : num;
        }

        function sortTable(colIdx) {
            var headerCells = table.querySelectorAll('thead th.sortable');
            var key = headerCells[colIdx]?.getAttribute('data-key');
            if (!key) return;
            sortCol = sortCol === colIdx ? sortCol : colIdx;
            sortDir = sortCol === colIdx ? -sortDir : 1;
            sortCol = colIdx;

            var hasRowNum = !!table.querySelector('thead th[style*="width: 40px"]');
            var dataColIdx = hasRowNum ? colIdx + 1 : colIdx;

            rows.sort(function(a, b) {
                var va = getCellValue(a, dataColIdx);
                var vb = getCellValue(b, dataColIdx);
                if (typeof va === 'number' && typeof vb === 'number') {
                    return (va - vb) * sortDir;
                }
                return String(va).localeCompare(String(vb)) * sortDir;
            });

            rows.forEach(function(r) { tbody.appendChild(r); });
            table.querySelectorAll('.sort-icon').forEach(function(s) { s.textContent = '↕'; });
            var icon = headerCells[colIdx]?.querySelector('.sort-icon');
            if (icon) icon.textContent = sortDir > 0 ? '↑' : '↓';

            var rowNum = 1;
            rows.forEach(function(tr) {
                if (tr.style.display !== 'none') {
                    var firstTd = tr.querySelector('td');
                    if (firstTd && firstTd.classList.contains('text-muted')) firstTd.textContent = rowNum++;
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
            searchInput.addEventListener('keyup', filterRows);
        }

        table.querySelectorAll('thead th.sortable').forEach(function(th, idx) {
            th.style.cursor = 'pointer';
            th.addEventListener('click', function() { sortTable(idx); });
        });
    });
});
</script>
@endpush
@endonce
