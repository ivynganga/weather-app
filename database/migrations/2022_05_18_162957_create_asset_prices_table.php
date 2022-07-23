<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_price', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->references('id')->on('asset');
            $table->decimal('amount_usd', 12,4)->nullable();
            $table->decimal('amount_kes', 12,4)->nullable();
            $table->decimal('rate', 10, 4)->nullable();
            $table->dateTime('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_price');
    }
}
