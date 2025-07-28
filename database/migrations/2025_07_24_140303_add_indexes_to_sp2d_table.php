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
        Schema::table('sp2d', function (Blueprint $table) {
        // Tambahkan indeks pada kolom yang sering dicari
            $table->index('jenis_sp2d');
            $table->index('tanggal_sp2d');
            $table->index('created_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sp2d', function (Blueprint $table) {
            // Opsi untuk menghapus indeks jika migrasi di-rollback
            $table->dropIndex(['jenis_sp2d']);
            $table->dropIndex(['tanggal_sp2d']);
            $table->dropIndex(['created_at']);
        });
    }
};
