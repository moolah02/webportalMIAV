<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_licenses', function (Blueprint $table) {
            if (!Schema::hasColumn('business_licenses', 'license_name')) {
                $table->string('license_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('business_licenses', 'license_type')) {
                $table->string('license_type')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('business_licenses', 'issuing_authority')) {
                $table->string('issuing_authority')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'compliance_notes')) {
                $table->text('compliance_notes')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'document_path')) {
                $table->string('document_path')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'renewal_reminder_days')) {
                $table->integer('renewal_reminder_days')->default(15);
            }
            if (!Schema::hasColumn('business_licenses', 'auto_renewal')) {
                $table->boolean('auto_renewal')->default(false);
            }
            if (!Schema::hasColumn('business_licenses', 'business_impact')) {
                $table->text('business_impact')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'regulatory_body')) {
                $table->string('regulatory_body')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'license_conditions')) {
                $table->text('license_conditions')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_id')) {
                $table->string('customer_id')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_company')) {
                $table->string('customer_company')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_address')) {
                $table->text('customer_address')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'revenue_amount')) {
                $table->decimal('revenue_amount', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'billing_cycle')) {
                $table->string('billing_cycle')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'license_terms')) {
                $table->text('license_terms')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'usage_limit')) {
                $table->string('usage_limit')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'support_level')) {
                $table->string('support_level')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'customer_reference')) {
                $table->string('customer_reference')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'service_start_date')) {
                $table->date('service_start_date')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'license_quantity')) {
                $table->integer('license_quantity')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'auto_renewal_customer')) {
                $table->boolean('auto_renewal_customer')->default(false);
            }
            if (!Schema::hasColumn('business_licenses', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('business_licenses', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        // No destructive rollback
    }
};
