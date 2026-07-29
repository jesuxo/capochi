<?php

namespace App\Http\Controllers;

use App\Models\Savend;
use Illuminate\Http\Request;

class SavendController extends Controller
{

    public function index()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $vendedores  = Savend::where('comercial',$comercialid)->with('user')->get();
        return view('sellers-list-view', compact('vendedores') );
    }

    public function list(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $vendedores  = Savend::where('comercial',$comercialid)->with('user')->orderBy('codvend','desc')->get();
        return response()->json(['success'=>'success', 'vendedores' => $vendedores], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $newVend = new Savend();
        $newVend->fill($request->all());
        $newVend->id3 = $request->codvend;
        $newVend->comercial = $comercialid;
        $newVend->save();

        return response()->json(['success'=>'success' ]);
    }


    public function json()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $all = Savend::where('comercial',$comercialid)->orderBy('descrip','asc')->get();
        $aux = [];
        $vendedores = [];
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "codvend"       => "$item->codvend",
                "sellerName"    => "$item->descrip",
                "email"         => "$item->email",
                "phone"         => "$item->telef",
                "accountStatus" => ($item->activo)? "Active":"Inactive"
            ];
            array_push($vendedores,$aux);
        }
        return response()->json($vendedores );
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
        $vendedor = Savend::find($id);
        $vendedor->descrip = $request->sellerName;
        $vendedor->email   = $request->email;
        $vendedor->telef   = $request->phone;
        $vendedor->activo  = ($request->activo=='Active')? 1: 0;
        $vendedor->save();

        response()->json(['success'=>'success',"actializado"=>111]);
    }

    public function destroy($id)
    {
        $vendedor = Savend::find($id);
        $vendedor->delete();
        response()->json(['success'=>'success']);
    }
}
