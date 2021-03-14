<?php

namespace App\Models\V1\Seguridad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioRol extends Model
{
    use SoftDeletes;

    //protected $dateFormat = 'Y-d-m H:i:s';

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
    protected $table = 'users_rols';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'rol_id'
    ];

    /**
     * Get the user associated with the users_rols.
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id', 'id');
    }

    /**
     * Get the rol associated with the users_rols.
     *
     * @return object
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }
}
