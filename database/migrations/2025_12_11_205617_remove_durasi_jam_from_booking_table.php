<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fungsi ini akan menghapus kolom 'durasi_jam'.
     */
    public function up(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropColumn('durasi_jam');
        });
    }

    /**
     * Reverse the migrations.
     * Fungsi ini akan menambahkan kembali kolom 'durasi_jam' jika kita melakukan rollback.
     */
    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->decimal('durasi_jam', 4, 2)->default(2)->after('tanggal');
        });
    }
};