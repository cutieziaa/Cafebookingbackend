<?php
// database/migrations/2024_01_01_000005_create_order_pickup_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPickupTable extends Migration
{
    public function up()
    {
        Schema::create('order_pickup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('users')->onDelete('cascade');
            $table->string('order_code')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->enum('pickup_type', ['dine_in', 'take_away']);
            $table->enum('order_status', ['baru', 'dikonfirmasi', 'siap_diambil', 'selesai', 'dibatalkan'])->default('baru');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_pickup');
    }
}