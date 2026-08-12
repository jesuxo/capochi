<?php
// app/Http/Controllers/InventarioHistoricoController.php

namespace App\Http\Controllers;

use App\Models\Saeprdday;
use App\Models\Sasucursal;
use App\Models\Saprod;
use App\Models\Sainsta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioHistoricoController extends Controller
{
    // Panel principal
    public function index()
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid') ?? 1;

        $fechaHoy = Carbon::now()->format('Y-m-d');

        $sucursalesConSyncHoy = Saeprdday::where('fecha', $fechaHoy)
            ->select('fksucursal', DB::raw('COUNT(DISTINCT codprod) as total_productos'))
            ->with('sucursal')
            ->groupBy('fksucursal')
            ->get();

        $sucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->where('sincronizacion', 1)
            ->orderBy('descrip')
            ->get();

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')
            ->whereRaw("id in ($arraysucursales)")
            ->get();

        $ultimasSincronizaciones = Saeprdday::select('fksucursal', DB::raw('MAX(fecha) as ultima_fecha'))
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->groupBy('fksucursal')
            ->get()
            ->keyBy('fksucursal');

        $categorias = Sainsta::where('comercial', $comercialid)
            ->where('tipoins', 0)
            ->where('nivel', 1)
            ->orderBy('codalte')
            ->get();

        return view('inventario-historico', compact(
            'sucursales',
            'ultimasSincronizaciones',
            'sucursalesConSyncHoy',
            'categorias',
            'allsucursales',
            'fechaHoy'
        ));
    }

    public function calendarioSincronizacion()
    {
        $comercialid = session('comercialid') ?? 1;

        // Obtener sucursales del comercial actual
        $sucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->where('sincronizacion', 1)
            ->orderBy('descrip')
            ->get();

        return view('inventario-calendario', compact('sucursales'));
    }

    /**
     * Obtener datos del calendario de sincronizaciones para un período
     */
    public function getCalendarioSincronizacionData(Request $request)
    {
        try {
            $comercialid = session('comercialid') ?? 1;
            $mes = $request->input('mes', Carbon::now()->month);
            $anio = $request->input('anio', Carbon::now()->year);
            $sucursalId = $request->input('sucursal_id', 0);

            // Fechas del mes
            $fechaInicio = Carbon::create($anio, $mes, 1)->startOfDay();
            $fechaFin = Carbon::create($anio, $mes, 1)->endOfMonth()->endOfDay();

            // Obtener todas las fechas del mes (días)
            $diasDelMes = [];
            $fecha = clone $fechaInicio;
            while ($fecha <= $fechaFin) {
                $diasDelMes[] = $fecha->format('Y-m-d');
                $fecha->addDay();
            }

            // Obtener sucursales
            $sucursales = Sasucursal::where('fk_comercial', $comercialid)
                ->where('sincronizacion', 1);

            if ($sucursalId > 0) {
                $sucursales->where('id', $sucursalId);
            }

            $sucursales = $sucursales->orderBy('descrip')->get();

            // Obtener todas las sincronizaciones del período
            $sincronizaciones = Saeprdday::whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->whereHas('sucursal', function($q) use ($comercialid) {
                    $q->where('fk_comercial', $comercialid);
                })
                ->select('fksucursal', 'fecha', DB::raw('COUNT(DISTINCT codprod) as total_productos'))
                ->groupBy('fksucursal', 'fecha')
                ->get()
                ->groupBy('fksucursal');

            // Construir matriz de datos
            $data = [];
            $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $diasSemanaCorto = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

            foreach ($sucursales as $sucursal) {
                $diaDescanso = $sucursal->dia_descanso ?? 0; // 0 = Domingo por defecto
                $nombreDiaDescanso = $diasSemana[$diaDescanso];

                $sucursalData = [
                    'id' => $sucursal->id,
                    'nombre' => $sucursal->descrip,
                    'dia_descanso' => $diaDescanso,
                    'nombre_dia_descanso' => $nombreDiaDescanso,
                    'dias' => [],
                    'totales' => [
                        'dias_habiles' => 0,
                        'dias_sincronizados' => 0,
                        'dias_descanso' => 0,
                        'cumplimiento' => 0
                    ]
                ];

                $diasHabiles = 0;
                $diasSincronizados = 0;
                $diasDescanso = 0;

                $syncsPorSucursal = $sincronizaciones->get($sucursal->id, collect())->keyBy('fecha');

                foreach ($diasDelMes as $fechaStr) {
                    $fechaObj = Carbon::parse($fechaStr);
                    $diaNumero = $fechaObj->format('d');
                    $diaSemana = $diasSemanaCorto[$fechaObj->dayOfWeek];
                    $esDiaDescanso = $fechaObj->dayOfWeek === $diaDescanso;
                    $esDiaHabil = !$esDiaDescanso;

                    // Verificar si hay sincronización
                    $tieneSync = $syncsPorSucursal->has($fechaStr);
                    $productosSync = $tieneSync ? $syncsPorSucursal[$fechaStr]->total_productos : 0;

                    // Contabilizar días
                    if ($esDiaDescanso) {
                        $diasDescanso++;
                    } else {
                        $diasHabiles++;
                        if ($tieneSync) {
                            $diasSincronizados++;
                        }
                    }

                    $sucursalData['dias'][] = [
                        'fecha' => $fechaStr,
                        'dia_numero' => $diaNumero,
                        'dia_semana' => $diaSemana,
                        'es_dia_habilitado' => $esDiaHabil,
                        'es_dia_descanso' => $esDiaDescanso,
                        'tiene_sincronizacion' => $tieneSync,
                        'productos_sincronizados' => $productosSync
                    ];
                }

                // Calcular cumplimiento
                $cumplimiento = $diasHabiles > 0 ? round(($diasSincronizados / $diasHabiles) * 100, 1) : 0;
                $sucursalData['totales'] = [
                    'dias_habiles' => $diasHabiles,
                    'dias_sincronizados' => $diasSincronizados,
                    'dias_descanso' => $diasDescanso,
                    'cumplimiento' => $cumplimiento,
                    'dias_faltantes' => $diasHabiles - $diasSincronizados
                ];

                $data[] = $sucursalData;
            }

            return response()->json([
                'success' => true,
                'mes' => $mes,
                'anio' => $anio,
                'nombre_mes' => Carbon::create($anio, $mes, 1)->translatedFormat('F'),
                'dias_del_mes' => $diasDelMes,
                'sucursales' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en getCalendarioSincronizacionData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }

    public function getSincronizacionPorFecha(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;
        $fecha = $request->fecha;

        // Obtener todas las sucursales
        $sucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->where('sincronizacion', 1)
            ->orderBy('descrip')
            ->get();

        // Obtener sincronizaciones de la fecha seleccionada
        $syncsFecha = Saeprdday::where('fecha', $fecha)
            ->select('fksucursal', DB::raw('COUNT(DISTINCT codprod) as total_productos'))
            ->groupBy('fksucursal')
            ->get()
            ->keyBy('fksucursal');

        // Obtener últimas sincronizaciones de cada sucursal
        $ultimasSyncs = Saeprdday::select('fksucursal', DB::raw('MAX(fecha) as ultima_fecha'))
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->groupBy('fksucursal')
            ->get()
            ->keyBy('fksucursal');

        $resultado = [];
        $sincronizadas = 0;

        foreach ($sucursales as $sucursal) {
            $tieneSync = $syncsFecha->has($sucursal->id);
            $ultimaSync = $ultimasSyncs[$sucursal->id] ?? null;

            if ($tieneSync) {
                $sincronizadas++;
            }

            $resultado[] = [
                'id' => $sucursal->id,
                'descrip' => $sucursal->descrip,
                'sincronizado' => $tieneSync,
                'total_productos' => $tieneSync ? $syncsFecha[$sucursal->id]->total_productos : 0,
                'ultima_fecha' => $ultimaSync ? Carbon::parse($ultimaSync->ultima_fecha)->format('d/m/Y') : null,
                'dias_desde' => $ultimaSync ? Carbon::parse($ultimaSync->ultima_fecha)->diffInDays(now()) : null
            ];
        }

        $totalSucursales = $sucursales->count();
        $porcentajeSync = $totalSucursales > 0 ? round(($sincronizadas / $totalSucursales) * 100) : 0;

        return response()->json([
            'success' => true,
            'fecha' => $fecha,
            'fecha_formateada' => Carbon::parse($fecha)->format('d/m/Y'),
            'total_sucursales' => $totalSucursales,
            'sincronizadas' => $sincronizadas,
            'no_sincronizadas' => $totalSucursales - $sincronizadas,
            'porcentaje_sync' => $porcentajeSync,
            'sucursales' => $resultado
        ]);
    }

    public function getInventarioPorFecha(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;
        $fecha = $request->fecha;
        $fksucursal = $request->fksucursal;

        $query = Saeprdday::with(['producto', 'sucursal'])
            ->where('fecha', $fecha)
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            });

        if ($fksucursal && $fksucursal > 0) {
            $query->where('fksucursal', $fksucursal);
        }

        $inventarios = $query->orderBy('codprod')->get();

        $resumen = [
            'total_productos' => $inventarios->count(),
            'total_unidades' => $inventarios->sum('existen'),
            'total_valor' => $inventarios->sum(function($item) {
                return $item->existen * ($item->preciod ?? 0);
            }),
            'sucursales_involucradas' => $inventarios->groupBy('fksucursal')->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $inventarios,
            'resumen' => $resumen
        ]);
    }

    // NUEVO: Obtener inventario en formato matriz (productos vs sucursales)
    public function getInventarioMatriz(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;
        $fecha = $request->fecha;
        $categoria = $request->categoria ?? null;

        $sucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->where('sincronizacion', 1)
            ->orderBy('descrip')
            ->get();

        $query = Saeprdday::with(['producto', 'sucursal'])
            ->where('fecha', $fecha)
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            });

        if ($categoria) {
            $query->whereHas('producto', function($q) use ($categoria) {
                $q->where('codinst', $categoria);
            });
        }

        $inventarios = $query->get();

        $matriz = [];
        $totalesPorSucursal = [];

        foreach ($sucursales as $sucursal) {
            $totalesPorSucursal[$sucursal->id] = [
                'nombre' => $sucursal->descrip,
                'unidades' => 0,
                'valor' => 0
            ];
        }

        foreach ($inventarios as $item) {
            $codprod = $item->codprod;
            $sucursalId = $item->fksucursal;

            if (!isset($matriz[$codprod])) {
                $matriz[$codprod] = [
                    'codprod' => $codprod,
                    'descrip' => $item->producto->descrip ?? 'N/A',
                    'categoria' => $item->producto->instancia->descrip ?? 'N/A',
                    'sucursales' => []
                ];
                foreach ($sucursales as $sucu) {
                    $matriz[$codprod]['sucursales'][$sucu->id] = 0;
                }
            }

            $existen = $item->existen;
            $valor = $existen * ($item->preciod ?? 0);

            $matriz[$codprod]['sucursales'][$sucursalId] = $existen;
            $totalesPorSucursal[$sucursalId]['unidades'] += $existen;
            $totalesPorSucursal[$sucursalId]['valor'] += $valor;
        }

        uasort($matriz, function($a, $b) {
            return strcmp($a['descrip'], $b['descrip']);
        });

        $totalGeneralUnidades = array_sum(array_column($totalesPorSucursal, 'unidades'));
        $totalGeneralValor = array_sum(array_column($totalesPorSucursal, 'valor'));

        return response()->json([
            'success' => true,
            'matriz' => array_values($matriz),
            'sucursales' => $sucursales,
            'totales_sucursales' => $totalesPorSucursal,
            'total_general_unidades' => $totalGeneralUnidades,
            'total_general_valor' => $totalGeneralValor,
            'total_productos' => count($matriz)
        ]);
    }

    // NUEVO: Comparar inventarios entre dos fechas
    public function compararInventarios(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;

        $query1 = Saeprdday::where('fecha', $fecha1);
        $query2 = Saeprdday::where('fecha', $fecha2);

        if ($request->fksucursal && $request->fksucursal > 0) {
            $query1->where('fksucursal', $request->fksucursal);
            $query2->where('fksucursal', $request->fksucursal);
        }

        $inventario1 = $query1->get()->keyBy('codprod');
        $inventario2 = $query2->get()->keyBy('codprod');

        $todosCodigos = $inventario1->keys()->merge($inventario2->keys())->unique();

        $comparacion = [];
        foreach ($todosCodigos as $codprod) {
            $prod1 = $inventario1->get($codprod);
            $prod2 = $inventario2->get($codprod);

            $existen1 = $prod1->existen ?? 0;
            $existen2 = $prod2->existen ?? 0;
            $diferencia = $existen2 - $existen1;
            $variacion = $existen1 > 0 ? ($diferencia / $existen1) * 100 : ($diferencia != 0 ? 100 : 0);

            $comparacion[] = [
                'codprod' => $codprod,
                'descrip' => $prod1->producto->descrip ?? ($prod2->producto->descrip ?? 'N/A'),
                'existen_fecha1' => $existen1,
                'existen_fecha2' => $existen2,
                'diferencia' => $diferencia,
                'variacion_porcentaje' => round($variacion, 2),
                'costo_unitario' => $prod1->preciod ?? ($prod2->preciod ?? 0)
            ];
        }

        return response()->json([
            'success' => true,
            'comparacion' => $comparacion,
            'resumen' => [
                'total_productos' => count($comparacion),
                'total_unidades_f1' => collect($comparacion)->sum('existen_fecha1'),
                'total_unidades_f2' => collect($comparacion)->sum('existen_fecha2'),
                'diferencia_total' => collect($comparacion)->sum('diferencia'),
                'valor_total_f2' => collect($comparacion)->sum(function($item) {
                    return $item['existen_fecha2'] * $item['costo_unitario'];
                })
            ]
        ]);
    }

    public function getCategoriasTree(Request $request)
    {
        try {
            $comercialid = session('comercialid') ?? 1;
            $fksucursal = $request->fksucursal ?? 0;

            // Si no hay sucursal seleccionada, retornar array vacío pero con éxito
            if ($fksucursal == 0) {
                return response()->json([
                    'success' => true,
                    'categorias_tree' => [],
                    'message' => 'Seleccione una sucursal para ver las categorías'
                ]);
            }

            $datasucu = "";
            if ($fksucursal > 0) {
                $datasucu = " and c.fk_sucursal = $fksucursal";
            }

            // Obtener solo instancias que tienen productos con existencias
            $instancias = Sainsta::where('comercial', $comercialid)
                ->where('tipoins', 0)
                ->orderBy('codalte', 'asc')
                ->get();

            $instanciasInfo = [];
            $instanciasPorPadre = [];

            foreach ($instancias as $inst) {
                $codalte = $inst->codalte;
                $len = strlen($codalte);

                $sqlcostoinv = "
                SELECT
                    LEFT(b.codalte, $len) AS codigo_prefijo,
                    SUM(c.Existen) AS existen,
                    SUM(a.preciod * c.Existen) AS preciod
                FROM saprod a
                INNER JOIN sainsta b ON a.CodInst = b.CodInst
                    AND b.tipoins = 0
                    AND b.comercial = $comercialid
                    AND LEFT(b.codalte, $len) = '$codalte'
                INNER JOIN saexis c ON a.codprod = c.codprod $datasucu
                INNER JOIN sasucursal d ON c.fk_sucursal = d.id
                    AND d.fk_comercial = $comercialid
                WHERE a.comercial = $comercialid
                GROUP BY LEFT(b.codalte, $len)
            ";

                $costoinven = DB::select($sqlcostoinv);

                if (isset($costoinven[0]) && $costoinven[0]->existen != 0 && $costoinven[0]->existen > 0) {
                    $instanciasInfo[$inst->codinst] = [
                        'codinst'  => $inst->codinst,
                        'codalte'  => $codalte,
                        'label'    => $inst->label ?? $inst->descrip,
                        'descrip'  => $inst->descrip,
                        'nivel'    => $inst->nivel,
                        'insPadre' => $inst->insPadre,
                        'existen'  => (float)$costoinven[0]->existen,
                        'preciod'  => (float)$costoinven[0]->preciod,
                        'hijos'    => []
                    ];

                    if (!isset($instanciasPorPadre[$inst->insPadre])) {
                        $instanciasPorPadre[$inst->insPadre] = [];
                    }
                    $instanciasPorPadre[$inst->insPadre][] = $inst->codinst;
                }
            }

            function buildTree($parentId, $instanciasInfo, $instanciasPorPadre) {
                $tree = [];
                if (isset($instanciasPorPadre[$parentId])) {
                    foreach ($instanciasPorPadre[$parentId] as $childId) {
                        if (isset($instanciasInfo[$childId])) {
                            $node = $instanciasInfo[$childId];
                            $node['hijos'] = buildTree($childId, $instanciasInfo, $instanciasPorPadre);
                            $tree[] = $node;
                        }
                    }
                }
                return $tree;
            }

            $categoriasTree = buildTree(0, $instanciasInfo, $instanciasPorPadre);

            return response()->json([
                'success' => true,
                'categorias_tree' => $categoriasTree,
                'total_categorias' => count($categoriasTree),
                'fksucursal' => $fksucursal
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en getCategoriasTree: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar categorías: ' . $e->getMessage(),
                'categorias_tree' => []
            ]);
        }
    }

    public function getEvolucionTemporal(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $evolucionDiaria = Saeprdday::select(
            DB::raw('fecha'),
            DB::raw('SUM(existen) as total_unidades'),
            DB::raw('SUM(existen * COALESCE(preciod, 0)) as total_valor'),
            DB::raw('COUNT(DISTINCT codprod) as total_productos')
        )
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $tieneDatos = $evolucionDiaria->count() > 0;

        $lineChartData = [
            'fechas' => $tieneDatos ? $evolucionDiaria->pluck('fecha') : collect([]),
            'unidades' => $tieneDatos ? $evolucionDiaria->pluck('total_unidades') : collect([]),
            'valores' => $tieneDatos ? $evolucionDiaria->pluck('total_valor') : collect([])
        ];

        $barrasSucursales = [];
        if ($tieneDatos) {
            $ultimaFecha = $evolucionDiaria->last()->fecha;
            $datosUltimaFecha = Saeprdday::select(
                'fksucursal',
                DB::raw('SUM(existen) as total_unidades')
            )
                ->where('fecha', $ultimaFecha)
                ->whereHas('sucursal', function($q) use ($comercialid) {
                    $q->where('fk_comercial', $comercialid);
                })
                ->with('sucursal')
                ->groupBy('fksucursal')
                ->get();

            foreach ($datosUltimaFecha as $item) {
                $barrasSucursales[] = [
                    'sucursal' => $item->sucursal->descrip,
                    'unidades' => $item->total_unidades
                ];
            }
        }

        // CORRECCIÓN: Usar DB::select con la consulta completa para evitar problemas con alias
        $productosData = DB::select("
        SELECT
            codprod,
            (MAX(existen) - MIN(existen)) as variacion,
            AVG(existen) as promedio
        FROM saeprdday
        WHERE fecha BETWEEN ? AND ?
        AND EXISTS (
            SELECT 1 FROM sasucursal
            WHERE saeprdday.fksucursal = sasucursal.id
            AND sasucursal.fk_comercial = ?
        )
        GROUP BY codprod
        HAVING (MAX(existen) - MIN(existen)) > 0
        ORDER BY ABS(MAX(existen) - MIN(existen)) DESC
        LIMIT 10
    ", [$fechaInicio, $fechaFin, $comercialid]);

        foreach ($productosData as $prod) {
            $producto = Saprod::where('codprod', $prod->codprod)
                ->where('comercial', $comercialid)
                ->first();
            $prod->descrip = $producto->descrip ?? $prod->codprod;
        }

        $resumenPeriodo = [
            'fecha_inicial' => $tieneDatos ? $evolucionDiaria->first()->fecha : null,
            'fecha_final' => $tieneDatos ? $evolucionDiaria->last()->fecha : null,
            'unidad_inicial' => $tieneDatos ? ($evolucionDiaria->first()->total_unidades ?? 0) : 0,
            'unidad_final' => $tieneDatos ? ($evolucionDiaria->last()->total_unidades ?? 0) : 0,
            'variacion_unidades' => 0,
            'variacion_porcentaje' => 0
        ];

        if ($tieneDatos && $resumenPeriodo['unidad_inicial'] > 0) {
            $resumenPeriodo['variacion_unidades'] = $resumenPeriodo['unidad_final'] - $resumenPeriodo['unidad_inicial'];
            $resumenPeriodo['variacion_porcentaje'] = ($resumenPeriodo['variacion_unidades'] / $resumenPeriodo['unidad_inicial']) * 100;
        }

        return response()->json([
            'success' => true,
            'evolucion_diaria' => $evolucionDiaria,
            'line_chart' => $lineChartData,
            'top_productos' => $productosData,
            'barras_sucursales' => $barrasSucursales,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'resumen_periodo' => $resumenPeriodo,
            'tiene_datos' => $tieneDatos
        ]);
    }

    // Dashboard con gráficas
    public function dashboard(Request $request)
    {
        $comercialid = session('comercialid') ?? 1;

        $sucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->where('sincronizacion', 1)
            ->orderBy('descrip')
            ->get();

        $fecha_fin = Carbon::now();
        $fecha_inicio = Carbon::now()->subDays(30);

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fecha_inicio = Carbon::parse($request->fecha_inicio);
            $fecha_fin = Carbon::parse($request->fecha_fin);
        }

        $query = Saeprdday::whereBetween('fecha', [$fecha_inicio, $fecha_fin])
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            });

        if ($request->filled('fksucursal') && $request->fksucursal > 0) {
            $query->where('fksucursal', $request->fksucursal);
        }

        $evolucionTotal = $query->select(
            DB::raw('DATE(fecha) as fecha'),
            DB::raw('SUM(existen) as total_unidades'),
            DB::raw('SUM(existen * COALESCE(preciod, 0)) as total_valor')
        )
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('fecha')
            ->get();

        $topProductos = Saeprdday::select(
            'codprod',
            DB::raw('MAX(existen) - MIN(existen) as variacion_absoluta'),
            DB::raw('AVG(existen) as promedio')
        )
            ->whereBetween('fecha', [$fecha_inicio, $fecha_fin])
            ->whereHas('sucursal', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->groupBy('codprod')
            ->orderByRaw('ABS(MAX(existen) - MIN(existen)) DESC')
            ->limit(10)
            ->get();

        foreach ($topProductos as $producto) {
            $prodInfo = Saprod::where('codprod', $producto->codprod)
                ->where('comercial', $comercialid)
                ->first();
            $producto->descrip = $prodInfo->descrip ?? $producto->codprod;
        }

        return view('inventario-dashboard', compact(
            'sucursales',
            'evolucionTotal',
            'topProductos',
            'fecha_inicio',
            'fecha_fin'
        ));
    }
}
