<?php

namespace App\Http\Controllers;

use App\Models\Sainsta;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SainstaController extends Controller
{
    public function index()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $instanciaspadre = Sainsta::where('comercial',$comercial)
            ->selectRaw(" CONCAT (repeat('&nbsp;',(Nivel)*4) , ' ' ,  Descrip) as label, descrip, id ")->orderBy('codalte')->get();
        return view('sub-categories',compact('instanciaspadre'));
    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $all = Sainsta::where('comercial',$comercial)->with(['padre','hijos', 'productos','servicios'])->orderBy('descrip','asc')->get();
        $aux = [];
        $instancias = [];

        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "hijos"         => (isset($item->hijos) and count($item->hijos) > 0)? 1: 0,
                "productos"     => (isset($item->productos) and count($item->productos) > 0)? 1: 0,
                "servicios"     => (isset($item->servicios) and count($item->servicios) > 0)? 1: 0,
                "subcategory"   => "$item->descrip",
                "category"      => (isset($item->padre) and isset($item->padre->id))? $item->padre->descrip : ''
            ];

            array_push($instancias,$aux);
        }
        return response()->json($instancias );
    }
    public function lastprod($codinst)
    {
        $last   = '';
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $product = Saprod::where('comercial',$comercial)->orderBy('id','desc')->first();

        if(isset($product) and $product->codprod != '')
            $last = $product->codprod;

        return response()->json(['last' => $last ]);
    }

    public function list(Request $request)
    {
        $comercial  = $request->comercial;
        $comercial  = str_replace("4000","",$comercial);
        if(!$comercial or $comercial==0){
            $fk_sucursal = $request->sucursal;
            $fk_sucursal = str_replace("300","",$fk_sucursal);
            $sucursal    = Sasucursal::find($fk_sucursal);
            $comercial   = $sucursal->fk_comercial;
        }
        $instancias = Sainsta::where('comercial',$comercial)->orderBy('codinst','desc')->get();
        return response()->json(['success'=>'success', 'instancias' => $instancias], 200);
    }

    public function listComercial(Request $request)
    {
        $comercialid = str_replace("700","",$request->comercial);
        $instancias = Sainsta::where('comercial',$comercialid)->orderBy('codinst','desc')->get();
        return response()->json(['success'=>'success', 'instancias' => $instancias], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $insPadre = $request->insPadre;
        $nivel    = 1;
        $padreid  = 0;
        $codalte  = '';
        if($insPadre){
            $padre   = Sainsta::where(['comercial' => $comercial, 'descrip' => $insPadre])->first();
            if(isset($padre)){
                $nivel   = $padre->nivel + 1;
                $padreid = $padre->codinst;
                $codalte = $padre->codalte;
            }
        }
        $new = new Sainsta();
        $new->insPadre = $padreid;
        $new->codinst  = 0;
        $new->codalte  = '';
        $new->comercial = $comercial;
        $new->descrip  = strtoupper($request->descrip);
        $new->nivel    = $nivel;
        $new->save();

        $new->codinst  = $new->id;
        if(!$codalte)
            $new->codalte = $new->id;
        else
            $new->codalte = "$codalte".$new->id;
        $new->save();
        return response()->json(['id'=>$new->id]);
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
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $sainsta    = Sainsta::find($id);
        $codaltepadre = '';

        if($request->descrip != $request->insPadre){

            $insPadre = $request->insPadre;
            $nivel    = $sainsta->nivel;
            $padreid  = $sainsta->insPadre;

            if($insPadre){
                $padre   = Sainsta::where(['comercial'=>$comercial, 'descrip' => $insPadre])->first();
                if(isset($padre)){
                    $nivel        = $padre->nivel + 1;
                    $padreid      = $padre->codinst;
                    $codaltepadre = $padre->codalte;
                }
            }

            $sainsta->insPadre  = $padreid;
            $sainsta->descrip   = strtoupper($request->descrip);
            $sainsta->nivel     = $nivel;
            $sainsta->comercial = $comercial;
            if($codaltepadre)
                $sainsta->codalte = "$codaltepadre".$sainsta->id.".";

            $sainsta->save();

        }
        return response()->json(['id'=>$sainsta->id]);
    }

    public function destroy($id)
    {
        $sainsta = Sainsta::find($id);
        $sainsta->delete();

    }
}
