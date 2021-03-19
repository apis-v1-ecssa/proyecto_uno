<?php

namespace App\Models\V1\Principal;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    const FACTURADO = 1;
    const EN_PROCESO = 2;
    const ACEPTADO = 3;
    const COMPLETO = 4;
    const CANCELADO = 5;

    public $timestamps = false;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'sqlsrv';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'description'
    ];
}
