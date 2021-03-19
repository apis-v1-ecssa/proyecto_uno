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

        $insert = new Usuario();
        $insert->cui = '0000000000000';
        $insert->name = 'Sistema';
        $insert->surname = 'SAP';
        $insert->email = 'sap@demo.com';
        $insert->password = 'admin345';
        $insert->departament_id = 1;
        $insert->municipality_id = 1;
        $insert->save();

        factory(Usuario::class, 500)->create();
        factory(UsuarioRol::class, 1500)->create();

        UsuarioRol::where('user_id', 1)->delete();

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
                'name' => 'ACEPTADO',
                'color' => '#50c736',
                'description' => 'El producto fue aceptado en conformidad.'
            ]
        );

        Status::create(
            [
                'name' => 'COMPLETO',
                'color' => '#36c3c7',
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

        DB::table('users')
            ->where('id', 2)
            ->update(['cui' => '2342008040608']);

        

        Excel::import(new DataPrueba, 'database/seeds/Catalogos/Prueba.xlsx');
    }
}
