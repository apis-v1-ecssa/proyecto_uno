<?php

namespace App\Models\V1\Principal;

use App\Models\V1\Principal\Client;
use App\Models\V1\Principal\Detail;
use App\Models\V1\Principal\Status;
use App\Models\V1\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'series',
        'seller',
        'description',
        'doc_date',
        'delivery_time',
        'status_id',
        'client_id',
        'firma',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'firma'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'delivery_time' => 'datetime:d/m/Y h:i:s a',
        'doc_date' => 'date:d/m/Y'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['delivery_time', 'doc_date'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['picture'];

    /**
     * Get the user's link base64 photo.
     *
     * @return string
     */
    public function getPictureAttribute()
    {
        $imagen = Storage::disk('firma')->exists($this->firma); //Preguntamos si la imagen existe creada local

        if (!$imagen) { //Si la imagen no existe
            return null;
        }

        $imagen = Storage::disk('firma')->get($this->firma); //Seleccionar la imagen
        return "data:application/jpg;base64," . base64_encode($imagen);
    }

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
