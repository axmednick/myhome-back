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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // User varsa
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('cascade'); // Agency varsa
            $table->foreignId('package_id')->constrained()->onDelete('cascade'); // Paketin ID-si
            $table->dateTime('start_date'); // Paketin başlama tarixi
            $table->dateTime('end_date'); // Paketin bitmə tarixi
            $table->boolean('is_active')->default(true); // Paket aktivdirsə
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
