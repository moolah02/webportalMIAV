{{-- Current Terminals List Modal (for Edit mode) --}}
<div class="modal fade" id="terminalListModal" tabindex="-1" aria-labelledby="terminalListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="terminalListModalLabel">
                    <i class="fas fa-list me-2"></i>
                    Project Terminals
                    <span class="badge bg-light text-dark ms-2" id="terminalListCount">0</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                        <tbody id="currentTerminalsTable">
                            {{-- Populated by JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
