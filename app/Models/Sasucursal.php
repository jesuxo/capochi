<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sasucursal extends Model
{
    use HasFactory;

    protected $table    = 'sasucursal';
    protected $fillable = [ 'descrip', 'direccion'];

    protected $casts = [
        'dia_descanso' => 'integer',
    ];

    public function saprodsucursales (){
        return $this->hasMany(Saprodsucursal::class, 'fk_sucursal', 'id');
    }

    public function comercial (){
        return $this->belongsTo(Sacomercial::class, 'fk_comercial', 'id');
    }

    public function sacliesucursales (){
        return $this->hasMany(Sacliesucursal::class, 'fk_sucursal', 'id');
    }

    public function tarjetas (){
        $comercial = session('comercialid') ;
        return $this->hasMany(Satarj::class, 'fk_sucursal', 'id')->where('comercial',$comercial);
    }

    public function saprovsucursales (){
        return $this->hasMany(Saprovsucursal::class, 'fk_sucursal', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'usersucursal',
            'fk_sucursal',
            'fk_user'
        )->withTimestamps();
    }

    public function esDiaDescanso($fecha)
    {
        if ($this->dia_descanso === null) {
            // Si no tiene definido, asumir Domingo como descanso
            return Carbon::parse($fecha)->dayOfWeek === 0;
        }
        return Carbon::parse($fecha)->dayOfWeek === $this->dia_descanso;
    }
}
