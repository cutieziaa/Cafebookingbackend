<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup', function (Blueprint $table) {
            $table->id();

            // siapa yang pesan
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // menghubungkan pickup ke order
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->string('nama_penerima')->nullable();
            $table->string('catatan')->nullable();

            $table->enum('status', [
                'pending',
                'processing',
                'ready',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup');
    }
};
