<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('booking_id')->nullable(); // dine-in
            $table->unsignedBigInteger('pickup_id')->nullable();  // takeaway
            $table->unsignedBigInteger('user_id');

            $table->decimal('total', 10, 2)->default(0);

            $table->enum('jenis_order', ['dine_in', 'pickup'])->default('pickup');

            $table->enum('status', ['pending', 'paid', 'cancelled'])
                  ->default('pending');

            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('booking')->nullOnDelete();
            $table->foreign('pickup_id')->references('id')->on('pickup')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
