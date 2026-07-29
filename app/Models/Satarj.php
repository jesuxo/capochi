<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satarj extends Model
{
    use HasFactory;
    protected $table    = 'satarj';
    protected $fillable = ['codtarj', 'descrip', 'bs', 'dolares', 'pesos', 'multiple'];

    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }
}
