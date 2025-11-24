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
            $table->unsignedBigInteger('meja_tipe_id');
            $table->integer('nomor');
            $table->boolean('tersedia')->default(true);
            $table->timestamps();

            $table->foreign('meja_tipe_id')->references('id')->on('meja_tipe')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meja');
    }
}
