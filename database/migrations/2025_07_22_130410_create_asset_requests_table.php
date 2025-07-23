<?php

// 2. ASSET REQUESTS MIGRATION
// Run: php artisan make:migration create_asset_requests_table
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // Auto-generated: REQ-2024-001
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'fulfilled', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->text('business_justification'); // Why they need these assets
            $table->date('needed_by_date')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->decimal('total_estimated_cost', 12, 2)->default(0);
            $table->string('department')->nullable();
            $table->string('cost_center')->nullable();
            
            // Approval workflow
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Fulfillment
            $table->foreignId('fulfilled_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('fulfilled_at')->nullable();
            $table->text('fulfillment_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('employee_id');
            $table->index('approved_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};

    