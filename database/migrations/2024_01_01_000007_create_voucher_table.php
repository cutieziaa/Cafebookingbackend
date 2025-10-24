<?php
// database/migrations/2024_01_01_000007_create_voucher_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTable extends Migration
{
    public function up()
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->text('deskripsi');
            $table->integer('diskon_percent');
            $table->decimal('minimal_transaksi', 10, 2)->default(0);
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voucher');
    }
}