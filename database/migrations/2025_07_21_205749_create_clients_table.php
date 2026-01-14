<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_title')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['active', 'inactive', 'prospect', 'lost'])->default('prospect');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->string('lead_source')->nullable();
            $table->text('notes')->nullable();
            $table->date('acquired_date')->nullable();
            $table->date('last_contact_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};