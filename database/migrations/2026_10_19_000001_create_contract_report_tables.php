<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables behind the contractual service reports (weekly operational / monthly
 * executive). All additive: nothing existing is altered or dropped.
 *
 *  - terminal_transaction_stats: transaction activity per device per period.
 *    The portal has no feed of transactions, so the figures are uploaded from
 *    the file the client/processor supplies. Nothing is ever invented: a period
 *    with no uploaded row is reported as "not supplied".
 *  - report_exceptions: why a device was not visited, or not activated, in a
 *    period — recorded by the office, with the owner it is escalated to.
 *  - contract_reports: the narrative (summary, recommendations, client actions)
 *    plus a snapshot of the figures as issued, so a sent report can always be
 *    reproduced exactly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminal_transaction_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_terminal_id')->constrained('pos_terminals')->cascadeOnDelete();
            $table->string('terminal_code')->nullable();   // as written in the uploaded file
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('transactions_count')->default(0);
            $table->decimal('transactions_value', 15, 2)->nullable();
            $table->unsignedInteger('target_count')->nullable(); // per-row override of the device/client target
            $table->string('source_file')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['pos_terminal_id', 'period_start', 'period_end'], 'tts_terminal_period_unique');
            $table->index(['period_start', 'period_end'], 'tts_period_idx');
        });

        Schema::create('report_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_terminal_id')->constrained('pos_terminals')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('type', 20);           // non_visit | non_activation
            $table->string('reason_code', 50);
            $table->text('reason_note')->nullable();
            $table->foreignId('escalation_owner_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('status', 20)->default('open');   // open | resolved
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pos_terminal_id', 'period_start', 'period_end', 'type'], 'rex_terminal_period_type_unique');
            $table->index(['period_start', 'period_end'], 'rex_period_idx');
        });

        Schema::create('contract_reports', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);           // weekly | monthly
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('status', 20)->default('draft');  // draft | issued
            $table->text('executive_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('client_actions')->nullable();
            $table->json('payload')->nullable();  // figures exactly as issued
            $table->foreignId('prepared_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'period_start', 'period_end', 'client_id'], 'cr_period_client_idx');
        });

        // Transaction targets. Resolution order at report time:
        // uploaded row target -> device target -> client target -> system default.
        Schema::table('pos_terminals', function (Blueprint $table) {
            $table->unsignedInteger('monthly_transaction_target')->nullable()->after('contract_details');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedInteger('monthly_transaction_target')->nullable()->after('contract_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('monthly_transaction_target');
        });

        Schema::table('pos_terminals', function (Blueprint $table) {
            $table->dropColumn('monthly_transaction_target');
        });

        Schema::dropIfExists('contract_reports');
        Schema::dropIfExists('report_exceptions');
        Schema::dropIfExists('terminal_transaction_stats');
    }
};
