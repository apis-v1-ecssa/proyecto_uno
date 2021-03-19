<?php


use App\Models\V1\Seguridad\Usuario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlsrv')->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 15)->unique();
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('admin')->default(Usuario::USUARIO_REGULAR);
            $table->string('photo', 100)->nullable(); //Guardaremos la imagen en el local storage
            $table->string('email')->unique();
            $table->string('password');
            $table->string('observation', 500)->nullable();
            $table->string('ubication', 100)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('area_code', 15)->nullable();
            $table->string('country', 75)->nullable();
            $table->string('url', 100)->nullable();

            $table->foreignId('departament_id')->constrained('departaments');
            $table->foreignId('municipality_id')->constrained('municipalities');

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
        Schema::connection('sqlsrv')->dropIfExists('users');
    }
}
