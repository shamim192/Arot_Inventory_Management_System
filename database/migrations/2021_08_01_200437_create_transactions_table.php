<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['In', 'Out']);
            $table->enum('flag', ['Invest', 'Expense', 'Supplier Payment', 'Customer Payment', 'Transfer']);
            $table->unsignedBigInteger('flagable_id');
            $table->string('flagable_type');
            $table->unsignedBigInteger('bank_id');
            $table->dateTime('datetime');
            $table->text('note')->nullable();
            $table->decimal('amount', 10, 2);
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
        Schema::dropIfExists('transactions');
    }
}
