<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_return_id');
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('stock_item_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('unit_quantity', 10, 3);
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('actual_quantity', 10, 3);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_return_items');
    }
}
