<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitacoraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->string('table', 50);
            $table->longText('info');

            $table->foreignId('user_id')->constrained('users')->default(1);
            $table->foreignId('deliverie_id')->constrained('deliveries');
            $table->unsignedBigInteger('detail_id');

            $table->foreignId('deliverie_status_id')->constrained('status');
            $table->unsignedBigInteger('detail_status_id');

            $table->timestamps(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitacora');
    }
}
