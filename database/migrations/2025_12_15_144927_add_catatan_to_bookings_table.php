<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking', function (Blueprint $table) {
            // Tambahkan kolom 'catatan' dengan tipe TEXT setelah kolom 'jumlah_orang'
            $table->text('catatan')->nullable()->after('jumlah_orang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking', function (Blueprint $table) {
            // Hapus kolom 'catatan' jika migration di-rollback
            $table->dropColumn('catatan');
        });
    }
};