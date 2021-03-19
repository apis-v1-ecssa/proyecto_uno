<?php

namespace App\Models\V1\Principal;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
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
    protected $table = 'bitacora';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'table',
        'info',
        'user_id',
        'deliverie_id',
        'detail_id',
        'deliverie_status_id',
        'detail_status_id'
    ];
}
