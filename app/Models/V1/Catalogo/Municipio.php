<?php

namespace App\Models\V1\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
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
    protected $table = 'municipalities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'departament_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d/m/Y h:i:s a',
        'updated_at' => 'datetime:d/m/Y h:i:s a'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $departament = Departamento::find($this->departament_id)->name;
        return str_replace('  ', ' ', "{$departament}, {$this->name}");
    }

    /**
     * Get the departments associated with the municipality.
     *
     * @return object
     */
    public function departament()
    {
        return $this->hasOne(Departamento::class, 'id', 'departament_id');
    }
}
