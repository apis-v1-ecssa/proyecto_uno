<?php

namespace App\Model\WEB;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $connection = 'sqlsrv_web'; //Conexión
    protected $table = 'PRECIOSWEB'; //Nombre de la vista
}
