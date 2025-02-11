<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paid_service_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('paid_services')->onDelete('cascade');
            $table->integer('duration'); // Müddət (gün və ya dəfə)
            $table->string('duration_type'); // 'day' və ya 'times'
            $table->decimal('price', 8, 2); // Qiymət
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_service_options');
    }
};
