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
        Schema::create('announcement_supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->unsignedBigInteger('supply_id');
            $table->foreign('announcement_id')->references('id')->on('announcements')->cascadeOnDelete();
            $table->foreign('supply_id')->references('id')->on('supplies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_supplies');
    }
};
