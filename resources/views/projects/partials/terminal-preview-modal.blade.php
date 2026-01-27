{{-- Terminal Preview Modal --}}
<div class="modal fade" id="terminalPreviewModal" tabindex="-1" aria-labelledby="terminalPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="terminalPreviewModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Terminal Upload Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                {{-- Summary Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-primary" id="previewTotalCount">0</div>
                                <div class="text-muted small">Total in File</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 bg-success bg-opacity-10 h-100">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-success" id="previewFoundCount">0</div>
                                <div class="text-muted small">Ready to Assign</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 bg-info bg-opacity-10 h-100">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-info" id="previewAlreadyCount">0</div>
                                <div class="text-muted small">Already Assigned</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 bg-warning bg-opacity-10 h-100">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-warning" id="previewNotFoundCount">0</div>
                                <div class="text-muted small">Not Found</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Found Terminals Section --}}
                <div id="foundTerminalsSection" class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Terminals Ready to Assign
                        </h6>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="selectAllFound" onchange="selectAllFound(this)" checked>
                            <label class="form-check-label small" for="selectAllFound">Select All</label>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40"></th>
                                    <th>Terminal ID</th>
                                    <th>Merchant Name</th>
                                    <th>City</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="foundTerminalsTable">
                                {{-- Populated by JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Already Assigned Section --}}
                <div id="alreadyAssignedSection" class="mb-4" style="display: none;">
                    <h6 class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Already Assigned to Project (Will Skip)
                    </h6>
                    <div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0 table-secondary">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Terminal ID</th>
                                    <th>Merchant Name</th>
                                    <th>City</th>
                                </tr>
                            </thead>
                            <tbody id="alreadyAssignedTable">
                                {{-- Populated by JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Not Found Section --}}
                <div id="notFoundSection" class="mb-4" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Terminals Not Found in System
                        </h6>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="selectAllMissing" onchange="selectAllMissing(this)">
                            <label class="form-check-label small" for="selectAllMissing">Select All (to create)</label>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 mb-2">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Terminals marked "Can create" have enough data in your file to be created. Check "Create missing terminals" option to include them.
                        </small>
                    </div>
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40">Create</th>
                                    <th>Terminal ID</th>
                                    <th>Reason</th>
                                    <th>Data Status</th>
                                </tr>
                            </thead>
                            <tbody id="notFoundTerminalsTable">
                                {{-- Populated by JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Inclusion Reason --}}
                <div class="border-top pt-3">
                    <label class="form-label fw-semibold">Inclusion Reason (optional)</label>
                    <input type="text"
                           class="form-control"
                           id="modalInclusionReason"
                           placeholder="e.g., Initial project scope, Client request, etc."
                           value="Bulk Upload">
                    <small class="text-muted">This reason will be recorded for all assigned terminals</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" onclick="confirmTerminalUpload()">
                    <i class="fas fa-check me-1"></i> Confirm & Assign Terminals
                </button>
            </div>
        </div>
    </div>
</div>
