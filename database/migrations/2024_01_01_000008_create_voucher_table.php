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
            $table->string('nama')->nullable(); // Tambah nama voucher
            $table->text('deskripsi')->nullable(); // Tambah deskripsi
            
            $table->enum('tipe_diskon', ['persen', 'nominal'])->default('persen');
            $table->integer('diskon_persen')->nullable();
            $table->decimal('diskon_nominal', 10, 2)->nullable();
            $table->decimal('maksimum_diskon', 10, 2)->nullable(); // Batas maksimal diskon
            
            $table->decimal('minimum_order', 10, 2)->default(0);
            $table->integer('limit_penggunaan')->default(1);
            $table->integer('penggunaan_sekarang')->default(0); // Track penggunaan
            
            $table->enum('status', ['aktif', 'nonaktif', 'habis'])->default('aktif');
            $table->date('tanggal_mulai')->nullable(); // Tanggal mulai berlaku
            $table->dateTime('expired_at')->nullable();
            
            $table->boolean('hanya_untuk_user_tertentu')->default(false);
            $table->json('user_ids')->nullable(); // Jika hanya untuk user tertentu
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
}