<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id')->nullable();
            $table->string('merchant_name')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('visit_summary')->nullable();
            $table->text('action_points')->nullable();
            $table->json('evidence')->nullable();
            $table->text('signature')->nullable();
            $table->json('other_terminals_found')->nullable();
            $table->json('terminal')->nullable();
            $table->timestamps();

            $table->index('merchant_id');
            $table->index('employee_id');
            $table->index('assignment_id');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
