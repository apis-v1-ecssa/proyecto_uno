<?php

namespace App\Models\V1\Seguridad;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $dateFormat = 'Y-d-m H:i:s';

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
    protected $table = 'menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'route_name',
        'father',
        'hide',
        'icon'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a',
        'hide' => 'boolean',
        'father' => 'integer'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
