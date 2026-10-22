<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What the technician records on the tablet when a site could not be visited,
 * or a device was found inactive, and who the matter sits with.
 *
 * Until now the contractual report worked these out from the job and the
 * previous visit, or the office typed them in afterwards. Captured at the
 * point of the visit they are first-hand, which is what clause 10 asks for.
 *
 * Mirrored onto technician_visits so the Report Builder can use them too.
 * All columns are nullable: visits recorded before this change simply have
 * nothing to say here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->boolean('site_visited')->nullable()->after('completed_at');
            $table->text('non_visit_reason')->nullable()->after('site_visited');
            $table->text('non_activation_reason')->nullable()->after('non_visit_reason');
            $table->foreignId('escalation_owner_id')->nullable()->after('non_activation_reason')
                ->constrained('employees')->nullOnDelete();
            $table->string('escalation_owner_name')->nullable()->after('escalation_owner_id');
        });

        Schema::table('technician_visits', function (Blueprint $table) {
            $table->boolean('site_visited')->nullable()->after('outcome');
            $table->text('non_visit_reason')->nullable()->after('site_visited');
            $table->text('non_activation_reason')->nullable()->after('non_visit_reason');
            $table->foreignId('escalation_owner_id')->nullable()->after('non_activation_reason')
                ->constrained('employees')->nullOnDelete();
            $table->string('escalation_owner_name')->nullable()->after('escalation_owner_id');
        });
    }

    public function down(): void
    {
        Schema::table('technician_visits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('escalation_owner_id');
            $table->dropColumn(['site_visited', 'non_visit_reason', 'non_activation_reason', 'escalation_owner_name']);
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('escalation_owner_id');
            $table->dropColumn(['site_visited', 'non_visit_reason', 'non_activation_reason', 'escalation_owner_name']);
        });
    }
};
