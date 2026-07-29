<?php

namespace App\Http\Controllers;

use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SasucursalController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $comercial  = $request->comercial;
        $comercial  = str_replace("4000","",$comercial);

        $new = new Sasucursal();
        $new->fill($request->all());
        $new->fk_comercial = $comercial;
        $new->save();
        $lastid = $new->id;
        return response()->json(['id' => $lastid]);
    }

    public function show(Sasucursal $sasucursal)
    {
        //
    }

    public function edit(Sasucursal $sasucursal)
    {
        //
    }

    public function update(Request $request, Sasucursal $sasucursal)
    {
        //
    }

    public function destroy(Sasucursal $sasucursal)
    {
        //
    }
}
