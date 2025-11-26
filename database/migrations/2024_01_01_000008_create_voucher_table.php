<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTable extends Migration
{
    public function up(): void
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();
            $table->integer('diskon_persen')->nullable();
            $table->decimal('diskon_nominal', 10, 2)->nullable();

            $table->decimal('minimum_order', 10, 2)->default(0);
            $table->integer('limit_penggunaan')->default(1);

            $table->dateTime('expired_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
}
