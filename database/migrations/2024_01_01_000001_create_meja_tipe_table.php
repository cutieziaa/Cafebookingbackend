<?php
// database/migrations/2024_01_01_000001_create_meja_tipe_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meja_tipe', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tipe', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meja_tipe');
    }
};