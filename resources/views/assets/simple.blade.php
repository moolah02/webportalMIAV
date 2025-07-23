@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">📦 Internal Assets</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Manage your company assets and inventory</p>
        </div>
        <button onclick="alert('Coming soon!')" style="background: #2196f3; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">
            + Add New Asset
        </button>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">📦</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">0</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Assets</div>
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">✅</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">0</div>
                    <div style="font-size: 14px; opacity: 0.9;">Active Assets</div>
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">⚠️</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">0</div>
                    <div style="font-size: 14px; opacity: 0.9;">Low Stock</div>
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 32px;">💰</div>
                <div>
                    <div style="font-size: 28px; font-weight: bold;">$0</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
        <div style="font-size: 64px; margin-bottom: 20px;">🏗️</div>
        <h3 style="color: #333; margin-bottom: 15px;">Asset Management System</h3>
        <p style="color: #666; font-size: 16px; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
            The complete asset management system is under development. This will include asset tracking, inventory management, employee requests, and approval workflows.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; max-width: 800px; margin-left: auto; margin-right: auto;">
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 32px; margin-bottom: 10px;">📋</div>
                <h4 style="margin-bottom: 10px; color: #333;">Asset Tracking</h4>
                <p style="font-size: 14px; color: #666;">Track all company assets and their status</p>
            </div>
            
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 32px; margin-bottom: 10px;">📊</div>
                <h4 style="margin-bottom: 10px; color: #333;">Inventory Management</h4>
                <p style="font-size: 14px; color: #666;">Monitor stock levels and reorder points</p>
            </div>
            
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 32px; margin-bottom: 10px;">🛒</div>
                <h4 style="margin-bottom: 10px; color: #333;">Request System</h4>
                <p style="font-size: 14px; color: #666;">Employee asset request workflow</p>
            </div>
            
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 32px; margin-bottom: 10px;">⚖️</div>
                <h4 style="margin-bottom: 10px; color: #333;">Approval System</h4>
                <p style="font-size: 14px; color: #666;">Manager approval workflow</p>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('dashboard') }}" style="background: #2196f3; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-right: 10px;">
                ← Back to Dashboard
            </a>
            <button onclick="showComingSoonAlert()" style="background: #4caf50; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer;">
                🔔 Get Notified
            </button>
        </div>
    </div>
</div>

<script>
function showComingSoonAlert() {
    alert('🚀 Asset Management System Coming Soon!\n\nFeatures will include:\n• Asset tracking & inventory\n• Employee request system\n• Manager approval workflow\n• Stock level monitoring\n• Asset lifecycle management');
}
</script>
@endsection