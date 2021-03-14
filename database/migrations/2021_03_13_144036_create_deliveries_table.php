<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlsrv')->create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('docto_no');
            $table->string('seller', 200);
            $table->string('description', 500)->nullable();
            $table->dateTime('delivery_time')->nullable();

            $table->foreignId('status_id')->constrained('status');
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('user_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv')->dropIfExists('deliveries');
    }
}
