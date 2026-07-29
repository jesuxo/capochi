<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sacomp extends Model
{
    use HasFactory;
    protected $table    = 'sacomp';

    protected $fillable = ['tipocom', 'numerod', 'codprov', 'nrounico', 'nroctrol', 'numeror', 'otipo', 'onumero', '
                            numeron', 'fechat', 'fechai', 'fechae', 'fechav', 'codusua', 'codesta', 'signo', 'codoper', 'texento', 'porcret', 'credito', '
                            codubic', 'descrip', 'direc1', 'direc2', 'telef', 'id3', 'monto', 'totalprd', 'totalsrv', 'tgravable', 'fletes', 'mtotax', '
                            reteniva', 'descto1', 'descto2', 'totaldeuda', 'mtototal', 'notas1', 'notas2', 'notas3', 'notas5', 'notas8', 'fk_sucursal'];


    public function proveedor   (){
        return $this->belongsTo(Saprov::class, 'CodClie', 'CodClie');
    }

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function items     (){
        return $this->hasMany(Saitemcom::class, 'numerod', 'numerod')->whereTipocom('sacomp.tipocom');
    }
}
