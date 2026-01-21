<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the visit_terminals table if it does not exist.
        if (!Schema::hasTable('visit_terminals')) {
            Schema::create('visit_terminals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
                // This links to pos_terminals primary key (id), not the public terminal_id string.
                $table->unsignedBigInteger('terminal_id')->nullable()->index();
                $table->string('status', 100)->default('active'); // mobile used to send "active"
                $table->string('condition', 100)->nullable();
                $table->string('serial_number', 191)->nullable();
                $table->string('terminal_model', 191)->nullable();
                $table->string('device_type', 191)->nullable();
                $table->text('comments')->nullable();
                $table->timestamps();

                $table->index(['visit_id', 'status']);
            });
        } else {
            // Table exists but may be missing columns after refactors; add safely.
            Schema::table('visit_terminals', function (Blueprint $table) {
                if (!Schema::hasColumn('visit_terminals', 'status')) {
                    $table->string('status', 100)->default('active')->after('terminal_id');
                    $table->index('status');
                }
                if (!Schema::hasColumn('visit_terminals', 'condition')) {
                    $table->string('condition', 100)->nullable()->after('status');
                }
                if (!Schema::hasColumn('visit_terminals', 'serial_number')) {
                    $table->string('serial_number', 191)->nullable()->after('condition');
                }
                if (!Schema::hasColumn('visit_terminals', 'terminal_model')) {
                    $table->string('terminal_model', 191)->nullable()->after('serial_number');
                }
                if (!Schema::hasColumn('visit_terminals', 'device_type')) {
                    $table->string('device_type', 191)->nullable()->after('terminal_model');
                }
                if (!Schema::hasColumn('visit_terminals', 'comments')) {
                    $table->text('comments')->nullable()->after('device_type');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_terminals');
    }
};

