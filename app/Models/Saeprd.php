<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saeprd extends Model
{
    use HasFactory;

    protected $table = 'saeprd';
    protected $primaryKey = ['CodProd', 'Periodo', 'fk_sucursal'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'CodProd',
        'Periodo',
        'ExInicial',
        'ExFinal',
        'preciodi',
        'preciod2i',
        'fk_sucursal'
    ];

    protected $casts = [
        'ExInicial'   => 'decimal:4',
        'ExFinal'     => 'decimal:4',
        'preciodi'    => 'decimal:4',
        'preciod2i'   => 'decimal:4',
        'fk_sucursal' => 'integer'
    ];

    /**
     * Relación con Saprod (Productos)
     */
    public function saprod()
    {
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'CodProd', 'codprod')->where('comercial',$comercial);
    }

    /**
     * Relación con Sucursal
     */
    public function sucursal()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }
}
