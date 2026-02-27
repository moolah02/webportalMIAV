<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();         // e.g. 'system', 'mobile', 'srs'
            $table->string('title');                  // Page title shown in <h1>
            $table->string('subtitle')->nullable();   // Subtitle / description line
            $table->longText('content');              // Full HTML body content
            $table->unsignedBigInteger('last_edited_by')->nullable();
            $table->timestamps();

            $table->foreign('last_edited_by')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_pages');
    }
};
