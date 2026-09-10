{{-- Terminal Preview Modal (opened by terminal-upload-section via $('#terminalPreviewModal').modal()) --}}
<div class="modal fade tpm" id="terminalPreviewModal" tabindex="-1" aria-labelledby="terminalPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terminalPreviewModalLabel">Terminal Upload Preview</h5>
                <button type="button" class="tpm-close" data-dismiss="modal" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="modal-body">
                <div class="tpm-summary">
                    <div><div class="tpm-v" id="previewTotalCount">0</div><div class="tpm-l">Total in File</div></div>
                    <div><div class="tpm-v is-good" id="previewFoundCount">0</div><div class="tpm-l">Ready to Assign</div></div>
                    <div><div class="tpm-v" id="previewAlreadyCount">0</div><div class="tpm-l">Already Assigned</div></div>
                    <div><div class="tpm-v is-warn" id="previewNotFoundCount">0</div><div class="tpm-l">Not Found</div></div>
                </div>

                <div id="foundTerminalsSection" class="tpm-section">
                    <div class="tpm-section-head">
                        <h6><svg class="mv-i mv-i-sm" aria-hidden="true" style="color:var(--mv-good)"><use href="#i-check-circle"/></svg> Terminals Ready to Assign</h6>
                        <label class="tpm-check" for="selectAllFound"><input type="checkbox" id="selectAllFound" onchange="selectAllFound(this)" checked> Select All</label>
                    </div>
                    <div class="tpm-table" style="max-height: 250px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr><th width="40"></th><th>Terminal ID</th><th>Merchant Name</th><th>City</th><th>Status</th></tr>
                            </thead>
                            <tbody id="foundTerminalsTable"></tbody>
                        </table>
                    </div>
                </div>

                <div id="alreadyAssignedSection" class="tpm-section" style="display: none;">
                    <div class="tpm-section-head">
                        <h6><svg class="mv-i mv-i-sm" aria-hidden="true" style="color:var(--mv-muted)"><use href="#i-info"/></svg> Already Assigned to Project (Will Skip)</h6>
                    </div>
                    <div class="tpm-table" style="max-height: 150px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr><th>Terminal ID</th><th>Merchant Name</th><th>City</th></tr>
                            </thead>
                            <tbody id="alreadyAssignedTable"></tbody>
                        </table>
                    </div>
                </div>

                <div id="notFoundSection" class="tpm-section" style="display: none;">
                    <div class="tpm-section-head">
                        <h6><svg class="mv-i mv-i-sm" aria-hidden="true" style="color:var(--mv-warn)"><use href="#i-alert-triangle"/></svg> Terminals Not Found in System</h6>
                        <label class="tpm-check" for="selectAllMissing"><input type="checkbox" id="selectAllMissing" onchange="selectAllMissing(this)"> Select All (to create)</label>
                    </div>
                    <div class="alert-warning tpm-note">
                        Terminals marked "Can create" have enough data in your file to be created. Check "Create missing terminals" option to include them.
                    </div>
                    <div class="tpm-table" style="max-height: 200px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr><th width="60">Create</th><th>Terminal ID</th><th>Reason</th><th>Data Status</th></tr>
                            </thead>
                            <tbody id="notFoundTerminalsTable"></tbody>
                        </table>
                    </div>
                </div>

                <div class="tpm-reason">
                    <label class="ui-label" for="modalInclusionReason">Inclusion Reason (optional)</label>
                    <input type="text" class="ui-input" id="modalInclusionReason" placeholder="e.g., Initial project scope, Client request, etc." value="Bulk Upload">
                    <div class="tpm-l" style="margin-top:4px">This reason will be recorded for all assigned terminals</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn-primary" onclick="confirmTerminalUpload()"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg> Confirm & Assign Terminals</button>
            </div>
        </div>
    </div>
</div>

<style>
.tpm .modal-content { border: 1px solid var(--mv-line); border-radius: 12px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.tpm .modal-header { background: var(--mv-surface); border-bottom: 1px solid var(--mv-line); padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; }
.tpm .modal-title { font-size: 15px; font-weight: 600; color: var(--mv-ink); margin: 0; }
.tpm .modal-body { padding: 16px 18px; }
.tpm .modal-footer { border-top: 1px solid var(--mv-line); padding: 12px 18px; display: flex; justify-content: flex-end; gap: 8px; }
.tpm-close { background: none; border: 0; color: var(--mv-muted); cursor: pointer; padding: 4px; border-radius: 6px; display: inline-flex; }
.tpm-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.tpm-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); border: 1px solid var(--mv-line); border-radius: 8px; margin-bottom: 16px; }
.tpm-summary > div { padding: 12px 14px; }
.tpm-summary > div + div { border-left: 1px solid var(--mv-line); }
.tpm-v { font-size: 22px; font-weight: 600; color: var(--mv-ink); font-variant-numeric: tabular-nums; line-height: 1.15; }
.tpm-v.is-good { color: var(--mv-good); }
.tpm-v.is-warn { color: var(--mv-warn); }
.tpm-l { font-size: 12px; color: var(--mv-muted); }
.tpm-section { margin-bottom: 16px; }
.tpm-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; gap: 12px; }
.tpm-section-head h6 { margin: 0; font-size: 13px; font-weight: 600; color: var(--mv-ink); display: flex; align-items: center; gap: 7px; }
.tpm-check { display: inline-flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--mv-ink-2); cursor: pointer; margin: 0; }
.tpm-check input, .tpm-table input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--mv-accent); }
.tpm-table { overflow: auto; border: 1px solid var(--mv-line); border-radius: 8px; }
.tpm-table thead th { position: sticky; top: 0; z-index: 1; }
.tpm-table td:nth-child(2) { font-family: var(--mv-mono); font-size: 12.5px; }
.tpm-note { padding: 9px 12px; font-size: 12.5px; border: 1px solid; border-radius: 8px; margin-bottom: 8px; }
.tpm-reason { border-top: 1px solid var(--mv-line); padding-top: 14px; }
.tpm-reason .ui-input { width: 100%; max-width: 520px; }
</style>
