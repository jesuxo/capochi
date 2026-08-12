<?php

namespace App\Http\Controllers;

use App\Exports\SaprodExport;
use App\Imports\SaprodUpdate;
use App\Imports\SaprodUpdateSaexis;
use App\Models\Saeprdday;
use App\Models\Saexis;
use App\Models\Sainsta;
use App\Models\Saitemfac;
use App\Models\Saoper;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class SaprodController extends Controller
{
    public function inventarios(Request $request){
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $costoinven = DB::table('saprod as a')
            ->join('saexis as b', 'a.codprod', '=', 'b.codprod')
            ->join('sasucursal as c', 'b.fk_sucursal', '=', 'c.id')
            ->where('a.comercial', $comercialid)
            ->where('c.fk_comercial', $comercialid)
            ->select(
                DB::raw('SUM((a.preciod + a.preciod2) * b.existen) as suma'),
                'c.descrip'
            )
            ->groupBy('c.descrip')
            ->orderBy('c.descrip')
            ->get();

        return view('reporteInventarios', compact('costoinven') );
    }

    public function saprodexport($codalte)
    {
        $file = Excel::download(new SaprodExport($codalte), 'productos.xlsx');

        return $file;
    }

    public function updateSaprodData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new SaprodUpdate(), $request->file('import_file'));

        return redirect()->back()->with('status', 'Archivo Procesado Exitosamente');
    }

    public function updateSaexisData(Request $request)
    {
        try {
            // Validar los datos recibidos
            $request->validate([
                'import_file' => 'required|file|mimes:xlsx,xls,csv',
                'fksucursal' => 'required|integer',
                'fecha_inventario' => 'required|date|before_or_equal:today'
            ]);

            $sucursalid = $request->fksucursal;
            $fechaInventario = $request->fecha_inventario;

            // Verificar si ya existe inventario para esa fecha y sucursal (opcional)
            $existeInventario = Saeprdday::where('fecha', $fechaInventario)
                ->where('fksucursal', $sucursalid)
                ->exists();

            if($existeInventario && $sucursalid != 0) {
                return redirect()->back()->with('warning',
                    'Ya existe inventario para la sucursal en la fecha ' .
                    Carbon::parse($fechaInventario)->format('d/m/Y') .
                    '. ¿Deseas sobrescribirlo?');
            }

            // Importar el archivo
            if($request->hasFile('import_file')) {
                $import = new SaprodUpdateSaexis($sucursalid, $fechaInventario);
                Excel::import($import, $request->file('import_file'));

                $mensaje = $sucursalid == 0
                    ? 'Inventario actualizado para TODAS las sucursales con fecha ' .
                    Carbon::parse($fechaInventario)->format('d/m/Y')
                    : 'Inventario actualizado para la sucursal con fecha ' .
                    Carbon::parse($fechaInventario)->format('d/m/Y');

                return redirect()->back()->with('success', $mensaje);
            }

            return redirect()->back()->with('error', 'No se pudo procesar el archivo');

        } catch (\Exception $e) {
            \Log::error('Error en updateSaexisData: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al procesar el inventario: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')->get();

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
                               ->where(['comercial'=>$comercialid,'tipoins'=>0])
                               ->orderBy('codalte','asc')
                               ->get();

        return view('product-list', compact('instancias', 'allsucursales') );
    }

    public function existencias(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fksucursal    = (isset($request->fksucursal ))? $request->fksucursal : '';
        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')
            ->whereRaw("id in ($arraysucursales)")
            ->get();
        $instancias    = Sainsta::selectRaw("Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
            ->where('comercial', $comercialid)
            ->orderBy('codalte','asc')
            ->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)
            ->whereRaw("id in ($arraysucursales)")
            ->orderBy('descrip');

        if($fksucursal)
            $sucursales = $sucursales->where('id',$fksucursal);

        $sucursales = $sucursales->get();

        return view('existenciasInstancias', compact( 'fksucursal', 'arraysucursales', 'allsucursales', 'sucursales', 'instancias', 'comercialid') );
    }

    public function existenciasphp(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $codinst    = $request->codinst;
        $fksucursal = $request->fksucursal;

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
            ->where('comercial',$comercial)
            ->orderBy('descrip','asc')
            ->get();
        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)");

        if($fksucursal)
            $sucursales = $sucursales->where('id',$fksucursal);

        $sucursales = $sucursales->get();

        $instanciaselected = '';

        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }

        return view('existenciasInstanciasphp',
            compact(
                'fksucursal',
                'insPadre',
                'codinst',
                'sucursales',
                'instancias',
                'instanciaselected',
                'comercial')
        )->render();
    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $all = Saprod::where('comercial',$comercial)->with(['instancia'])->orderBy('fijo','asc')->get();
        $aux = [];
        $productos = [];
        $noimage = URL::asset('build/images/noimagen.jpg');
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "price"         => "$item->costod3",
                "fijo"          => "$item->fijo",
                "exdecimal"     => "$item->exdecimal",
                "image"         => (isset($item->productImg))? '': $noimage,
                "productTitle"  => "$item->descrip",
                "category"      => $item->instancia->descrip
            ];

            array_push($productos,$aux);
        }
        return response()->json($productos );
    }

    public function productossucursales(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1 ]) // Niveles 1 y 2
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->whereRaw("id in ($arraysucursales)")->orderBy('descrip','asc')->get();

        $existenciaact = (isset($request->existenciaact ))? $request->existenciaact : '';
        $fksucursal    = (isset($request->fksucursal    ))? $request->fksucursal    : '';
        $codinst       = (isset($request->codinst       ))? $request->codinst       : '';
        $fechasreport  = $request->fechasreport;
        $fechasreport2 = (isset($request->fechasreport2))? $request->fechasreport2 :'';

        $fechasaux     = str_replace(' ', '', $fechasreport);
        $fechasaux2    = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";

            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal,$codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if(  $fec1 != ''  ){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod ) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('productosSucursales', compact(
            'fecha1',
            'fecha2',
            'instancias',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'codinst',
            'cantidadprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales',
            'existenciaact'
        ));
    }

    /**
     * Reporte de mermas por categoría y sucursal
     */
    public function mermassucursales(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;

        // Obtener sucursales del comercial
        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->orderBy('descrip', 'asc')
            ->get();

        // Obtener categorías (instancias nivel 1)
        $instancias = Sainsta::where('comercial', $comercialid)
            ->where('tipoins', 0)
            ->where('nivel', 1)
            ->orderBy('descrip', 'asc')
            ->get();

        // Obtener operaciones de merma para el filtro
        $operacionesMerma = Saoper::where('comercial', $comercialid)
            ->where('merma', 1)
            ->orderBy('orden', 'asc')
            ->get();

        // Parámetros de filtro
        $fksucursal   = $request->input('fksucursal');
        $codinst      = $request->input('codinst');
        $codoper      = $request->input('codoper'); // Nuevo filtro por operación
        $fechasreport = $request->input('fechasreport');
        $fechashoy    = Carbon::now()->format('d/m/Y');

        if (!$fechasreport) {
            $fechasreport = $fechashoy;
        }

        // Procesar fechas
        $fechasaux = str_replace(' ', '', $fechasreport);
        $fec1 = $fec2 = '';

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            list($d1, $m1, $y1) = explode("/", $fechasreport);
            $fec1 = "$d1/$m1/$y1";
            $fec2 = $fec1;
            $fechasreport = "$fec1 to $fec2";
        }

        list($d1, $m1, $y1) = explode("/", $fec1);
        list($d2, $m2, $y2) = explode("/", $fec2);

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        $fec1Sql = "$y1-$m1-$d1";
        $fec2Sql = "$y2-$m2-$d2";

        // Construir consulta para mermas
        $query = DB::table('saitemopi as b')
            ->select([
                'a.codprod',
                'a.descrip as producto',
                'a.exdecimal',
                'c.descrip as sucursal',
                'c.id as fk_sucursal',
                'e.descrip as instancia',
                'e.codalte',
                DB::raw('SUM(b.Cantidad) as cantidad_merma')
            ])
            ->join('saprod as a', 'a.codprod', '=', 'b.CodItem')
            ->join('sasucursal as c', 'c.id', '=', 'b.fk_sucursal')
            ->join('saopei as d', function($join) {
                $join->on('d.NumeroD', '=', 'b.NumeroD')
                    ->on('d.TipoOpI', '=', 'b.TipoOpI');
            })
            ->join('sainsta as e', 'e.codinst', '=', 'a.codinst')
            ->join('saoper as f', 'f.codoper', '=', 'd.CodOper')
            ->where('a.comercial', $comercialid)
            ->where('c.fk_comercial', $comercialid)
            ->where('e.comercial', $comercialid)
            ->where('f.comercial', $comercialid)
            ->where('f.merma', 1)
            ->whereIn('b.tipoopi', ['P'])
            ->whereBetween('b.FechaE', [$fec1Sql . ' 00:00:00.000', $fec2Sql . ' 23:59:59.999'])
            ->whereBetween('d.FechaT', [$fec1Sql . ' 00:00:00.000', $fec2Sql . ' 23:59:59.999']);

        // Aplicar filtros
        if ($fksucursal && $fksucursal > 0) {
            $query->where('c.id', $fksucursal);
        }

        if ($codinst) {
            $query->where('a.codinst', $codinst);
        }

        if ($codoper) {
            $query->where('f.codoper', $codoper);
        }

        $listado = $query->groupBy([
            'a.codprod', 'a.descrip', 'a.exdecimal',
            'c.descrip', 'c.id',
            'e.descrip', 'e.codalte'
        ])
            ->orderBy('e.codalte')
            ->get();

        // Procesar datos para la vista
        $sucursales   = [];
        $cantidadprod = [];
        $itemmermas   = [];

        foreach ($listado as $merma) {
            if (!isset($sucursales[$merma->fk_sucursal])) {
                $sucursales[$merma->fk_sucursal] = $merma->sucursal;
            }

            $key = $merma->codprod . $merma->fk_sucursal;
            if (!isset($cantidadprod[$key])) {
                $cantidadprod[$key] = 0;
            }
            $cantidadprod[$key] += $merma->cantidad_merma;

            $itemmermas[$merma->instancia][$merma->codprod] = [
                'descrip'   => $merma->producto,
                'exdecimal' => $merma->exdecimal
            ];
        }

        asort($sucursales);

        return view('mermasSucursales', compact(
            'fecha1',
            'fecha2',
            'fechasreport',
            'sucursales',
            'itemmermas',
            'cantidadprod',
            'codinst',
            'codoper',
            'fksucursal',
            'allsucursales',
            'instancias',
            'operacionesMerma'
        ));
    }

    public function resultadosucursales(Request $request)
    {
        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $codinst = '';

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')->get();

        $fksucursal    = (isset($request->fksucursal ))? $request->fksucursal : '';
        $fechasreport  = $request->fechasreport;
        $fechasreport2 = (isset($request->fechasreport2))? $request->fechasreport2 :'';

        $fechasaux  = str_replace(' ', '', $fechasreport);
        $fechasaux2 = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";

            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal,$codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if($fksucursal != '' and $fec1 != ''){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod, $preciodprod) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('resultadosucursales', compact(
            'fecha1',
            'fecha2',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'cantidadprod',
            'costodprod',
            'preciodprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales'
        ));
    }

    private function obtenerVentasPeriodo($comercialid, $fechaInicio, $fechaFin, $fksucursal, $codinst = null)
    {
        $codalte = '';
        if(isset($codinst) and $codinst >0){
            $sainsta = Sainsta::where('codinst', $codinst)->first();
            if(isset($sainsta->codalte)){
                $codalte = $sainsta->codalte;
            }
        }

        $datos = Saitemfac::whereRaw("TipoFac in ('A','B')")
            ->selectRaw("fk_sucursal, CodItem, SUM(Cantidad*Signo) as salidas, SUM(Cantidad*costod*Signo) as costod, SUM(Cantidad*preciod*Signo) as preciod")
            ->with(['sucursal', 'producto.instancia' => function($q)use ($codalte) {
                if($codalte != '')
                    $q->whereRaw("codalte like '$codalte%'");
                $q->orderBy('codalte', 'asc');
            }])
            ->where('esserv', 0)
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->whereBetween('FechaE', [$fechaInicio . ' 00:00:00.00', $fechaFin . ' 23:58:22.00'])
            ->groupBy(['fk_sucursal', 'CodItem'])
            ->orderBy('fk_sucursal');

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $datos =  $datos->where('fk_sucursal', $fksucursal);
        }

        $datos =  $datos->get();

        return $datos;

    }

    private function procesarDatosVentas($listado, $agruparsucu): array
    {
        $sucursales   = [];
        $itemventas   = [];
        $cantidadprod = [];
        $costodprod   = [];
        $preciodprod  = [];

        if($agruparsucu == 1){


            if (isset($listado)) {
                foreach ($listado as $prodsuc) {
                    if (!isset($sucursales[$prodsuc->sucursal->id]))
                        $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;

                    if (!isset($cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($costodprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $costodprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($preciodprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $preciodprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;


                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['descrip']   = $prodsuc->producto->descrip;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['exdecimal'] = $prodsuc->producto->exdecimal;

                    $cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->salidas;
                    $costodprod  [$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->costod;
                    $preciodprod [$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->preciod;
                }
            }

            return [$sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod];
        }else{


            if (isset($listado)) {
                foreach ($listado as $prodsuc) {

                    if (!isset($sucursales[$prodsuc->sucursal->id]))
                        $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;

                    if (!isset($cantidadprod[$prodsuc->CodItem ]))
                        $cantidadprod[$prodsuc->CodItem ] = 0;

                    if (!isset($costodprod[$prodsuc->CodItem]))
                        $costodprod[$prodsuc->CodItem ] = 0;

                    if (!isset($preciodprod[$prodsuc->CodItem]))
                        $preciodprod[$prodsuc->CodItem ] = 0;

                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['descrip']   = $prodsuc->producto->descrip;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['exdecimal'] = $prodsuc->producto->exdecimal;

                    $cantidadprod[$prodsuc->CodItem] += $prodsuc->salidas;
                    $costodprod  [$prodsuc->CodItem] += $prodsuc->costod;
                    $preciodprod [$prodsuc->CodItem] += $prodsuc->preciod;
                }
            }

            return [ $sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod];

        }
    }

    public function operacionessucursales(Request $request)
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $codoper      = $request->codoper;
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

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $datasucu = '';
        if ($codoper != '') {
            $datasucu = "   d.codoper = '$codoper' ";
        }

        $listado = DB::table('saprod as a')
            ->select([
                DB::raw('SUM(b.Cantidad * d.Signo) AS salidas'),
                'a.codprod as coditem',
                'a.descrip as producto',
                'c.descrip as sucursal',
                'd.CodOper',
                'c.id as fk_sucursal',
                'a.exdecimal',
                'e.descrip as instancia',
                'f.descrip as operacion',
                'f.orden',
                'e.codalte'
            ])
            ->join('saitemopi as b', function($join) use ($fec1, $fec2) {
                $join->on('a.codprod', '=', 'b.coditem')
                    ->whereIn('b.tipoopi', ['P', 'O'])
                    ->whereBetween('b.FechaE', [
                        $fec1 . ' 00:00:00.000',
                        $fec2 . ' 23:59:59.999'
                    ]);
            })
            ->join('sasucursal as c', function($join) use ($comercialid) {
                $join->on('c.id', '=', 'b.fk_sucursal')
                    ->where('c.fk_comercial', $comercialid);
            })
            ->join('saopei as d', function($join) use ($fec1, $fec2) {
                $join->on('d.fk_sucursal', '=', 'b.fk_sucursal')
                    ->on('d.NumeroD', '=', 'b.NumeroD')
                    ->on('d.TipoOpI', '=', 'b.TipoOpI')
                    ->whereBetween('d.FechaT', [
                        $fec1 . ' 00:00:00.000',
                        $fec2 . ' 23:59:59.999'
                    ]);
            })
            ->join('sainsta as e', function($join) use ($comercialid) {
                $join->on('e.codinst', '=', 'a.codinst')
                    ->where('e.comercial', $comercialid);
            })
            ->join('saoper as f', 'f.codoper', '=', 'd.CodOper')
            ->where('a.comercial', $comercialid)
            ->when($datasucu, function($query) use ($datasucu) {

                if (!empty($datasucu)) {
                    $query->whereRaw($datasucu);
                }
            })
            ->groupBy([
                'a.codprod', 'a.descrip', 'c.descrip', 'd.CodOper', 'c.id',
                'a.exdecimal', 'e.descrip', 'e.codalte', 'f.descrip', 'f.orden'
            ])
            ->orderBy('e.codalte')
            ->get();

        $sucursales   = [];
        $cantidadprod = [];
        $itemopei     = [];
        if(isset($listado))

            foreach($listado as $prodsuc){

                if(!isset($sucursales[$prodsuc->fk_sucursal])){
                    $sucursales[$prodsuc->fk_sucursal] = $prodsuc->sucursal;
                }

                if(!isset($cantidadprod[$prodsuc->coditem.$prodsuc->fk_sucursal])){
                    $cantidadprod[$prodsuc->coditem.$prodsuc->fk_sucursal]=0;
                }

                $cantidadprod[$prodsuc->coditem.$prodsuc->fk_sucursal] += $prodsuc->salidas;
                if(isset($prodsuc->producto)){
                    $itemopei[$prodsuc->instancia][$prodsuc->coditem]['descrip']   = $prodsuc->producto;
                    $itemopei[$prodsuc->instancia][$prodsuc->coditem]['exdecimal'] = $prodsuc->exdecimal;
                }

            }

        asort($sucursales);

        $saoper = Saoper::where('comercial', $comercialid)->orderBy('id','asc')->get();

        return view('operacionesSucursales', compact('fechasreport', 'codoper', 'fecha1', 'saoper', 'fecha2', 'sucursales', 'itemopei', 'cantidadprod'));
    }

    public function operacionessucursal(Request $request)
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $fk_sucursal  = $request->fk_sucursal;

        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }
        $sucursal = '';
        if($fk_sucursal and $fk_sucursal >0){
            $sucursal = Sasucursal::find($fk_sucursal);
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

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $datasucu = '';

        if($fk_sucursal)
            $datasucu = " and d.fk_sucursal = $fk_sucursal";


        $sql = "SELECT
                SUM(b.Cantidad * d.Signo) AS salidas,
                a.codprod AS coditem,
                a.descrip ,
                c.descrip AS sucursal,
                d.CodOper,
                c.id AS fk_sucursal,
                a.exdecimal,
                e.descrip AS instancia,
                f.descrip AS operacion,
                f.orden,
                e.codalte
            FROM saprod a
            JOIN saitemopi b ON a.codprod = b.coditem
            JOIN sasucursal c ON c.id = b.fk_sucursal
            JOIN saopei d ON d.fk_sucursal = c.id
                          AND d.fk_sucursal = b.fk_sucursal
                          AND d.NumeroD = b.NumeroD
                          AND d.TipoOpI = b.TipoOpI
            JOIN sainsta e ON e.codinst = a.codinst
            JOIN saoper f ON f.codoper = d.CodOper
            WHERE
                a.comercial = $comercialid
                AND c.fk_comercial = $comercialid
                AND e.comercial = $comercialid
                AND b.tipoopi IN ('P', 'O')
                AND d.FechaT BETWEEN '$fec1 00:00:00.000' AND '$fec2 23:59:59.999'
                AND b.FechaE BETWEEN '$fec1 00:00:00.000' AND '$fec2 23:59:59.999'
                $datasucu
            GROUP BY
                a.codprod, a.descrip, c.descrip, d.CodOper, c.id,
                a.exdecimal, e.descrip, e.codalte, f.descrip, f.orden
            ORDER BY e.codalte;";

        $listado = DB::select($sql);


        $operaciones  = [];
        $cantidadprod = [];
        $itemopei     = [];
        $txtoper      = [];
        if(isset($listado))

            foreach($listado as $prodsuc){

                if(!isset($operaciones[$prodsuc->CodOper])){
                    $operaciones[$prodsuc->CodOper] = substr($prodsuc->operacion,4,50);
                    array_push($txtoper, "'$prodsuc->CodOper'");
                }

                if(!isset($cantidadprod[$prodsuc->coditem.$prodsuc->CodOper])){
                    $cantidadprod[$prodsuc->coditem.$prodsuc->CodOper] = 0;
                }

                $cantidadprod[$prodsuc->coditem.$prodsuc->CodOper] += $prodsuc->salidas;
                if(isset($prodsuc->descrip)){
                    $itemopei[$prodsuc->instancia][$prodsuc->coditem]['descrip']   = $prodsuc->descrip;
                    $itemopei[$prodsuc->instancia][$prodsuc->coditem]['exdecimal'] = $prodsuc->exdecimal;
                }

            }

        $operaciones  = [];
        if(isset($txtoper) and count($txtoper)>0){
            $txtoper = implode(',', $txtoper);

            $saoper  = Saoper::selectRaw('CodOper, descrip')->whereRaw("CodOper in ($txtoper)")->orderBy('id')->get();

            foreach ($saoper as  $oper){
                if(!isset($operaciones[$oper->CodOper])){
                    $operaciones[$oper->CodOper] = substr($oper->descrip,4,50);
                }
            }
        }

        $sucursales = Sasucursal::where('fk_comercial',$comercialid)->orderBy('descrip','asc')->get();

        return view('operacionesSucursal', compact('sucursal','fechasreport', 'sucursales', 'fk_sucursal', 'fecha1', 'fecha2', 'operaciones', 'itemopei', 'cantidadprod'));
    }


    public function busquedaHomeProd(Request $request)
    {
        $busqueda = $request->busqueda;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector ) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $comercial = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $productos = Saprod::where('comercial',$comercial)->whereRaw($cadena)->orderBy('updated_at','desc')->limit(60)->get();

        return view('layouts.ajaxbusqueda',compact('productos'))->render();
    }

    public function saprodsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $productos = $request->productos;
        $productos = json_decode($productos);

        if (isset($productos))
            foreach ($productos as $producto){
                $aux = Saprodsucursal::where(['codprod' => $producto->codprod, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprodsucursal();
                    $rel->codprod     = $producto->codprod;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $productos = Saprod::where('comercial',$comercial)
            ->whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->get()->take(50);

        return response()->json(['success'=>'success', 'newproductos' => $productos]);
    }

    public function productosinstsancias(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codinst     = $request->codinst;

        $listado = DB::table('saprod as a')
            ->select([
                'a.preciod',
                'a.descrip',
                'a.codprod',
                'e.codubic',
                'b.existen',
                'e.descrip as deposito'
            ])
            ->join('saexis as b', 'a.codprod', '=', 'b.codprod')
            ->join('sasucursal as c', 'b.fk_sucursal', '=', 'c.id')
            ->join('sadepo as e', 'b.codubic', '=', 'e.codubic')
            ->join('sainsta as d', 'd.codinst', '=', 'a.codinst')
            ->where('c.fk_comercial', $comercialid)
            ->where('a.codinst', $codinst)
            ->where('e.comercial', $comercialid)
            ->where('b.existen', '>', 0)
            ->orderBy('a.descrip')
            ->get();

        return response()->json(['success'=>'success', 'listado' => $listado]);

    }


    public function confirmarinventario(Request $request)
    {
        $sucursalid  = str_replace("300", "", $request->sucursal);
        $fecha       = Carbon::now()->format('Y-m-d');
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;

        $sqlcostoinv = "SELECT a.codprod,  sum(a.preciod) as preciod,  sum(a.costod) as costod,  sum(b.existen) as existen
						FROM   saprod a , saexis b
						WHERE  a.codprod   = b.codprod
                        and b.fk_sucursal  = $sucursalid
						and a.comercial    = $comercialid
                        group by b.codprod
								";

        $listado = DB::select($sqlcostoinv);

        foreach ($listado as $value) {
            $codprod = $value->codprod;
            $existen = $value->existen;
            $costod  = $value->costod;
            $preciod = $value->preciod;

            $record = Saeprdday::where(['codprod' => $codprod, 'fecha' => $fecha,'fksucursal' => $sucursalid])->first();
            if(isset($record) and isset($record->codprod) and $record->codprod != ''){

            }else{
                $record = new Saeprdday();
                $record->fecha      = $fecha;
                $record->fksucursal = $sucursalid;
                $record->codprod    = $codprod;
            }

            $record->costod  = $costod;
            $record->preciod = $preciod;
            $record->existen = $existen;
            $record->save();
        }

        return response()->json(['success'=>'success', 'done' => 1]);

    }


    public function productosinstsanciascodalte(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT
                    a.preciod,
                    a.descrip,
                    a.codprod,
                    e.codubic,
                    b.existen,
                    e.descrip as deposito
                FROM saprod a
                INNER JOIN saexis b ON a.codprod = b.codprod
                INNER JOIN sasucursal c ON b.fk_sucursal = c.id
                INNER JOIN sadepo e ON b.codubic = e.codubic
                INNER JOIN sainsta d ON d.codinst = a.codinst AND d.comercial = a.comercial
                WHERE c.fk_comercial = $comercialid
                    AND a.comercial = $comercialid
                    AND e.comercial = $comercialid
                    AND LEFT(d.codalte, $len) = '$codalte'
                    AND b.existen > 0
                    AND c.id = $sucursalid
                ORDER BY a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado, 'sqlcostoinv' => $sqlcostoinv]);

    }

    public function viewprodinstsanciascodalte(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $busqueda    = $request->busqueda;
        $len         = strlen($codalte);

        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'",  "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector ) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(a.codprod like '%$value%' or a.descrip like '%$value%' or a.refere like '%$value%' or a.marca like '%$value%' or a.descrip2 like '%$value%')";
                $numerito++;
            }
        }


        if($cadena!='') $cadena = " and ($cadena) ";

        $sqlcostoinv = "SELECT a.preciod, a.preciod, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								$cadena
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip'] = $producto->descrip;
            $productos[$producto->codprod]['preciod'] = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('productosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function listprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::with('sucursalapi')->whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubiccompany(Request $request)
    {
        $codprod    = $request->codprod;
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::with('deposito')
            ->whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubicinv(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function create()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->where('comercial',$comercial)
            ->orderBy('codalte','asc')->get();

        return view('product-create', compact('instancias' ) );
    }

    public function checkcodprod($codprod)
    {
        $check   = 0;
        $comercial = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        if($codprod != '')
            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial])->first();
            if(isset($product) and $product->codprod != '')
                $check = 0;
            else
                $check = 1;

        return response()->json(['check' => $check ]);
    }

    public function store(Request $request)
    {
        $comercial = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $newprod = new Saprod();
        $newprod->fill($request->all());
        $newprod->comercial = $comercial;
        $newprod->save();
        return redirect()->route('productos.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $producto   = Saprod::find($id);
        $comercial = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial',$comercial)
            ->orderBy('codalte','asc')->get();

        return view('product-edit', compact('instancias','producto', 'id'));
    }

    public function update(Request $request, $id)
    {
        $comercial = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $producto  = Saprod::find($id);
        $producto->fill($request->all());
        $producto->descrip = strtoupper($request->descrip);
        if(!$request->esexento)
            $producto->esexento = 0;
        if(!$request->exdecimal)
            $producto->exdecimal = 0;
        if(!$request->activo)
            $producto->activo = 0; //// luego ver como manejamos esto

        if(isset($request->preciod)) {
            $preciod = $request->preciod;
            $coma = substr_count($preciod, ',');
            $punto = substr_count($preciod, '.');

            if ($coma > 0 and $punto > 0) {
                $preciod = str_replace(".", '', $preciod);
                $preciod = str_replace(",", '.', $preciod);
            }
            if ($coma > 0 and !$punto)
                $preciod = str_replace(",", '.', $preciod);
            $producto->preciod = $preciod;
        }

        ////////////////////////////////////////////////////////////////////////////

        if(isset($request->costod)) {
            $costod = $request->costod;

            $coma = strpos($costod, ',');
            $punto = strpos($costod, '.');

            if ($coma > 0 and $punto > 0) {

                $costod = str_replace(".", '', $costod);
                $costod = str_replace(",", '.', $costod);
            }
            if ($coma > 0 and !$punto)
                $costod = str_replace(",", '.', $costod);

            $producto->costod = $costod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod2)) {
            $costod2 = $request->costod2;
            $coma = substr_count($costod2, ',');
            $punto = substr_count($costod2, '.');

            if ($coma > 0 and $punto > 0) {
                $costod2 = str_replace(".", '', $costod2);
                $costod2 = str_replace(",", '.', $costod2);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod2);
            $producto->costod2 = $costod2;
        }
        //////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod3)) {
            $costod3 = $request->costod3;
            $coma = substr_count($costod3, ',');
            $punto = substr_count($costod3, '.');

            if ($coma > 0 and $punto > 0) {
                $costod3 = str_replace(".", '', $costod3);
                $costod3 = str_replace(",", '.', $costod3);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod3);
            $producto->costod3 = $costod3;
        }
        ////////////////////////////////////////////////////////////////////////////


        $producto->save();

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();
        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $comercial)
                    $item->delete();
            }

        return redirect()->route('productos.edit',$id);
    }

    public function destroy($id)
    {
        //
    }
}
