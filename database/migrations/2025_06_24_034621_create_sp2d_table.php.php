<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sp2d', function (Blueprint $table) {
            $table->id();

            // Info SP2D
            $table->string('nomor_sp2d')->unique();
            $table->date('tanggal_sp2d');
            $table->enum('jenis_sp2d', ['GU', 'UP', 'LS', 'TU', 'gaji', 'PFK']);
            $table->string('keterangan')->nullable();

            // Relasi
            $table->unsignedBigInteger('id_instansi');
            $table->unsignedBigInteger('id_penerima');
            $table->unsignedBigInteger('id_user')->nullable();

            // Relasi foreign key
            $table->foreign('id_instansi')->references('id')->on('instansi')->onDelete('cascade');
            $table->foreign('id_penerima')->references('id')->on('penerima')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->nullable();

            // Keuangan
            $table->string('no_rek');
            $table->decimal('brutto', 20, 2);
            $table->decimal('ppn', 20, 2)->default(0)->nullable();
            $table->decimal('pph_21', 20, 2)->default(0)->nullable();
            $table->decimal('pph_22', 20, 2)->default(0)->nullable();
            $table->decimal('pph_23', 20, 2)->default(0)->nullable();
            $table->decimal('pph_4', 20, 2)->default(0)->nullable();
            $table->string('no_bg', 20, 0)->unique();
            $table->timestamp('waktu_sesuai')->nullable();

            $table->timestamps(); // created_at, updated_at
        });

        // Add generated column after table creation
        DB::statement("
            ALTER TABLE `sp2d` 
            ADD COLUMN `netto` DECIMAL(20, 2) 
            GENERATED ALWAYS AS (brutto - (IFNULL(ppn, 0) + IFNULL(pph_21, 0) + IFNULL(pph_22, 0) + IFNULL(pph_23, 0) + IFNULL(pph_4, 0))) STORED
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp2d');
    }
};
