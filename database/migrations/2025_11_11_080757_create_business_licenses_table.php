<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_licenses', function (Blueprint $table) {
            $table->id();

            // Basic license information
            $table->string('license_name');
            $table->string('license_number')->unique()->nullable();
            $table->string('license_type');
            $table->string('issuing_authority')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->enum('status', ['active', 'expired', 'pending_renewal', 'suspended', 'cancelled', 'under_review'])->default('active');

            // Financial information
            $table->decimal('cost', 12, 2)->nullable();
            $table->decimal('renewal_cost', 12, 2)->nullable();

            // Organization details
            $table->string('location')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('responsible_employee_id')->nullable();

            // Additional details
            $table->text('description')->nullable();
            $table->text('compliance_notes')->nullable();
            $table->string('document_path')->nullable();
            $table->integer('renewal_reminder_days')->default(15);
            $table->boolean('auto_renewal')->default(false);
            $table->enum('priority_level', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->text('business_impact')->nullable();
            $table->string('regulatory_body')->nullable();
            $table->text('license_conditions')->nullable();

            // License direction (company-held vs customer-issued)
            $table->enum('license_direction', ['company_held', 'customer_issued'])->default('company_held')->index();

            // Customer license fields (when license_direction = customer_issued)
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_company')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->decimal('revenue_amount', 12, 2)->nullable();
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'annually', 'one_time'])->nullable();
            $table->text('license_terms')->nullable();
            $table->string('usage_limit')->nullable();
            $table->enum('support_level', ['basic', 'standard', 'premium', 'enterprise'])->nullable();
            $table->string('customer_reference')->nullable();
            $table->date('service_start_date')->nullable();
            $table->integer('license_quantity')->nullable();
            $table->boolean('auto_renewal_customer')->default(false);

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            if (Schema::hasTable('departments')) {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            }
            if (Schema::hasTable('employees')) {
                $table->foreign('responsible_employee_id')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('employees')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('employees')->onDelete('set null');
            }

            // Indexes for performance
            $table->index('status');
            $table->index('expiry_date');
            $table->index('license_type');
            $table->index('priority_level');
            $table->index(['license_direction', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_licenses');
    }
};
