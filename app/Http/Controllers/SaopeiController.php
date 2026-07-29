<?php

namespace App\Http\Controllers;

use App\Models\Saitemopi;
use App\Models\Saopei;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SaopeiController extends Controller
{

    public function documento(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $operaciones = $request->operaciones;
        $operaciones = json_decode($operaciones);

        if(isset($operaciones)){
            foreach ($operaciones as $ope){

                if(isset($ope->nrounico)){
                    $record = Saopei::where(['nrounico'=>  $ope->nrounico, 'fk_sucursal'=> $sucursalid])->first();

                    if(!isset($record->id)){
                        $record = new Saopei();

                        if( isset($ope->allitems)){
                            foreach ($ope->allitems as $allitem){
                                $newitem = new Saitemopi();
                                $auxitem = (array) $allitem;
                                $newitem->fill($auxitem);
                                $newitem->TipoOpi     = $ope->TipoOpi;
                                $newitem->FechaE      = $ope->FechaE;
                                $newitem->fk_sucursal = $sucursalid ;
                                $newitem->save();
                            }
                        }

                    }else{
                        $record = Saopei::find($record->id);
                    }

                    $aux = (array) $ope;
                    $record->fill($aux) ;
                    $record->fk_sucursal = $sucursalid ;
                    $record->TipoOpi = $ope->TipoOpi;
                    $record->save();
                }
            }
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

}
