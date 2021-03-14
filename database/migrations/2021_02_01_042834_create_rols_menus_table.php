<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolsMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlsrv')->create('rols_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('rols');
            $table->foreignId('menu_id')->constrained('menus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv')->dropIfExists('rols_menus');
    }
}
