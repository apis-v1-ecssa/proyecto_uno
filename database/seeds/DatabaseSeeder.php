<?php

use App\Imports\DataPrueba;
use App\Imports\RolMenuImport;
use Illuminate\Database\Seeder;
use App\Imports\MunicipioImport;
use Illuminate\Support\Facades\DB;
use App\Imports\DepartamentoImport;
use App\Models\V1\Principal\Status;
use App\Models\V1\Seguridad\Usuario;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\V1\Seguridad\UsuarioRol;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Migrando data para los roles, menus y asociando menu al rol correspondiente
        Excel::import(new RolMenuImport, 'database/seeds/Catalogos/RolMenu.xlsx');

        //Migrando Departamento y Municipios asociados
        Excel::import(new DepartamentoImport, 'database/seeds/Catalogos/Departamentos.xlsx');
        Excel::import(new MunicipioImport, 'database/seeds/Catalogos/Municipios.xlsx');

        factory(Usuario::class, 1)->create();

        UsuarioRol::create(
            [
                'user_id' => 1,
                'rol_id' => 1
            ]
        );

        Status::create(
            [
                'name' => 'FACTURADO',
                'color' => '#ff5252',
                'description' => 'En este estado el producto se encuentra listo para iniciar la busqueda de productos en bodega.'
            ]
        );

        Status::create(
            [
                'name' => 'EN PROCESO',
                'color' => '#ffcb52',
                'description' => 'En este estado el está siendo buscado en bodega.'
            ]
        );

        Status::create(
            [
                'name' => 'ENTREGADO',
                'color' => '#36c3c7',
                'description' => 'En este estado el producto fue entregado pero no en totalidad.'
            ]
        );

        Status::create(
            [
                'name' => 'COMPLETO',
                'color' => '#50c736',
                'description' => 'En este estado el producto fue entregado completo.'
            ]
        );

        Status::create(
            [
                'name' => 'CANCELADO',
                'color' => '#7e817d',
                'description' => 'En este estado el producto fue anulado para la entrega.'
            ]
        );

        Artisan::call('storage:link');
        Artisan::call('passport:install');
        //fb8QYHjGOBKEL4S8CMDNkXW44lSHGmM6lrvoxwO1

        DB::table('oauth_clients')
            ->where('id', 2)
            ->update(['secret' => 'fb8QYHjGOBKEL4S8CMDNkXW44lSHGmM6lrvoxwO1']);

        Excel::import(new DataPrueba, 'database/seeds/Catalogos/Prueba.xlsx');
    }
}
