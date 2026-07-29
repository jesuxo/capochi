<?php
// app/View/Composers/ComercialComposer.php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ComercialComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        if ($user) {
            $comerciales_acceso = $user->getComercialesAcceso();
            $comercial_actual = session('comercial_actual');

            $view->with([
                'comerciales_acceso' => $comerciales_acceso,
                'comercial_actual' => $comercial_actual,
                'comercialid' => session('comercialid')
            ]);
        } else {
            $view->with([
                'comerciales_acceso' => collect(),
                'comercial_actual' => null,
                'comercialid' => null
            ]);
        }
    }
}
