<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saipacxc extends Model
{
    use HasFactory;
    protected $table    = 'saipacxc';
    protected $fillable = ['fk_sucursal', 'NroPpal', 'CodPago', 'dolares','Monto','pesos', 'NroUnico', 'Descrip', 'codclie'];


    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function cxc  (){
        return $this->belongsTo(Saacxc::class, 'NroPpal', 'NroUnico')
            ->whereColumn('fk_sucursal','=','fk_sucursal');
    }

    public function satarj  (){
        return $this->belongsTo(Satarj::class, 'codpago', 'codtarj');
    }

    public function cliente  (){
        return $this->belongsTo(Saclie::class, 'codclie', 'codclie');
    }
}
