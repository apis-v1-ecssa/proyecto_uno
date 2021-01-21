<?php

namespace App\Model\WEB;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $connection = 'sqlsrv_web'; //Conexión
    protected $table = 'PRODUCTOSWEB'; //Nombre de la vista
}
