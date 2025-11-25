<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // dine in
            $table->foreignId('booking_id')->nullable()->constrained('booking')->nullOnDelete();

            // pickup
            $table->foreignId('pickup_id')->nullable()->constrained('pickup')->nullOnDelete();

            // user
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // data pembayaran
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('jenis_order', ['dine_in', 'pickup'])->default('pickup');

            $table->enum('status', ['pending', 'paid', 'cancelled'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
