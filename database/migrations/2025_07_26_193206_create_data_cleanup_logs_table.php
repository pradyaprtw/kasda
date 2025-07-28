<?php

// database/migrations/xxxx_xx_xx_create_data_cleanup_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataCleanupLogsTable extends Migration
{
    public function up()
    {
        Schema::create('data_cleanup_logs', function (Blueprint $table) {
            $table->id();
            $table->date('deleted_before'); // tanggal sampai mana data dihapus
            $table->string('deleted_by');
            $table->timestamp('deleted_at')->useCurrent(); // waktu event dijalankan

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_cleanup_logs');
    }
}
