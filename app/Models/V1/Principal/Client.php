<?php

namespace App\Models\V1\Principal;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
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
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_code',
        'nit',
        'name'
    ];
}
