{{-- Shared layout styles for employees/create and employees/edit --}}
@push('styles')
<style>
.ef-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 1000px) { .ef-layout { grid-template-columns: 1fr; } }
.ef-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.ef-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 16px 18px; }
.ef-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.ef-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.ef-full { grid-column: 1 / -1; }
@media (max-width: 760px) { .ef-grid, .ef-grid-3, .ef-grid-4 { grid-template-columns: 1fr; } }
.ef-grid .ui-input, .ef-grid .ui-select, .ef-body .ui-select { width: 100%; }
.ef-body { padding: 16px 18px; }
.ef-req { color: var(--mv-crit); }
.ef-opt { color: var(--mv-muted); font-weight: 400; }
.ef-hint { font-size: 12px; color: var(--mv-muted); margin-top: 5px; }
.ef-error { font-size: 12px; color: var(--mv-crit); margin-top: 5px; }
.ef-alert { padding: 11px 14px; font-size: 13px; border: 1px solid; margin-bottom: 16px; }
.ef-alert ul { margin: 6px 0 0 18px; list-style: disc; }
.ef-roles { border: 1px solid var(--mv-line); border-radius: 8px; overflow: hidden; }
.ef-roles-note { font-size: 12px; color: var(--mv-muted); padding: 9px 12px; background: var(--mv-surface-2); border-bottom: 1px solid var(--mv-line); }
.ef-roles-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); }
@media (max-width: 900px) { .ef-roles-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.ef-role { display: flex; align-items: center; gap: 9px; padding: 9px 12px; font-size: 13px; color: var(--mv-ink); cursor: pointer; border-bottom: 1px solid var(--mv-line); border-right: 1px solid var(--mv-line); margin: 0; }
.ef-role:hover { background: var(--mv-surface-2); }
.ef-role input { width: 15px; height: 15px; accent-color: var(--mv-accent); cursor: pointer; }
.ef-check { display: flex; align-items: center; gap: 9px; font-size: 13px; color: var(--mv-ink); cursor: pointer; }
.ef-check input { width: 15px; height: 15px; accent-color: var(--mv-accent); }
.ef-code { border: 1px dashed var(--mv-line-strong); border-radius: 8px; padding: 12px 14px; background: var(--mv-surface-2); }
.ef-code-v { font-family: var(--mv-mono); font-size: 14px; font-weight: 500; color: var(--mv-ink); margin: 2px 0; }
.ef-chips { display: flex; flex-wrap: wrap; gap: 5px; }
.ef-chip { font-size: 11.5px; padding: 2px 7px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); }
.ef-muted { color: var(--mv-muted); font-size: 12.5px; }
.ef-actions { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; flex-wrap: wrap; }
.ef-rows { padding: 4px 18px; }
.ef-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; font-size: 13px; color: var(--mv-ink-2); gap: 10px; }
.ef-row + .ef-row { border-top: 1px solid var(--mv-line); }
.ef-row strong { font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.ef-note { padding: 12px 18px; font-size: 12.5px; color: var(--mv-ink-2); line-height: 1.7; }
.ef-note strong { color: var(--mv-ink); font-weight: 600; }
</style>
@endpush
