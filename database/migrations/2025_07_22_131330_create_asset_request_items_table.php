<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_requested');
            $table->integer('quantity_approved')->default(0);
            $table->integer('quantity_fulfilled')->default(0);
            $table->decimal('unit_price_at_request', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->text('special_requirements')->nullable();
            $table->enum('item_status', ['pending', 'approved', 'partially_approved', 'rejected', 'fulfilled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_request_items');
    }
};