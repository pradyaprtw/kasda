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
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sp2d')->constrained('sp2d')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('aktivitas'); // contoh: "Input data", "Edit data", dll
            $table->index(['id_sp2d', 'id_user']); // indeks untuk mempercepat query berdasarkan sp2d dan user
            $table->timestamp('created_at')->useCurrent(); // waktu aktivitas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
