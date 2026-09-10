{{-- Styles shared by roles/create and roles/edit (permission matrix + sidebar) --}}
@push('styles')
<style>
.rp-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 16px; align-items: start; }
@media (max-width: 1100px) { .rp-layout { grid-template-columns: 1fr; } }
.rp-main, .rp-side { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
@media (min-width: 1101px) { .rp-side { position: sticky; top: 16px; } }
.rp-body { padding: 16px 18px; }
.rp-input { width: 100%; max-width: 520px; padding: 9px 12px; font-size: 14px; }
.rp-req { color: var(--mv-crit); }
.rp-hint { font-size: 12px; color: var(--mv-muted); margin-top: 6px; }
.rp-error { font-size: 12px; color: var(--mv-crit); margin-top: 6px; }
.rp-alert { padding: 11px 14px; font-size: 13px; border: 1px solid; }
.rp-alert ul { margin: 6px 0 0 18px; list-style: disc; }
.rp-toolbar { display: flex; gap: 6px; }

/* Info strip (edit) */
.rp-info { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 28px; padding: 14px 18px; }
.rp-info-item { display: flex; flex-direction: column; gap: 2px; }
.rp-info-item span { font-size: 11.5px; color: var(--mv-muted); }
.rp-info-item strong { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.rp-info .badge { margin-left: auto; }

/* Permission groups */
.permission-category { border: 1px solid var(--mv-line); border-radius: 10px; background: var(--mv-surface); overflow: hidden; }
.permission-category + .permission-category { margin-top: 12px; }
.category-header { display: flex; align-items: center; gap: 12px; padding: 10px 14px; background: var(--mv-surface-2); cursor: pointer; user-select: none; }
.rp-cat-icon { width: 28px; height: 28px; border-radius: 7px; background: var(--mv-surface); border: 1px solid var(--mv-line); color: var(--mv-ink-2); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rp-cat-title { font-size: 13.5px; font-weight: 600; color: var(--mv-ink); }
.rp-cat-sub { font-size: 12px; color: var(--mv-muted); }
.rp-cat-actions { display: flex; align-items: center; gap: 4px; margin-left: auto; }
.category-selection-info { font-size: 12px; color: var(--mv-muted); font-variant-numeric: tabular-nums; margin-right: 8px; white-space: nowrap; }
.rp-link-btn { background: none; border: 0; padding: 4px 7px; font-size: 12.5px; font-weight: 500; color: var(--mv-accent-ink); cursor: pointer; border-radius: 6px; white-space: nowrap; }
.rp-link-btn:hover { background: var(--mv-accent-soft); }
.category-toggle-icon { color: var(--mv-muted); display: inline-flex; margin-left: 4px; transition: transform .2s ease; }
.category-content { overflow: hidden; transition: max-height .25s ease; border-top: 1px solid var(--mv-line); }
.category-content:not(.expanded) { border-top-color: transparent; }
.rp-perm-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); margin-bottom: -1px; }
@media (max-width: 760px) { .rp-perm-grid { grid-template-columns: 1fr; } }
.permission-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; border-bottom: 1px solid var(--mv-line); cursor: pointer; margin: 0; transition: background-color .12s ease; }
@media (min-width: 761px) { .rp-perm-grid .permission-item:nth-child(odd) { border-right: 1px solid var(--mv-line); } }
.permission-item:hover { background: var(--mv-surface-2); }
.permission-item.selected { background: var(--mv-accent-soft); }
.permission-item.danger { background: var(--mv-crit-soft); }
.permission-item input[type="checkbox"] { width: 15px; height: 15px; margin-top: 2px; flex-shrink: 0; accent-color: var(--mv-accent); cursor: pointer; }
.rp-perm-text { display: flex; flex-direction: column; min-width: 0; }
.perm-name { font-size: 13px; font-weight: 500; color: var(--mv-ink); }
.perm-desc { font-size: 12px; color: var(--mv-muted); line-height: 1.4; margin-top: 2px; }
.rp-danger-chip { display: inline-block; font-size: 10.5px; font-weight: 600; letter-spacing: .03em; color: var(--mv-crit); background: var(--mv-crit-soft); border: 1px solid #F2CACA; border-radius: 5px; padding: 0 5px; margin-left: 6px; vertical-align: 1px; }

/* Sidebar */
.rp-selected { min-height: 64px; max-height: 280px; overflow-y: auto; }
.rp-empty { color: var(--mv-muted); font-size: 12.5px; padding: 18px 4px; text-align: center; }
.rp-chips { display: flex; flex-wrap: wrap; gap: 5px; }
.rp-chip { font-size: 11.5px; padding: 2px 7px; border-radius: 5px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); color: var(--mv-ink-2); }
.rp-chip.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.rp-templates { padding: 6px; display: flex; flex-direction: column; gap: 1px; }
.template-btn { display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; background: none; border: 0; border-radius: 7px; padding: 8px 10px; font-size: 13px; color: var(--mv-ink); cursor: pointer; }
.template-btn .mv-i { color: var(--mv-muted); }
.template-btn:hover { background: var(--mv-surface-2); }
.template-btn.danger, .template-btn.danger .mv-i { color: var(--mv-crit); }
.template-btn.danger:hover { background: var(--mv-crit-soft); }
.template-btn.clear { color: var(--mv-ink-2); }
.rp-sep { height: 1px; background: var(--mv-line); margin: 5px 4px; }
.rp-summary { display: flex; flex-direction: column; gap: 0; padding-top: 6px; padding-bottom: 6px; }
.summary-item { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; font-size: 13px; color: var(--mv-ink-2); }
.summary-item + .summary-item { border-top: 1px solid var(--mv-line); }
.summary-item > span:last-child { font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.rp-actions { display: flex; justify-content: flex-end; gap: 8px; padding: 12px 18px; }
.rp-note { font-size: 12px; color: var(--mv-warn); background: var(--mv-warn-soft); border-top: 1px solid #F0DDB6; padding: 10px 18px; display: flex; gap: 8px; align-items: flex-start; }
</style>
@endpush
