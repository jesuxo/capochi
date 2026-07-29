<?php

namespace App\Http\Controllers;

use App\Models\Sacomp;
use App\Models\Saitemcom;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SacompController extends Controller
{
    public function documento(Request $request)
    {
        $sucursalid    = str_replace("300","",$request->sucursal);
        $compras       = $request->compras;
        $compras       = json_decode($compras);
        $sucursal      = Sasucursal::find($sucursalid);
        $allsucursales = Sasucursal::where("fk_comercial", $sucursal->fk_comercial)->get();

            if(isset($compras)){
                foreach ($compras as $com){

                    if(isset($com->nrounico)){
                        $record = Sacomp::where(['nrounico'=>  $com->nrounico, 'fk_sucursal'=> $sucursalid])->first();

                        $additems = 0;
                        if(!isset($record->id)){
                            $record = new Sacomp();
                            $additems = 1;
                        }else{
                            $record = Sacomp::find($record->id);
                        }

                        $aux = (array) $com;
                        $record->fill($aux) ;
                        $record->fk_sucursal = $sucursalid ;

                        if($additems and isset($com->allitems)){
                            foreach ($com->allitems as $allitem){
                                $newitem = new Saitemcom();
                                $auxitem = (array) $allitem;
                                $newitem->fill($auxitem);
                                $newitem->fk_sucursal = $sucursalid ;
                                $newitem->save();

                                $saprod   = Saprod::where(['codprod'=>  $allitem->coditem, 'comercial'=> $sucursal->fk_comercial])->first();
                                if(isset($saprod) and isset($saprod->codprod)){
                                    $saprod->costod     = (isset($allitem->costod))?    $allitem->costod    : 0;
                                    $saprod->costod2    = (isset($allitem->costod2))?   $allitem->costod2   : 0;
                                    $saprod->costod3    = (isset($allitem->costod3))?   $allitem->costod3   : 0;
                                    $saprod->preciod    = (isset($allitem->preciod))?   $allitem->preciod   : 0;
                                    $saprod->save();
                                }
                                foreach($allsucursales as $current){
                                    $sucursalprods = Saprodsucursal::where(['codprod' => $allitem->coditem, 'fk_sucursal' => $current->id])->get();
                                    foreach ($sucursalprods as $sucursalprod) {
                                        $sucursalprod->delete();
                                    }
                                }

                            }
                        }


                        $record->save();
                    }
                }
            }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

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
        //
    }

    public function show(Sacomp $sacomp)
    {
        //
    }

    public function edit(Sacomp $sacomp)
    {
        //
    }

    public function update(Request $request, Sacomp $sacomp)
    {
        //
    }

    public function destroy(Sacomp $sacomp)
    {
        //
    }
}
