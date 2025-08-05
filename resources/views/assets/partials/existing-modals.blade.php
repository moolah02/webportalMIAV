<!-- Quick Actions Modal (Your existing modal) -->
<div id="assetQuickActionsModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 400px; inline-size: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>📦</span>
                <span id="modalAssetName">Asset Actions</span>
            </h3>
            <button onclick="closeAssetActions()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <div style="display: grid; gap: 12px;">
                <button onclick="viewAsset()" class="modal-action-btn" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white;">
                    <span style="font-size: 20px;">👁️</span>
                    <div>
                        <div style="font-weight: bold;">View Details</div>
                        <div style="font-size: 12px; opacity: 0.9;">See complete asset information</div>
                    </div>
                </button>
                
                <button onclick="editAsset()" class="modal-action-btn" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white;">
                    <span style="font-size: 20px;">✏️</span>
                    <div>
                        <div style="font-weight: bold;">Edit Asset</div>
                        <div style="font-size: 12px; opacity: 0.9;">Update asset information</div>
                    </div>
                </button>
                
                <button onclick="updateStock()" class="modal-action-btn" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;">
                    <span style="font-size: 20px;">📊</span>
                    <div>
                        <div style="font-weight: bold;">Update Stock</div>
                        <div style="font-size: 12px; opacity: 0.9;">Adjust stock quantities</div>
                    </div>
                </button>
                
                <button onclick="deleteAsset()" class="modal-action-btn" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white;">
                    <span style="font-size: 20px;">🗑️</span>
                    <div>
                        <div style="font-weight: bold;">Delete Asset</div>
                        <div style="font-size: 12px; opacity: 0.9;">Remove asset permanently</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Modal (Your existing modal) -->
<div id="stockUpdateModal" style="display: none; position: fixed; top: 0; left: 0; inline-size: 100%; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1001; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; padding: 0; max-inline-size: 400px; inline-size: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative;">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>📊</span>
                <span>Update Stock</span>
            </h3>
            <button onclick="closeStockModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">×</button>
        </div>
        
        <!-- Modal Body -->
        <div style="padding: 20px;">
            <form id="stockUpdateForm">
                <div style="margin-block-end: 15px;">
                    <label style="display: block; margin-block-end: 5px; font-weight: 500;">New Stock Quantity</label>
                    <input type="number" id="newStockQuantity" min="0" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Update Stock</button>
                    <button type="button" onclick="closeStockModal()" class="btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>