@extends('layouts.master')
@section('title', 'Reporte de Mermas por Sucursal')
@section('css')
    <style>
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .report-table {
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
        }
        .report-table th {
            background-color: #61885f;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #0056a3;
        }
        .report-table td {
            padding: 6px;
            border: 1px solid #dee2e6;
        }
        .report-table tr:hover {
            background-color: #f8f9fa;
        }
        .tdline {
            border: 1px solid #61885f !important;
            font-size: 12px;
        }
        .tdlineff {
            border-left: 1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #61885f !important;
        }
        .btn-consultar {
            background-color: #61885f;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-consultar:hover {
            background-color: #4a6b48;
        }
        .merma-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .merma-badge.merma-alta {
            background-color: #dc3545;
            color: white;
        }
        .merma-badge.merma-media {
            background-color: #ffc107;
            color: black;
        }
        .merma-badge.merma-baja {
            background-color: #28a745;
            color: white;
        }
        .totales-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .totales-row td {
            border-top: 2px solid #61885f !important;
        }
        .select-sm {
            font-size: 12px;
            padding: 4px 8px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Reporte de Mermas por Sucursal</h4>
                    <p class="text-white-50 mb-0 small">
                        @if($fecha1 ?? '')
                            DESDE {{$fecha1}} HASTA {{$fecha2}}
                        @endif
                    </p>
                </div>
                <div class="card-body">
                    <form method="post" name="form1" id="form1" action="{{ route('mermas.sucursales') }}">
                        <div class="row filter-section">
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Sucursal</label>
                                <select class="form-select" onChange="$('#form1').submit()" id="idsucu" name="fksucursal">
                                    <option value="" {{($fksucursal=='' or $fksucursal==0)?'selected':''}}>Todas las Sucursales</option>
                                    @foreach($allsucursales as $sucu)
                                        <option value="{{$sucu->id}}" {{($sucu->id == $fksucursal)?'selected':''}}>
                                            {{ $sucu->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" onChange="$('#form1').submit()" name="codinst">
                                    <option value="" {{($codinst=='' or $codinst==0)?'selected':''}}>Todas las Categorías</option>
                                    @foreach($instancias as $instancia)
                                        <option value="{{$instancia->codinst}}" {{($instancia->codinst == $codinst)?'selected':''}}>
                                            {{ str_repeat('--', $instancia->nivel-1) }} {{ $instancia->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="form-label">Tipo de Merma</label>
                                <select class="form-select" onChange="$('#form1').submit()" name="codoper">
                                    <option value="" {{($codoper=='' or $codoper==0)?'selected':''}}>Todas las Mermas</option>
                                    @foreach($operacionesMerma as $oper)
                                        <option value="{{$oper->codoper}}" {{($oper->codoper == $codoper)?'selected':''}}>
                                            {{ $oper->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Período</label>
                                <input type="text" class="form-control" id="fechasreport" data-provider="flatpickr"
                                       data-range-date="true" data-date-format="d/m/Y"
                                       placeholder="Seleccionar fechas" name="fechasreport"
                                       readonly="readonly" value="{{$fechasreport ?? ''}}">
                            </div>

                            <div class="col-md-2 mb-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="bi bi-search me-1"></i> Consultar
                                </button>
                                <button type="button" class="btn btn-success ms-2 w-100" onclick="exportarExcel()">
                                    <i class="bi bi-file-excel me-1"></i> Excel
                                </button>
                            </div>
                            @csrf
                            @method('POST')
                        </div>
                    </form>

                    @if(!empty($sucursales))
                        <div class="table-responsive">
                            <table class="report-table" id="tablaMermas">
                                <thead>
                                <tr>
                                    <th rowspan="2" style="min-width: 80px;">Código</th>
                                    <th rowspan="2" style="min-width: 200px;">Producto</th>
                                    <th rowspan="2" style="min-width: 150px;">Categoría</th>
                                    @foreach($sucursales as $idSuc => $nombreSuc)
                                        <th class="tdlineff titulo" style="min-width: 80px;">
                                            {{ $nombreSuc }}
                                        </th>
                                    @endforeach
                                    @if(count($sucursales) > 1)
                                        <th rowspan="2" style="min-width: 80px;">Total</th>
                                    @endif
                                </tr>
                                <tr>
                                    @foreach($sucursales as $idSuc => $nombreSuc)
                                        <th style="font-size: 10px; background-color: #4a6b48;">Cantidad</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $totalGeneral = 0;
                                    $totalesSucursal = [];
                                    foreach($sucursales as $idSuc => $nombreSuc) {
                                        $totalesSucursal[$idSuc] = 0;
                                    }
                                    $n = 0;
                                @endphp

                                @forelse($itemmermas as $categoria => $productos)
                                    <tr style="background-color: #f5f5f5;">
                                        <td colspan="2" class="tdline" style="font-weight: bold;">
                                            <i class="bi bi-folder me-1"></i> {{ $categoria }}
                                        </td>
                                        @foreach($sucursales as $idSuc => $nombreSuc)
                                            <td class="tdline"></td>
                                        @endforeach
                                        @if(count($sucursales) > 1)
                                            <td class="tdline"></td>
                                        @endif
                                    </tr>

                                    @foreach($productos as $codprod => $producto)
                                        @php
                                            $exdecimal = $producto['exdecimal'] ?? 0;
                                            $n++;
                                            $totalProducto = 0;
                                        @endphp
                                        <tr @if(($n%2)!=0) style="background-color: #f8f9fa;" @endif>
                                            <td class="tdline">{{ $codprod }}</td>
                                            <td class="tdline">{{ $producto['descrip'] }}</td>
                                            <td class="tdline">{{ $categoria }}</td>

                                            @foreach($sucursales as $idSuc => $nombreSuc)
                                                @php
                                                    $key = $codprod . $idSuc;
                                                    $cantidad = $cantidadprod[$key] ?? 0;
                                                    $totalProducto += $cantidad;
                                                    $totalesSucursal[$idSuc] += $cantidad;

                                                    $badgeClass = 'merma-baja';
                                                    if ($cantidad > 50) {
                                                        $badgeClass = 'merma-alta';
                                                    } elseif ($cantidad > 20) {
                                                        $badgeClass = 'merma-media';
                                                    }
                                                @endphp
                                                <td class="tdline text-center">
                                                    @if($cantidad > 0)
                                                        <span class="merma-badge {{ $badgeClass }}">
                                                            {{ ($exdecimal) ? number_format($cantidad, 3, ',', '.') : number_format($cantidad, 0, ',', '.') }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach

                                            @if(count($sucursales) > 1)
                                                <td class="tdline text-center fw-bold">
                                                    @if($totalProducto > 0)
                                                        {{ ($exdecimal) ? number_format($totalProducto, 3, ',', '.') : number_format($totalProducto, 0, ',', '.') }}
                                                        @php $totalGeneral += $totalProducto; @endphp
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="{{ count($sucursales) + 3 }}" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <h5>No hay registros de mermas</h5>
                                            <p class="text-muted">No se encontraron operaciones de merma para el período seleccionado.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                                @if(!empty($itemmermas))
                                    <tfoot>
                                    <tr class="totales-row">
                                        <td colspan="3" class="text-end fw-bold">TOTAL MERMAS</td>
                                        @foreach($sucursales as $idSuc => $nombreSuc)
                                            <td class="text-center fw-bold">
                                                {{ number_format($totalesSucursal[$idSuc], 0, ',', '.') }}
                                            </td>
                                        @endforeach
                                        @if(count($sucursales) > 1)
                                            <td class="text-center fw-bold">
                                                {{ number_format($totalGeneral, 0, ',', '.') }}
                                            </td>
                                        @endif
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Nota:</strong> Se muestran solo las operaciones de descargo (TipoOpI = 'P') que están marcadas como merma en la tabla SAOPER (merma = 1).
                                        Los colores indican la severidad:
                                        <span class="merma-badge merma-baja">Baja (&lt;20)</span>
                                        <span class="merma-badge merma-media">Media (20-50)</span>
                                        <span class="merma-badge merma-alta">Alta (&gt;50)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function exportarExcel() {
            const tabla = document.getElementById('tablaMermas');
            if (!tabla) {
                alert('No hay datos para exportar');
                return;
            }
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(tabla);
            XLSX.utils.book_append_sheet(wb, ws, 'Mermas');
            XLSX.writeFile(wb, 'reporte_mermas.xlsx');
        }

        // Inicializar Flatpickr manualmente si no se inicializa automáticamente
        $(document).ready(function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr("#fechasreport", {
                    mode: "range",
                    dateFormat: "d/m/Y",
                    locale: "es",
                    allowInput: true
                });
            }
        });
    </script>
@endsection
