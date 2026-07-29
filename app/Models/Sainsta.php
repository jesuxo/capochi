<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sainsta extends Model
{
    use HasFactory;
    protected $table    = 'sainsta';
    protected $fillable = ['codinst', 'descrip', 'insPadre', 'nivel', 'tipoIns', 'DEsComp', 'codalte', 'desseri'];

    public function padre()
    {
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sainsta::class, 'codinst', 'insPadre')->where('comercial',$comercial);
    }

    public function hijos  ()
    {
        $comercial = session('comercialid') ;
        return $this->hasMany(Sainsta::class, 'insPadre', 'id')->where('comercial',$comercial);
    }

    public function productos  ()
    {
        $comercial = session('comercialid') ;
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')->where('comercial',$comercial);
    }

    public function productosexistencias  ()
    {
        $comercial = session('comercialid') ;
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')
            ->where('saprod.existen', '<>', 0)
            ->where('saprod.comercial',$comercial);
    }

    public function servicios  ()
    {
        return $this->hasMany(Saserv::class, 'codinst', 'codinst');
    }

    public function comercial  ()
    {
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id')->where('sainsta.comercial',$comercial);
    }

    public function scopePorComercial($query, $comercialId)
    {
        return $query->where('comercial', $comercialId);
    }

    // Scope para instancias de nivel 1 (departamentos principales)
    public function scopeNivel1($query)
    {
        $comercial = session('comercialid') ;
        return $query->where('nivel', 1)->where('comercial',$comercial);
    }

}
