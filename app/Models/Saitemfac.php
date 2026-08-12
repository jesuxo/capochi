<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saitemfac extends Model
{
    use HasFactory;
    protected $table    = 'saitemfac';
    protected $fillable = ['codclie', 'TipoFac', 'esserv', 'NumeroD', 'NroLinea', 'NroLineaC', 'Signo', 'FechaE', 'CodItem', 'Refere', 'DEsComp', 'CodUbic', 'OTipo',
        'ONumero', 'Descrip1', 'Descrip2', 'Descrip3', 'Cantidad', 'CantMayor', 'Costo', 'TotalItem', 'Precio', 'PriceO', 'MtoTax',
        'CodVend', 'NroUnicoL', 'ExistAntU', 'ExistAnt', 'FechaL', 'FechaV', 'esfiscal', 'preciod', 'costod',  'EsUnid', 'basecostod',
        'costodoriginal','fk_sucursal' ];

    protected $casts = [
        'Cantidad' => 'float',
        'preciod'  => 'float',
        'costod'   => 'float',
    ];

    public function factura  (){
        return $this->belongsTo(Safact::class, 'NumeroD', 'NumeroD')->whereTipofac('safact.tipofac')->whereNumerod('safact.NumeroD');
    }

    public function producto  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'CodItem', 'codprod')->where('comercial',$comercial);
    }

    public function sucursal  (){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }
}
