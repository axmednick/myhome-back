<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade'); // Abunəlik ID-si
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Paketi alan user
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('set null'); // Paketi alan agentlik
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null'); // Abunəliyi dəyişən admin
            $table->string('action'); // Paket alındı, yeniləndi və s.
            $table->json('changes')->nullable(); // Yeni dəyişiklikləri saxlayırıq
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};
