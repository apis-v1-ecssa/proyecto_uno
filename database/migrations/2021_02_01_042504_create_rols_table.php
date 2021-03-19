<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlsrv')->create('rols', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25)->unique();
            $table->softDeletes('deleted_at', 3);
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
        Schema::connection('sqlsrv')->dropIfExists('rols');
    }
}
