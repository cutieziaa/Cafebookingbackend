<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_item', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('menu_id');

            $table->integer('qty');
            $table->decimal('harga', 10, 2);

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('menu_id')->references('id')->on('menu')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
}
