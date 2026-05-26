<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_completions')) {
            return;
        }

        Schema::create('project_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->text('executive_summary')->nullable();
            $table->text('key_achievements')->nullable();
            $table->text('challenges_overcome')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->string('closure_reason')->nullable();
            $table->text('issues_found')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('additional_notes')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('completed_by')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_completions');
    }
};
