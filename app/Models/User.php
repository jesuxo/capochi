<?php
// app/Models/User.php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'type', 'email', 'avatar', 'password'
    ];

    /**
     * Relación con las sucursales
     */
    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(
            Sasucursal::class,
            'usersucursal',
            'fk_user',
            'fk_sucursal'
        )->withTimestamps();
    }

    /**
     * Obtener todas las sucursales del usuario (sin filtrar por comercial)
     */
    public function getTodasSucursales()
    {
        return $this->sucursales()->orderBy('id')->get();
    }

    /**
     * Obtener las sucursales del usuario filtradas por la comercial actual
     */
    public function getSucursalesComercialActual()
    {
        $comercialId = session('comercialid');

        if (!$comercialId) {
            // Si no hay comercial en sesión, obtener la primera sucursal
            $primeraSucursal = $this->sucursales()->orderBy('id')->get();
            if ($primeraSucursal && $primeraSucursal->fk_comercial) {
                $comercialId = $primeraSucursal->fk_comercial;
                Session::put('comercialid', $comercialId);
                Session::put('comercial_actual', $primeraSucursal->comercial);
            } else {
                return collect(); // Retorna colección vacía
            }
        }

        $sucus  = $this->sucursales()
            ->where('fk_comercial', $comercialId)
            ->orderBy('id')
            ->get();

        return $sucus;
    }

    /**
     * Obtener la primera sucursal del comercial actual
     */
    public function getPrimeraSucursalComercialActual()
    {
        return $this->getSucursalesComercialActual()->first();
    }

    /**
     * Obtener los IDs de las sucursales del comercial actual
     */
    public function getSucursalesIdsComercialActual()
    {
        return $this->getSucursalesComercialActual()->pluck('id')->toArray();
    }

    /**
     * Verificar si una sucursal pertenece al comercial actual del usuario
     */
    public function sucursalPerteneceComercialActual($sucursalId)
    {
        $sucursalesIds = $this->getSucursalesIdsComercialActual();
        return in_array($sucursalId, $sucursalesIds);
    }

    /**
     * Obtener la comercial actual del usuario
     */
    public function getComercialActual()
    {
        // Primero intentar obtener de la sesión
        $comercial = Session::get('comercial_actual');

        if ($comercial) {
            return $comercial;
        }

        // Si no está en sesión, obtener de la primera sucursal
        $primeraSucursal = $this->sucursales()->with('comercial')->orderBy('id')->first();

        if ($primeraSucursal && $primeraSucursal->comercial) {
            Session::put('comercial_actual', $primeraSucursal->comercial);
            Session::put('comercialid', $primeraSucursal->comercial->id);
            return $primeraSucursal->comercial;
        }

        return null;
    }

    /**
     * Obtener los comerciales a los que el usuario tiene acceso
     * a través de sus sucursales asignadas
     */
    public function getComercialesAcceso()
    {
        // Obtener todas las sucursales del usuario con sus comerciales
        $sucursales = $this->sucursales()->with('comercial')->get();

        // Extraer comerciales únicos (evitar duplicados)
        $comerciales = collect();

        foreach ($sucursales as $sucursal) {
            if ($sucursal->comercial && !$comerciales->contains('id', $sucursal->comercial->id)) {
                $comerciales->push($sucursal->comercial);
            }
        }

        // Ordenar por ID
        return $comerciales->sortBy('id');
    }

    /**
     * Verificar si el usuario tiene acceso a un comercial específico
     */
    public function tieneAccesoAComercial($comercialId)
    {
        return $this->sucursales()
            ->where('fk_comercial', $comercialId)
            ->exists();
    }

    /**
     * Obtener el comercial principal (primera sucursal ordenada por ID)
     */
    public function getComercialPrincipal()
    {
        $sucursal = $this->sucursales()->with('comercial')->orderBy('id')->first();

        if ($sucursal && $sucursal->comercial) {
            return $sucursal->comercial;
        }

        return null;
    }

    /**
     * Verificar si el usuario tiene sucursales asignadas
     */
    public function tieneSucursalesAsignadas()
    {
        return $this->sucursales()->exists();
    }

    /**
     * Cambiar a una comercial específica
     */
    public function cambiarComercial($comercialId)
    {
        if (!$this->tieneAccesoAComercial($comercialId)) {
            return false;
        }

        $comercial = Sacomercial::find($comercialId);

        if ($comercial) {
            Session::put('comercial_actual', $comercial);
            Session::put('comercialid', $comercial->id);
            return true;
        }

        return false;
    }
}
