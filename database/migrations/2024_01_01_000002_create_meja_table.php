<?php
// database/migrations/2024_01_01_000002_create_meja_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipe_id')->constrained('meja_tipe')->onDelete('cascade');
            $table->string('kode_meja', 50)->unique();
            $table->integer('kapasitas');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meja');
    }
};