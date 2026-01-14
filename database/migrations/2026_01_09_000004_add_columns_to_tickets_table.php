<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('ticket_id')->nullable()->after('id');
            $table->boolean('mobile_created')->default(false)->after('ticket_id');
            $table->string('offline_sync_id')->nullable()->after('mobile_created');
            $table->foreignId('technician_id')->nullable()->constrained('employees')->onDelete('set null')->after('offline_sync_id');
            $table->foreignId('pos_terminal_id')->nullable()->constrained('pos_terminals')->onDelete('set null')->after('technician_id');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null')->after('pos_terminal_id');
            $table->foreignId('visit_id')->nullable()->constrained('visits')->onDelete('set null')->after('client_id');
            $table->string('issue_type')->nullable()->after('visit_id');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('issue_type');
            $table->integer('estimated_resolution_time')->nullable()->after('priority');
            $table->string('title')->nullable()->after('estimated_resolution_time');
            $table->text('description')->nullable()->after('title');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open')->after('description');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null')->after('status');
            $table->text('resolution')->nullable()->after('assigned_to');
            $table->json('attachments')->nullable()->after('resolution');
            $table->timestamp('resolved_at')->nullable()->after('attachments');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_id', 'mobile_created', 'offline_sync_id', 'technician_id',
                'pos_terminal_id', 'client_id', 'visit_id', 'issue_type', 'priority',
                'estimated_resolution_time', 'title', 'description', 'status',
                'assigned_to', 'resolution', 'attachments', 'resolved_at'
            ]);
        });
    }
};
