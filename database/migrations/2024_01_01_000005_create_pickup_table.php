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

            // menu yang dipesan (optional, jika kamu support pre-order 1 menu saja)
            $table->foreignId('menu_id')->nullable()->constrained('menu')->nullOnDelete();

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
