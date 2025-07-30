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
        Schema::create('data_cleanup_flags', function (Blueprint $table) {
            $table->id();
            $table->boolean('flag_hapus')->default(false);
            $table->integer('tahun_target');
            $table->date('tanggal_trigger');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_cleanup_flags');
    }
};
