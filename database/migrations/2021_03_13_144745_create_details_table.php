<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlsrv')->create('details', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 25);
            $table->integer('amount');
            $table->integer('found')->default(0);
            $table->string('description', 1000);
            $table->string('observation', 500)->nullable();
            $table->dateTime('delivery_time', 3)->nullable();

            $table->foreignId('status_id')->constrained('status');
            $table->foreignId('deliverie_id')->constrained('deliveries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv')->dropIfExists('details');
    }
}
