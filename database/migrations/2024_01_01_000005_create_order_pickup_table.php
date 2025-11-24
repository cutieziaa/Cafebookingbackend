<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupTable extends Migration
{
    public function up(): void
    {
        Schema::create('pickup', function (Blueprint $table) {
            $table->id();

            // pelanggan yang pesan pickup
            $table->unsignedBigInteger('user_id');

            // nama penerima (opsional jika diambil orang lain)
            $table->string('nama_penerima')->nullable();

            // catatan pesanan
            $table->string('catatan')->nullable();

            // status pickup
            $table->enum('status', [
                'pending',       // baru pesan
                'processing',    // sedang dibuat
                'ready',         // siap diambil
                'completed',     // sudah diambil
                'cancelled'
            ])->default('pending');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup');
    }
}
