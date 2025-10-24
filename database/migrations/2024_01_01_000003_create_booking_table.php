<?php
// database/migrations/2024_01_01_000003_create_booking_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('meja_id')->constrained('meja')->onDelete('cascade');
            $table->date('tanggal_booking');
            $table->time('waktu_mulai');
            $table->integer('durasi_minutes');
            $table->integer('jumlah_orang');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['belum_bayar', 'dp_dibayar', 'lunas'])->default('belum_bayar');
            $table->enum('booking_status', ['menunggu', 'dikonfirmasi', 'batal', 'selesai'])->default('menunggu');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};