<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saeprdday extends Model
{
    use HasFactory;
    protected $table    = 'saeprdday';
    protected $fillable = ['fksucursal', 'codprod', 'existen',
                           'fecha', 'preciod', 'costod' ];

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fksucursal', 'id');
    }

    public function producto  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod')->where('comercial',$comercial);
    }

}
