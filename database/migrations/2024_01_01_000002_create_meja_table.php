<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMejaTable extends Migration
{
    public function up(): void
    {
        Schema::create('meja', function (Blueprint $table) {
            $table->id();

            // relasi tipe meja
            $table->foreignId('meja_tipe_id')
                  ->constrained('meja_tipe')
                  ->cascadeOnDelete();

            // FORMAT NOMOR MEJA: A01, B12, C07
            $table->string('nomor', 10);

            // apakah meja sedang tersedia
            $table->boolean('tersedia')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meja');
    }
}
