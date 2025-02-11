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
        Schema::create('announcement_boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->onDelete('cascade');
            $table->integer('total_boosts'); // Ümumi neçə dəfə irəli çəkilməlidir
            $table->integer('remaining_boosts'); // Neçə dəfə qalıb
            $table->timestamp('last_boosted_at')->nullable(); // Sonuncu dəfə nə vaxt irəli çəkilib
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_boosts');
    }
};
