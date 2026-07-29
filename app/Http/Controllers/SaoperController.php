<?php

namespace App\Http\Controllers;

use App\Models\Saoper;
use App\Models\Sasucursal;
use App\Models\Satarj;
use App\Models\Savend;
use Illuminate\Http\Request;

class SaoperController extends Controller
{

    public function index()
    {

    }

    public function list(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $operaciones = Saoper::where('comercial', $comercialid)->get();

        return response()->json(['success'=>'success', 'operaciones' => $operaciones], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {


    }


    public function json()
    {

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
