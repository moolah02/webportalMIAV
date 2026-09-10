{{-- Current Terminals List Modal (edit mode; opened by viewProjectTerminals()) --}}
<div class="modal fade tlm" id="terminalListModal" tabindex="-1" aria-labelledby="terminalListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terminalListModalLabel">
                    Project Terminals
                    <span class="badge badge-gray" id="terminalListCount">0</span>
                </h5>
                <button type="button" class="tlm-close" data-dismiss="modal" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="modal-body">
                <div class="tlm-table">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Terminal ID</th>
                                <th>Merchant Name</th>
                                <th>City</th>
                                <th>Region</th>
                                <th>Status</th>
                                <th>Added On</th>
                                <th width="60">Action</th>
                            </tr>
                        </thead>
                        <tbody id="currentTerminalsTable"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.tlm .modal-content { border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.tlm .modal-header { background: var(--mv-surface); border-bottom: 1px solid var(--mv-line); padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; }
.tlm .modal-title { font-size: 15px; font-weight: 600; color: var(--mv-ink); margin: 0; display: flex; align-items: center; gap: 8px; }
.tlm .modal-body { padding: 16px 18px; }
.tlm .modal-footer { border-top: 1px solid var(--mv-line); padding: 12px 18px; display: flex; justify-content: flex-end; }
.tlm-close { background: none; border: 0; color: var(--mv-muted); cursor: pointer; padding: 4px; border-radius: 6px; display: inline-flex; }
.tlm-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.tlm-table { overflow: auto; border: 1px solid var(--mv-line); border-radius: 8px; }
.tlm-table td:first-child { font-family: var(--mv-mono); font-size: 12.5px; }
</style>
