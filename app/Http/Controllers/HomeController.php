<?php

namespace App\Http\Controllers;

use App\Models\Saacxc;
use App\Models\Safact;
use App\Models\Sainsta;
use App\Models\Saipavta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function reporteventa(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')->get();

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->whereRaw("nivel=1 AND insPadre=0 AND tipoins=0 and comercial = $comercialid")
            ->orderBy('descrip','asc')
            ->get();

        $checkfact  = [];
        $sucursales = [];
        $arrayinsta = [];
        $listado    = [];
        $resultsven = [];
        $vendedores = [];
        $vsucursal  = [];

        $fechasreport = $request->fechasreport;
        $fksucursal   = (isset($request->fksucursal ))? $request->fksucursal : '';
        $fechasaux    = str_replace(' ','',$fechasreport);

        $fec1   = $fec2   = '';
        $fecha1 = $fecha2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if($fechasreport!='') {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fechasreport != ''){
            $fecha1 = $fec1;
            $fecha2 = $fec2;

            list($d1,$m1,$y1) = explode("/",$fec1);
            list($d2,$m2,$y2) = explode("/",$fec2);

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";

            $results = DB::table('safact as f')
                ->select([
                    'f.fk_sucursal',
                    'f.numerod',
                    DB::raw('((f.dolares-f.vuelto_dolares)*f.Signo) as dolares'),
                    DB::raw('(f.pesos*f.Signo) as pesos'),
                    DB::raw('(f.peso_tranf*f.Signo) as peso_tranf'),
                    DB::raw('(f.euros*f.Signo) as euros'),
                    DB::raw('(f.dolar_transf*f.Signo) as transf'),
                    DB::raw('((f.cancele-f.efectivosumado-f.vuelto_cancele)*f.Signo) as cancele'),
                    DB::raw('((f.vuelto_cancele)*f.Signo) as vuelto_cancele'),
                    DB::raw('(f.mtotax*f.Signo) as mtotax'),
                    DB::raw('(f.TGravable*f.Signo) as montobase'),
                    DB::raw('((f.cancelt-f.tarjetasumado)*f.Signo) as cancelt'),
                    DB::raw('(f.texento*f.Signo) as texentofact'),

                    DB::raw('((f.mtototal*f.Signo)/f.tasa_dolar) as mtototal'),
                    DB::raw('(((f.contado+f.credito)*f.Signo)/f.tasa_dolar) as totalventa'),
                    DB::raw('((f.credito*f.Signo)/f.tasa_dolar) as credito'),
                    DB::raw('((f.contado*f.Signo)/f.tasa_dolar) as contado'),
                    'a.descrip',
                    'g.codvend',
                    'g.descrip as vendedor'
                ])
                ->join('sasucursal as a', function($join) use ($comercialid) {
                    $join->on('a.id', '=', 'f.fk_sucursal')
                        ->where('a.fk_comercial', '=', $comercialid);
                })
                ->join('savend as g', function($join) use ($comercialid) {
                    $join->on('g.codvend', '=', 'f.codvend')
                        ->where('g.comercial', '=', $comercialid);
                })
                ->whereIn('f.tipofac', ['A', 'B'])
                ->whereBetween('f.FechaE', ["$y1-$m1-$d1 00:00:00", "$y2-$m2-$d2 23:58:22"])
                ->orderBy('a.descrip');

            if($fksucursal > 0)
                $results =  $results->where('f.fk_sucursal', $fksucursal);

            $results =  $results->get();


            if(isset($results))
                foreach ($results as $venta) {

                    if(!isset($sucursales[$venta->fk_sucursal])){
                        $sucursales[$venta->fk_sucursal] = $venta->descrip;
                    }

                    if(!isset($vendedores[$venta->codvend])){
                        $vendedores[$venta->codvend]['descrip'] = $venta->vendedor;

                    }


                    if(!isset($vsucursal[$venta->fk_sucursal])){
                        $vsucursal[$venta->fk_sucursal]['descrip'] = $venta->descrip;
                        $vsucursal[$venta->fk_sucursal]['cant']    = 0;
                        $vsucursal[$venta->fk_sucursal]['venta']   = 0;
                    }


                    if(!isset($checkfact[$venta->numerod])){
                        $checkfact[$venta->numerod] = 1;

                        if(!isset($listado[$venta->fk_sucursal]['dolares']))
                            $listado[$venta->fk_sucursal]['dolares'] =0;
                        $listado[$venta->fk_sucursal]['dolares'] += $venta->dolares;

                        if(!isset($listado[$venta->fk_sucursal]['pesos']))
                            $listado[$venta->fk_sucursal]['pesos'] =0;
                        $listado[$venta->fk_sucursal]['pesos']   += $venta->pesos;

                        if(!isset($listado[$venta->fk_sucursal]['peso_tranf']))
                            $listado[$venta->fk_sucursal]['peso_tranf'] =0;
                        $listado[$venta->fk_sucursal]['peso_tranf'] += $venta->peso_tranf;

                        if(!isset($listado[$venta->fk_sucursal]['euros']))
                            $listado[$venta->fk_sucursal]['euros'] =0;
                        $listado[$venta->fk_sucursal]['euros']   += $venta->euros;

                        if(!isset($listado[$venta->fk_sucursal]['transf']))
                            $listado[$venta->fk_sucursal]['transf'] =0;
                        $listado[$venta->fk_sucursal]['transf']  += $venta->transf;

                        if(!isset($listado[$venta->fk_sucursal]['cancele']))
                            $listado[$venta->fk_sucursal]['cancele'] =0;
                        $listado[$venta->fk_sucursal]['cancele'] += $venta->cancele;

                        if(!isset($listado[$venta->fk_sucursal]['vuelto_cancele']))
                            $listado[$venta->fk_sucursal]['vuelto_cancele'] =0;
                        $listado[$venta->fk_sucursal]['vuelto_cancele'] += $venta->vuelto_cancele;

                        if(!isset($listado[$venta->fk_sucursal]['cancelt']))
                            $listado[$venta->fk_sucursal]['cancelt'] =0;
                        $listado[$venta->fk_sucursal]['cancelt'] += $venta->cancelt;

                        if(!isset($listado[$venta->fk_sucursal]['credito']))
                            $listado[$venta->fk_sucursal]['credito'] =0;
                        $listado[$venta->fk_sucursal]['credito'] += $venta->credito;

                        if(!isset($listado[$venta->fk_sucursal]['totalventa']))
                            $listado[$venta->fk_sucursal]['totalventa'] =0;
                        $listado[$venta->fk_sucursal]['totalventa'] += $venta->totalventa;
                    }

                }
        }

        return view('reporteVentas', compact(
            'fechasreport',
            'vsucursal',
            'sucursales',
            'fecha1',
            'fecha2',
            'fksucursal',
            'allsucursales',
            'listado'));
    }

    public function reporteventasucu(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fksucursal   = $request->fksucursal;
        $contado      = $request->contado;
        $credito      = $request->credito;

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"-"))
            list($fec1, $fec2) = explode("-",$fechasaux);
        else {
            list($d1, $m1, $y1) = explode("/", $fechasreport);
            $fec1 = "$d1/$m1/$y1";
            $fec2 = $fec1;
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $ventas = Safact::selectRaw("fk_sucursal,nrounico,descrip,numerod, tipofac, codclie,
                            ((dolares-vuelto_dolares)*Signo) as dolares,
                            (pesos*Signo) as pesos,
                            (peso_tranf*Signo) as peso_tranf ,
                            (euros*Signo) as euros,
                            (dolar_transf*Signo) as transf,
                            ((cancele-efectivosumado-vuelto_cancele)*Signo) as cancele,
                             ((vuelto_cancele)*Signo) as vuelto_cancele,

                            (mtotax*Signo) as mtotax,
                            (TGravable*Signo) as montobase,
                            ((cancelt-tarjetasumado)*Signo) as cancelt,
                            (texento*Signo) as texentofact,
                            (igtf_cancele*Signo) as igtf_cancele,
                            (igtf_cancelt*Signo) as igtf_cancelt ,
                            (igtf_dolares*Signo) as igtf_dolares  ,
                            (igtf_pesos*Signo) as igtf_pesos,
                            (igtf_dolar_transf*Signo) as igtf_transf,
                             (igtf_monto*Signo) as igtf_monto ,


                             ((mtototal*Signo)/tasa_dolar) as mtototal,
                             (((contado+credito)*Signo)/tasa_dolar) as totalventa,
                             (((cancelaUSD)*Signo)) as cancelaUSD,
                             ((credito*Signo)/tasa_dolar) as credito,
                             ((contado*Signo)/tasa_dolar) as contado
                            ")
            ->whereRaw(" TipoFac in('A','B','Z','W') and fk_sucursal = $fksucursal")
            ->whereBetween('fechat', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00']);

        if($credito == 1)
            $ventas = $ventas->whereRaw("credito > 10");

        $ventas = $ventas->get();

        $listado    = [];
        if(isset($ventas))
            foreach ($ventas as $venta) {

                if(!isset($listado[$venta->nrounico]['codclie']))
                    $listado[$venta->nrounico]['codclie'] ='';
                $listado[$venta->nrounico]['codclie'] = $venta->codclie;

                if(!isset($listado[$venta->nrounico]['fksucu']))
                    $listado[$venta->nrounico]['fksucu'] ='';
                $listado[$venta->nrounico]['fksucu'] = $fksucursal;

                if(!isset($listado[$venta->nrounico]['numerod']))
                    $listado[$venta->nrounico]['numerod'] ='';
                $listado[$venta->nrounico]['numerod'] = $venta->numerod;

                if(!isset($listado[$venta->nrounico]['tipofac']))
                    $listado[$venta->nrounico]['tipofac'] ='';
                $listado[$venta->nrounico]['tipofac'] = $venta->tipofac;

                if(!isset($listado[$venta->nrounico]['dolares']))
                    $listado[$venta->nrounico]['dolares'] =0;
                $listado[$venta->nrounico]['dolares'] = $venta->dolares;

                if(!isset($listado[$venta->nrounico]['cliente']))
                    $listado[$venta->nrounico]['cliente'] ='';
                $listado[$venta->nrounico]['cliente'] =$venta->descrip;

                if(!isset($listado[$venta->nrounico]['pesos']))
                    $listado[$venta->nrounico]['pesos'] =0;
                $listado[$venta->nrounico]['pesos']   = $venta->pesos;

                if(!isset($listado[$venta->nrounico]['peso_tranf']))
                    $listado[$venta->nrounico]['peso_tranf'] =0;
                $listado[$venta->nrounico]['peso_tranf'] = $venta->peso_tranf;

                if(!isset($listado[$venta->nrounico]['euros']))
                    $listado[$venta->nrounico]['euros'] =0;
                $listado[$venta->nrounico]['euros']   = $venta->euros;

                if(!isset($listado[$venta->nrounico]['transf']))
                    $listado[$venta->nrounico]['transf'] =0;
                $listado[$venta->nrounico]['transf']  = $venta->transf;

                if(!isset($listado[$venta->nrounico]['cancele']))
                    $listado[$venta->nrounico]['cancele'] =0;
                $listado[$venta->nrounico]['cancele'] = $venta->cancele;

                if(!isset($listado[$venta->nrounico]['vuelto_cancele']))
                    $listado[$venta->nrounico]['vuelto_cancele'] =0;
                $listado[$venta->nrounico]['vuelto_cancele'] = $venta->vuelto_cancele;

                if(!isset($listado[$venta->nrounico]['cancelt']))
                    $listado[$venta->nrounico]['cancelt'] =0;
                $listado[$venta->nrounico]['cancelt'] = $venta->cancelt;

                if(!isset($listado[$venta->nrounico]['credito']))
                    $listado[$venta->nrounico]['credito'] =0;
                $listado[$venta->nrounico]['credito'] = $venta->credito;

                if(!isset($listado[$venta->nrounico]['cancelaUSD']))
                    $listado[$venta->nrounico]['cancelaUSD'] =0;
                $listado[$venta->nrounico]['cancelaUSD'] = $venta->cancelaUSD;

                if(!isset($listado[$venta->nrounico]['totalventa']))
                    $listado[$venta->nrounico]['totalventa'] =0;
                $listado[$venta->nrounico]['totalventa'] = $venta->totalventa;
            }


        $cobranzas = Saacxc::selectRaw("(cancele - (dolares*tasadolar)) as cancele, codusua, (cancelt - (dolar_tranf*tasadolar)) as cancelt, dolar_tranf as transf, dolares, codclie,
          date_format(FechaT, '%d/%m/%Y') as fecha, codvend, Document, nrounico, euros,cancelausd,
        tasadolar, pesos, peso_tranf, tasapeso, numerod, tipocxc, montodolares, fk_sucursal, CodClie ")
            ->with([ 'cliente',
                'sucursal.comercial:id',
            ])
            ->whereRaw(" (tipocxc = 50 or EsUnPago = 1)  and fk_sucursal = $fksucursal")
            ->whereRaw(" (tipocxc <> 99 ) ")
            ->whereBetween('fechat', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00'])
            ->whereHas('sucursal.comercial', function ($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->get();
        $listadoc = [];
        if(isset($cobranzas))
            foreach ($cobranzas as $cobranza) {
                if(!isset($listadoc[$cobranza->nrounico]['codclie']))
                    $listadoc[$cobranza->nrounico]['codclie'] ='';
                $listadoc[$cobranza->nrounico]['codclie'] = $cobranza->CodClie;

                if(!isset($listadoc[$cobranza->nrounico]['fksucu']))
                    $listadoc[$cobranza->nrounico]['fksucu'] ='';
                $listadoc[$cobranza->nrounico]['fksucu'] = $fksucursal;

                if(!isset($listadoc[$cobranza->nrounico]['numerod']))
                    $listadoc[$cobranza->nrounico]['numerod'] ='';
                $listadoc[$cobranza->nrounico]['numerod'] = $cobranza->numerod;

                if(!isset($listadoc[$cobranza->nrounico]['dolares']))
                    $listadoc[$cobranza->nrounico]['dolares'] =0;
                $listadoc[$cobranza->nrounico]['dolares'] = $cobranza->dolares;

                if(!isset($listadoc[$cobranza->nrounico]['cliente']))
                    $listadoc[$cobranza->nrounico]['cliente'] ='';
                $listadoc[$cobranza->nrounico]['cliente'] = $cobranza->cliente->descrip;

                if(!isset($listadoc[$cobranza->nrounico]['pesos']))
                    $listadoc[$cobranza->nrounico]['pesos'] =0;
                $listadoc[$cobranza->nrounico]['pesos']   = $cobranza->pesos;

                if(!isset($listadoc[$cobranza->nrounico]['peso_tranf']))
                    $listadoc[$cobranza->nrounico]['peso_tranf'] =0;
                $listadoc[$cobranza->nrounico]['peso_tranf'] = $cobranza->peso_tranf;

                if(!isset($listadoc[$cobranza->nrounico]['euros']))
                    $listadoc[$cobranza->nrounico]['euros'] =0;
                $listadoc[$cobranza->nrounico]['euros']   = $cobranza->euros;

                if(!isset($listadoc[$cobranza->nrounico]['transf']))
                    $listadoc[$cobranza->nrounico]['transf'] =0;
                $listadoc[$cobranza->nrounico]['transf']  = $cobranza->transf;

                if(!isset($listadoc[$cobranza->nrounico]['cancele']))
                    $listadoc[$cobranza->nrounico]['cancele'] =0;
                $listadoc[$cobranza->nrounico]['cancele'] = ($cobranza->cancele > 1) ? $cobranza->cancele : 0;

                if(!isset($listadoc[$cobranza->nrounico]['cancelt']))
                    $listadoc[$cobranza->nrounico]['cancelt'] =0;
                $listadoc[$cobranza->nrounico]['cancelt'] = ($cobranza->cancelt > 1) ? $cobranza->cancelt : 0;

                if(!isset($listadoc[$cobranza->nrounico]['cancelausd']))
                    $listadoc[$cobranza->nrounico]['cancelausd'] =0;
                $listadoc[$cobranza->nrounico]['cancelausd'] = $cobranza->cancelausd;

                if(!isset($listadoc[$cobranza->nrounico]['totalcobranza']))
                    $listadoc[$cobranza->nrounico]['totalcobranza'] =0;
                $listadoc[$cobranza->nrounico]['totalcobranza'] = $cobranza->montodolares;
            }

        $topprod = Saitemfac::whereIn('TipoFac', ['A', 'B'])
            ->selectRaw("CodItem, SUM(Cantidad * Signo) as salidas ")
            ->where('esserv', 0)
            ->where('nrolineac', 0)
            ->whereBetween('FechaE', ["{$fec1} 00:00:00.00", "{$fec2} 23:59:59.00"])
            ->with([
                'factura.sucursal.comercial:id',
                'producto:codprod,Descrip',
            ])
            ->groupBy(["CodItem"])
            ->where('fk_sucursal',$fksucursal)
            ->orderByDesc('salidas');

        if($credito == 1){
            $topprod = $topprod->whereHas('factura', function ($q) use ($fksucursal) {
                $q->whereRaw("safact.credito > 0 and safact.fk_sucursal = $fksucursal");
            });
        }
        $topprod = $topprod->get();

        //dd($topprod->toSql(), $topprod->getBindings());
        return view('reporteventasucursal', compact('fechasreport','listadoc', 'topprod', 'ventas', 'fecha1', 'fecha2', 'listado'))->render();
    }

    public function cambiarcomercial(Request $request)
    {
        $comercialid = $request->comercialid;
        session(['comercialid' => $comercialid]);
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        return redirect()->back();
    }

    public function revisarcosas()
    {
           /* $sqlduplicado = "

            select TipoFac, CodPago, NumeroD, Monto,fk_sucursal
                             from saipavta
                             group by TipoFac, CodPago, NumeroD, Monto, fk_sucursal
                             HAVING count(*)>1
                             LIMIT 20
                                    ";

            $duplicados = DB::select($sqlduplicado);
            foreach ($duplicados as $item){

                $record = Saipavta::where(["TipoFac" =>$item->TipoFac,
                    "CodPago"=>$item->CodPago,
                    "NumeroD"=>$item->NumeroD,
                    "Monto"  =>$item->Monto,
                    "fk_sucursal"=>$item->fk_sucursal])->first();
                if(isset($record->TipoFac))
                    $record->delete();

            }
*/

            $sqlduplicado = "SELECT  TipoFac, CodItem, CodUbic, NumeroD, NroLinea, fk_sucursal
                            FROM saitemfac
                            GROUP by TipoFac, CodItem, CodUbic, NumeroD, NroLinea, fk_sucursal
                            HAVING COUNT(*)>1
                            LIMIT 20
                                    ";

            $duplicados = DB::select($sqlduplicado);
            foreach ($duplicados as $item){

                $record = Saitemfac::where(["TipoFac"=>$item->TipoFac,
                    "CodItem"=>$item->CodItem,
                    "CodUbic"=>$item->CodUbic,
                    "NumeroD"=>$item->NumeroD,
                    "NroLinea"=>$item->NroLinea,
                    "fk_sucursal"=>$item->fk_sucursal])->first();
                if(isset($record->TipoFac))
                    $record->delete();

            }


/*
            $sqlduplicado = "select  tipofac, nrounico, fk_sucursal, numerod
                             from safact
                             group by tipofac, nrounico, fk_sucursal, numerod
                             HAVING count(*)>1
 LIMIT 200
                            ";

            $duplicados = DB::select($sqlduplicado);
            foreach ($duplicados as $item){

                $record = Safact::where(["tipofac"    =>$item->tipofac,
                    "nrounico"   =>$item->nrounico,
                    "fk_sucursal"=>$item->fk_sucursal,
                    "numerod"    =>$item->numerod])->first();
                if(isset($record->nrounico))
                    $record->delete();

            }*/
/*
            $sqlduplicado = "select  tipocxc, nrounico, fk_sucursal, numerod
                             from saacxc
                             group by tipocxc, nrounico, fk_sucursal, numerod
                             HAVING count(*)>1
 LIMIT 200
                            ";

            $duplicados = DB::select($sqlduplicado);
            foreach ($duplicados as $item){

                $record = Saacxc::where(["tipocxc"    =>$item->tipocxc,
                    "nrounico"   =>$item->nrounico,
                    "fk_sucursal"=>$item->fk_sucursal,
                    "numerod"    =>$item->numerod])->first();

                if(isset($record->nrounico))
                    $record->delete();

            }*/

            echo 'revisado';
    }

    public function index(Request $request)
    {
        //dd(bcrypt('Sistrh2026'));
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        return view('index' );

    }

    public function resumenVentas(Request $request)
    {

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $costoinven   = [];
        $unidadesvendidas = '';
        $contado      = 0;
        $credito      = 0;
        $facturas     = 0;
        $devoluciones = 0;
        $sucursales   = [];
        $cxc          = '';


            $contado = $credito =  $facturas = $devoluciones = $unidadesvendidas = 0;
            $sucursales  = [];

            if(isset($items))
                $unidadesvendidas =  $items->tantos;


            $ventas = Safact::selectRaw("
                                                    fk_sucursal,
                                                    tipofac,
                                                    count(*) as tantas,
                                                    sum(((contado + credito) * Signo) / tasa_dolar) as totalventa,
                                                    sum((credito * Signo) / tasa_dolar) as credito,
                                                    sum((contado * Signo) / tasa_dolar) as contado
                                                ")
                ->with([
                    'sucursal.comercial:id', // Solo las columnas necesarias
                ])
                ->whereIn('TipoFac', ['A', 'B']) // Reemplazo de whereRaw con whereIn
                ->whereBetween('fechat', ["{$fec1} 00:00:00", "{$fec2} 23:59:59"]) // Reemplazo de fecha con formato claro
                ->whereHas('sucursal.comercial', function ($q) use ($comercialid) {
                    $q->where('fk_comercial', $comercialid);
                })
                ->groupBy(['fk_sucursal', 'tipofac'])
                ->get();

            foreach($ventas as $venta){

                if($venta->tipofac == 'A' or $venta->tipofac == 'Z'){
                    $facturas += $venta->tantas;
                }
                if($venta->tipofac == 'B' or $venta->tipofac == 'W'){
                    $devoluciones += $venta->tantas;
                }

                $contado += number_format($venta->contado, 2, '.', '');
                $credito += number_format($venta->credito, 2, '.', '');

                if(!isset($sucursales[$venta->fk_sucursal]['descrip'])) {
                    $sucursales[$venta->fk_sucursal]['descrip'] = $venta->sucursal->descrip;
                    $sucursales[$venta->fk_sucursal]['id']           = $venta->fk_sucursal;
                    $sucursales[$venta->fk_sucursal]['total']        = 0;
                    $sucursales[$venta->fk_sucursal]['contado']      = 0;
                    $sucursales[$venta->fk_sucursal]['credito']      = 0;
                    $sucursales[$venta->fk_sucursal]['facturas']     = 0;
                    $sucursales[$venta->fk_sucursal]['tcobranzas']   = 0;
                    $sucursales[$venta->fk_sucursal]['devoluciones'] = 0;
                    $sucursales[$venta->fk_sucursal]['cobranzas']    = 0;
                }

                $sucursales[$venta->fk_sucursal]['total']   = $venta->contado + $venta->credito;
                $sucursales[$venta->fk_sucursal]['contado'] += number_format($venta->contado,2,'.','');
                $sucursales[$venta->fk_sucursal]['credito'] += number_format($venta->credito,2,'.','');

                if($venta->tipofac == 'A' or $venta->tipofac == 'Z'){
                    $sucursales[$venta->fk_sucursal]['facturas'] = $venta->tantas;
                }
                if($venta->tipofac == 'B' or $venta->tipofac == 'W'){
                    $sucursales[$venta->fk_sucursal]['devoluciones'] = $venta->tantas;
                }
            }


            $cobranzas = Saacxc::selectRaw("count(*) as tantas, sum(montodolares) as cobranza, fk_sucursal ")
                ->with([
                    'sucursal.comercial:id',
                ])
                ->whereRaw("tipocxc <> 99 and  (tipocxc = 50 or EsUnPago = 1) ")
                ->whereBetween('fechat', ["{$fec1} 00:00:00", "{$fec2} 23:59:59"])
                ->whereHas('sucursal.comercial', function ($q) use ($comercialid) {
                    $q->where('fk_comercial', $comercialid);
                })
                ->groupBy(['fk_sucursal' ])
                ->get();

            //dd($cobranzas->toSql(), $cobranzas->getBindings());

            foreach($cobranzas as $cobranza){

                if(!isset($sucursales[$cobranza->fk_sucursal]['descrip'])) {
                    $sucursales[$cobranza->fk_sucursal]['descrip']      = $cobranza->sucursal->descrip;
                    $sucursales[$cobranza->fk_sucursal]['id']           = $cobranza->sucursal->id;
                    $sucursales[$cobranza->fk_sucursal]['total']        = 0;
                    $sucursales[$cobranza->fk_sucursal]['contado']      = 0;
                    $sucursales[$cobranza->fk_sucursal]['credito']      = 0;
                    $sucursales[$cobranza->fk_sucursal]['facturas']     = 0;
                    $sucursales[$cobranza->fk_sucursal]['tcobranzas']   = 0;
                    $sucursales[$cobranza->fk_sucursal]['cobranzas']    = 0;
                    $sucursales[$cobranza->fk_sucursal]['devoluciones'] = 0;
                }
                $sucursales[$cobranza->fk_sucursal]['tcobranzas'] += $cobranza->tantas;
                $sucursales[$cobranza->fk_sucursal]['cobranzas']  += $cobranza->cobranza;

            }

            sort($sucursales);


        $montos = DB::table('saipavta as a')
            ->select([
                'b.clase',
                DB::raw("SUM(CASE a.tipofac WHEN 'A' THEN a.monto WHEN 'B' THEN (a.monto * -1) ELSE 0 END) as bs"),

            ])
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->where('b.bs', 1)
            ->where('b.comercial', $comercialid) // Filtro directo
            ->where('c.fk_comercial', $comercialid) // Filtro directo
            ->whereExists(function($query) use ($comercialid) {
                $query->select(DB::raw(1))
                    ->from('sasucursal as s')
                    ->whereColumn('s.id', 'a.fk_sucursal')
                    ->where('s.fk_comercial', $comercialid);
            })
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->groupBy(  'b.clase');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $montos =  $montos->where('a.fk_sucursal', $fksucursal);
        }

        $montos = $montos->get();

        $clases     = [];
        $listado    = [];
        $listadousd = [];

        if(isset($montos) and count($montos)> 0 )
            foreach ($montos as $monto) {

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->clase;
                }

                if(!isset($listado[$monto->clase]))
                    $listado[$monto->clase] = 0;

                $listado[$monto->clase] += $monto->bs;

            }

        $montos = [];

        $montos = DB::table('saipacxc as a')
            ->select([
                  'b.clase',
                DB::raw('SUM(a.monto) as bs')
            ])
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->join('saacxc as d', 'a.NroPpal', '=', 'd.nrounico')
            ->where('c.fk_comercial', $comercialid)
            ->whereRaw("d.tipocxc not in ('99','98') and d.fk_sucursal = a.fk_sucursal ")
            ->where('b.bs', 1)
            ->where('b.comercial', $comercialid) // Filtro directo
            ->whereRaw("a.created_at >= '$fec1 00:00:00' and a.created_at <= '$fec2 23:59:00'")
            ->groupBy(  'b.clase');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $montos =  $montos->where('a.fk_sucursal', $fksucursal);
        }

        $montos = $montos->get();
        if(isset($montos) and count($montos)> 0 )
            foreach ($montos as $monto) {

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->clase;
                }

                if(!isset($listado[$monto->clase]))
                    $listado[$monto->clase] = 0;

                $listado[$monto->clase] += $monto->bs;

            }

        $montos = DB::table('saipavta as a')
            ->select([ 'b.clase',
                DB::raw("SUM(CASE a.tipofac WHEN 'A' THEN a.dolares WHEN 'B' THEN (a.dolares * -1) ELSE 0 END) as dolares")
            ])
            ->join('satarj as b', 'a.codpago', '=', 'b.codtarj')
            ->join('sasucursal as c', function ($join) use ($comercialid) {
                $join->on('a.fk_sucursal', '=', 'c.id')
                    ->where('c.fk_comercial', '=', $comercialid);
            })
            ->where('b.dolares', 1)
            ->where('b.comercial', $comercialid) // Filtro directo
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->groupBy( 'b.clase');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $montos =  $montos->where('a.fk_sucursal', $fksucursal);
        }

        $montos = $montos->get();

        if (isset($montos))
            foreach ($montos as $monto) {

                if (!isset($clases[$monto->clase])) {
                    $clases[$monto->clase] = $monto->clase;
                }

                if (!isset($listadousd[$monto->clase]))
                    $listadousd[$monto->clase] = 0;

                $listadousd[$monto->clase] += $monto->dolares;

            }

        $montos = DB::table('saipacxc as a')
            ->select([
               'b.clase',
                DB::raw('SUM(a.dolares) as dolares')
            ])
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->join('saacxc as d', 'a.NroPpal', '=', 'd.nrounico')
            ->where('c.fk_comercial', $comercialid)
            ->whereRaw("d.tipocxc not in ('99','98') and d.fk_sucursal = a.fk_sucursal")
            ->where('b.comercial', $comercialid) // Filtro directo
            ->where('b.dolares', 1)
            ->whereRaw("a.created_at >= '$fec1 00:00:00' and a.created_at <= '$fec2 23:59:00'")
            ->groupBy(  'b.clase')
            ->get();


        if (isset($montos))
            foreach ($montos as $monto) {

                if (!isset($clases[$monto->clase])) {
                    $clases[$monto->clase] = $monto->clase;
                }

                if (!isset($listado[$monto->clase]))
                    $listadousd[$monto->clase] = 0;

                $listadousd[$monto->clase] += $monto->dolares;

            }



        ksort($clases);


        Session::put('lang', 'sp');
        Session::save();

            return view('resumenVentas',compact(    'fechasreport', 'clases', 'listado', 'listadousd',
                'costoinven',   'unidadesvendidas',
                'contado','credito', 'facturas', 'devoluciones', 'sucursales', 'cxc'));


    }

    public function lang($locale) {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }
}
