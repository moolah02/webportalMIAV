<?php

// ==============================================
// 3. EMPLOYEES TABLE UPDATE
// Run: php artisan make:migration update_employees_table_for_onboarding
// ==============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('employees', 'employee_id')) {
                $table->string('employee_id')->unique()->after('id');
            }
            if (!Schema::hasColumn('employees', 'first_name')) {
                $table->string('first_name')->after('employee_id');
            }
            if (!Schema::hasColumn('employees', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('employees', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'department')) {
                $table->string('department')->after('phone');
            }
            if (!Schema::hasColumn('employees', 'position')) {
                $table->string('position')->after('department');
            }
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->after('position');
            }
            if (!Schema::hasColumn('employees', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('hire_date');
            }
            if (!Schema::hasColumn('employees', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->constrained('employees')->after('salary');
            }
            if (!Schema::hasColumn('employees', 'status')) {
                $table->enum('status', ['active', 'inactive', 'pending'])->default('pending')->after('manager_id');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('status');
            }
            if (!Schema::hasColumn('employees', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('employees', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('employees', 'country')) {
                $table->string('country')->nullable()->after('state');
            }
            if (!Schema::hasColumn('employees', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('country');
            }
            if (!Schema::hasColumn('employees', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('employees', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('employees', 'skills')) {
                $table->json('skills')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('employees', 'notes')) {
                $table->text('notes')->nullable()->after('skills');
            }
            if (!Schema::hasColumn('employees', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id', 'first_name', 'last_name', 'phone', 'department', 
                'position', 'hire_date', 'salary', 'manager_id', 'status',
                'address', 'city', 'state', 'country', 'postal_code',
                'emergency_contact_name', 'emergency_contact_phone', 
                'skills', 'notes', 'avatar_url'
            ]);
        });
    }
};
