<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Models\V1\Seguridad\Rol;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Seguridad\UsuarioRol;
use App\Models\V1\Catalogo\Departamento;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Usuario::class, function (Faker $faker) {
    return [
        'cui' => $this->faker->unique()->numerify('#############'),
        'name' => $this->faker->randomElement([$this->faker->firstNameMale, $this->faker->firstNameFemale]),
        'surname' => $this->faker->lastName,
        'admin' => Usuario::USUARIO_REGULAR,
        'email' => $this->faker->unique()->freeEmail,
        'password' => 'admin',
        'observation' => $this->faker->randomElement([$this->faker->text($this->faker->numberBetween(100, 500)), null]),
        'ubication' => $this->faker->randomElement([$this->faker->address, null]),
        'departament_id' => Departamento::all()->random()->id,
        'municipality_id' => Municipio::all()->random()->id
    ];
});

$factory->define(UsuarioRol::class, function (Faker $faker) {
    $usuario = Usuario::all()->random()->id;
    $rol = Rol::all()->random()->id;

    if (is_null(UsuarioRol::where('user_id', $usuario)->where('rol_id', $rol)->first())) {
        return [
            'user_id' => $usuario,
            'rol_id' => $rol
        ];
    } else {
        return [];
    }
});
