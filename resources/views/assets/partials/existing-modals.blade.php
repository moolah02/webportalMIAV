{{-- Quick Actions + Stock Update modals. Not currently included by any view; if used,
     include from assets/index.blade.php, whose styles block defines the .as-* classes. --}}

<!-- Quick Actions Modal -->
<div id="assetQuickActionsModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal is-narrow">
        <div class="as-modal-head">
            <h3 id="modalAssetName">Asset Actions</h3>
            <button type="button" onclick="closeAssetActions()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">
            <div class="as-menu">
                <button type="button" onclick="viewAsset()" class="modal-action-btn as-menu-item">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-eye"/></svg>
                    <span>
                        <strong>View Details</strong>
                        <small>See complete asset information</small>
                    </span>
                </button>

                <button type="button" onclick="editAsset()" class="modal-action-btn as-menu-item">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-edit"/></svg>
                    <span>
                        <strong>Edit Asset</strong>
                        <small>Update asset information</small>
                    </span>
                </button>

                <button type="button" onclick="updateStock()" class="modal-action-btn as-menu-item">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-layers"/></svg>
                    <span>
                        <strong>Update Stock</strong>
                        <small>Adjust stock quantities</small>
                    </span>
                </button>

                <button type="button" onclick="deleteAsset()" class="modal-action-btn as-menu-item is-danger">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-trash"/></svg>
                    <span>
                        <strong>Delete Asset</strong>
                        <small>Remove asset permanently</small>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Modal -->
<div id="stockUpdateModal" class="as-overlay" style="display: none;" role="dialog" aria-modal="true">
    <div class="as-modal is-narrow">
        <div class="as-modal-head">
            <h3>Update Stock</h3>
            <button type="button" onclick="closeStockModal()" class="as-x" title="Close" aria-label="Close"><svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="as-modal-body">
            <form id="stockUpdateForm">
                <div>
                    <label class="ui-label" for="newStockQuantity">New Stock Quantity</label>
                    <input type="number" id="newStockQuantity" min="0" class="ui-input w-full">
                </div>
                <div class="as-modal-actions">
                    <button type="button" onclick="closeStockModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
