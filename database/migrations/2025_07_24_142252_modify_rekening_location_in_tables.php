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
        Schema::table('penerima', function (Blueprint $table) {
            $table->string('no_rek')->nullable()->after('nama_penerima');
        });

        Schema::table('sp2d', function (Blueprint $table) {
            $table->dropColumn('no_rek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logika untuk membatalkan (rollback) migrasi
        Schema::table('penerima', function (Blueprint $table) {
            $table->dropColumn('no_rek');
        });

        Schema::table('sp2d', function (Blueprint $table) {
            $table->string('no_rek')->nullable(); // Tambahkan kembali kolomnya
        });
    }
};
