<?php

namespace App\Models\V1\Principal;

use App\Models\V1\Principal\Client;
use App\Models\V1\Principal\Detail;
use App\Models\V1\Principal\Status;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

class Deliverie extends Model
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
    protected $table = 'deliveries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'docto_no',
        'seller',
        'description',
        'delivery_time',
        'status_id',
        'client_id',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'delivery_time' => 'datetime:d/m/Y h:i:s a'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['delivery_time'];

    /**
     * Get the status associated with the deliveries.
     *
     * @return object
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    /**
     * Get the client associated with the deliveries.
     *
     * @return object
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * Get the user associated with the deliveries.
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id', 'id');
    }

    /**
     * Get the detail associated with the deliveries.
     *
     * @return array
     */
    public function detail()
    {
        return $this->hasMany(Detail::class, 'deliverie_id', 'id');
    }
}
