@extends('layouts.master')
@section('title')
    Débito Fiscal por Sucursal
@endsection
@section('css')
    <style>
        .botoncal {
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover {
            font-size: 13px;
        }
        .kpi-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid #f95a02;
            height: 100%;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(249,90,2,0.2);
        }
        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            color: #f95a02;
            line-height: 1.2;
        }
        .kpi-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .kpi-sub {
            font-size: 11px;
            color: #a0aec0;
            margin-top: 4px;
        }
        .kpi-card.verde { border-left-color: #28a745; }
        .kpi-card.verde .kpi-value { color: #28a745; }
        .kpi-card.azul { border-left-color: #0072c5; }
        .kpi-card.azul .kpi-value { color: #0072c5; }
        .kpi-card.rojo { border-left-color: #dc3545; }
        .kpi-card.rojo .kpi-value { color: #dc3545; }
        .kpi-card.morado { border-left-color: #6f42c1; }
        .kpi-card.morado .kpi-value { color: #6f42c1; }

        .tdline {
            border: 1px solid #f95a02 !important;
        }
        .tdlineff {
            border-left: 1px solid #fff !important;
            color: white !important;
            background-color: #f95a02 !important;
        }
        .table-ranking tbody tr:hover {
            background-color: #fff8f3 !important;
        }
        .badge-ranking {
            display: inline-block;
            width: 26px;
            height: 26px;
            line-height: 26px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 12px;
            color: #fff;
        }
        .badge-1 { background: #f95a02; }
        .badge-2 { background: #0072c5; }
        .badge-3 { background: #28a745; }
        .badge-otro { background: #a0aec0; }
        .progress-sm {
            height: 6px;
            border-radius: 10px;
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-receipt-cutoff me-2"></i>
                        REPORTE DE DÉBITO FISCAL POR SUCURSAL
                    </h4>
                    <p class="text-white-50 mb-0 small">
                        @if($fecha1 != '')
                            <i class="bi bi-calendar-range me-2 text-white"></i>
                            {{$fecha1}} - {{$fecha2}}
                        @endif
                    </p>
                </div>
                <div class="card-body">
                    <form method="post" name="form1" id="form1" action="{{ route('reporte.debito.fiscal') }}">
                        @csrf
                        @method('POST')
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                                           placeholder="Seleccionar fechas">
                                    <div class="input-group-text bg-primary border-primary text-white">
                                        <button type="submit" class="botoncal">
                                            <i class="bi bi-search"></i> Consultar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" style="height:43px;"
                                        onclick="exportarExcel('tablaDebitoFiscal')">
                                    <i class="bi bi-file-excel"></i> Exportar a Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== KPIs PRINCIPALES ========== --}}
    <div class="row mt-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-label"><i class="bi bi-receipt me-1"></i> Facturas Emitidas</div>
                <div class="kpi-value">{{ number_format($totales['total_facturas'], 0, ',', '.') }}</div>
                <div class="kpi-sub">
                <span class="text-danger">
                    <i class="bi bi-arrow-return-left"></i>
                    {{ number_format($totales['total_devoluciones'], 0, ',', '.') }} devoluciones
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="kpi-card azul">
                <div class="kpi-label"><i class="bi bi-cash-stack me-1"></i> Total Facturado (Neto)</div>
                <div class="kpi-value">$ {{ number_format($totales['total_facturado_neto'], 2, ',', '.') }}</div>
                <div class="kpi-sub">
                    Base: $ {{ number_format($totales['base_gravable_neta'], 2, ',', '.') }}
                    &nbsp;|&nbsp;
                    Exento: $ {{ number_format($totales['exento_neto'], 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="kpi-card morado">
                <div class="kpi-label"><i class="bi bi-percent me-1"></i> Débito Fiscal (IVA Neto)</div>
                <div class="kpi-value">$ {{ number_format($totales['debito_fiscal_neto'], 2, ',', '.') }}</div>
                <div class="kpi-sub">
                    IVA facturado: $ {{ number_format($totales['iva_facturado'], 2, ',', '.') }}
                    <br>
                    <span class="text-danger">
                    - Devoluciones: $ {{ number_format($totales['iva_devoluciones'], 2, ',', '.') }}
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="kpi-card verde">
                <div class="kpi-label"><i class="bi bi-wallet2 me-1"></i> Contado / Crédito</div>
                <div class="kpi-value">$ {{ number_format($totales['contado_neto'], 2, ',', '.') }}</div>
                <div class="kpi-sub">
                    Crédito: $ {{ number_format($totales['credito_neto'], 2, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ========== GRÁFICA ========== --}}
    <div class="row mt-2">
        <div class="col-lg-7 mb-3">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        Débito Fiscal por Sucursal
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="graficaDebito"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">
                        <i class="bi bi-pie-chart-fill me-2"></i>
                        Participación en Débito Fiscal
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="graficaParticipacion"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TABLA DETALLADA ========== --}}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">
                        <i class="bi bi-table me-2"></i>
                        Detalle por Sucursal
                    </h4>
                    <small class="text-muted">Ordenado por Débito Fiscal (mayor a menor)</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped align-middle table-sm fs-13 mb-0 table-ranking"
                               id="tablaDebitoFiscal">
                            <thead class="text-muted table-light">
                            <tr>
                                <th width="40" class="tdlineff text-center">#</th>
                                <th class="tdlineff">Sucursal</th>
                                <th class="tdlineff text-center">Facturas</th>
                                <th class="tdlineff text-center">Devs</th>
                                <th class="tdlineff text-end">Total Facturado</th>
                                <th class="tdlineff text-end">Base Gravable</th>
                                <th class="tdlineff text-end">Exento</th>
                                <th class="tdlineff text-end">IVA Facturado</th>
                                <th class="tdlineff text-end">IVA Devuelto</th>
                                <th class="tdlineff text-end" style="background-color:#6f42c1 !important;">
                                    Débito Fiscal Neto
                                </th>
                                <th class="tdlineff text-center">%</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $rank = 1; @endphp
                            @foreach($resultados as $r)
                                @php
                                    $porc = ($totales['debito_fiscal_neto'] > 0)
                                        ? ($r->debito_fiscal_neto / $totales['debito_fiscal_neto']) * 100
                                        : 0;
                                    $badgeClass = $rank == 1 ? 'badge-1' : ($rank == 2 ? 'badge-2' : ($rank == 3 ? 'badge-3' : 'badge-otro'));
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <span class="badge-ranking {{ $badgeClass }}">{{ $rank }}</span>
                                    </td>
                                    <td>
                                        <i class="bi bi-building me-1 text-muted"></i>
                                        {{ $r->sucursal_nombre }}
                                    </td>
                                    <td class="text-center text-success fw-bold">
                                        {{ number_format($r->total_facturas, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center text-danger">
                                        {{ number_format($r->total_devoluciones, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        $ {{ number_format($r->total_facturado_neto, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        $ {{ number_format($r->base_gravable_neta, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        $ {{ number_format($r->exento_neto, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end text-success">
                                        $ {{ number_format($r->iva_facturado, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end text-danger">
                                        $ {{ number_format($r->iva_devoluciones, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold" style="color:#6f42c1; background:#faf5ff;">
                                        $ {{ number_format($r->debito_fiscal_neto, 2, ',', '.') }}
                                    </td>
                                    <td class="text-center" style="min-width:100px;">
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress progress-sm flex-grow-1" role="progressbar"
                                                 aria-valuenow="{{ $porc }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar" style="width: {{ $porc }}%; background:#6f42c1;"></div>
                                            </div>
                                            <small class="text-muted">{{ number_format($porc, 1) }}%</small>
                                        </div>
                                    </td>
                                </tr>
                                @php $rank++; @endphp
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr style="background:#f5f5f5; font-weight:bold;">
                                <td colspan="2" class="tdlineff">TOTALES</td>
                                <td class="text-center text-success">{{ number_format($totales['total_facturas'], 0, ',', '.') }}</td>
                                <td class="text-center text-danger">{{ number_format($totales['total_devoluciones'], 0, ',', '.') }}</td>
                                <td class="text-end">$ {{ number_format($totales['total_facturado_neto'], 2, ',', '.') }}</td>
                                <td class="text-end">$ {{ number_format($totales['base_gravable_neta'], 2, ',', '.') }}</td>
                                <td class="text-end">$ {{ number_format($totales['exento_neto'], 2, ',', '.') }}</td>
                                <td class="text-end text-success">$ {{ number_format($totales['iva_facturado'], 2, ',', '.') }}</td>
                                <td class="text-end text-danger">$ {{ number_format($totales['iva_devoluciones'], 2, ',', '.') }}</td>
                                <td class="text-end" style="color:#6f42c1; background:#f3e8ff;">
                                    $ {{ number_format($totales['debito_fiscal_neto'], 2, ',', '.') }}
                                </td>
                                <td class="text-center">100%</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TOP SUCURSALES (RANKING VISUAL) ========== --}}
    @if($resultados->count() > 0)
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-trophy-fill me-2 text-warning"></i>
                            Ranking: Sucursales con Mayor Débito Fiscal
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($resultados->take(3) as $idx => $top)
                                <div class="col-md-4 mb-3">
                                    <div class="kpi-card {{ $idx == 0 ? '' : ($idx == 1 ? 'azul' : 'verde') }}" style="text-align:center;">
                                        <div style="font-size: 40px; margin-bottom: 10px;">
                                            @if($idx == 0) 🥇
                                            @elseif($idx == 1) 🥈
                                            @else 🥉
                                            @endif
                                        </div>
                                        <h5 class="mb-1">{{ $top->sucursal_nombre }}</h5>
                                        <div class="kpi-value">$ {{ number_format($top->debito_fiscal_neto, 2, ',', '.') }}</div>
                                        <div class="kpi-sub">
                                            {{ number_format($top->total_facturas, 0, ',', '.') }} facturas |
                                            Total: $ {{ number_format($top->total_facturado_neto, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ========== GRÁFICA DE BARRAS ==========
        const ctxBar = document.getElementById('graficaDebito').getContext('2d');
        const sucursales = @json($graficaSucursales);
        const debitoData = @json($graficaDebito);
        const facturadoData = @json($graficaFacturado);

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: sucursales,
                datasets: [
                    {
                        label: 'Débito Fiscal (IVA)',
                        data: debitoData,
                        backgroundColor: 'rgba(111, 66, 193, 0.8)',
                        borderColor: 'rgba(111, 66, 193, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Facturado (Neto)',
                        data: facturadoData,
                        backgroundColor: 'rgba(0, 114, 197, 0.5)',
                        borderColor: 'rgba(0, 114, 197, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += '$ ' + context.parsed.y.toLocaleString('es-VE', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Débito Fiscal ($)',
                            color: '#6f42c1'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-VE');
                            },
                            color: '#6f42c1'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Total Facturado ($)',
                            color: '#0072c5'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-VE');
                            },
                            color: '#0072c5'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });

        // ========== GRÁFICA DE PARTICIPACIÓN (DOUGHNUT) ==========
        const ctxPie = document.getElementById('graficaParticipacion').getContext('2d');
        const colores = [
            '#f95a02', '#0072c5', '#28a745', '#6f42c1', '#ffc107',
            '#dc3545', '#17a2b8', '#fd7e14', '#20c997', '#e83e8c',
            '#6610f2', '#6c757d', '#343a40', '#007bff', '#28a745'
        ];

        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: sucursales,
                datasets: [{
                    data: debitoData,
                    backgroundColor: colores.slice(0, sucursales.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11 },
                            padding: 8,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const porc = total > 0 ? (context.parsed / total) * 100 : 0;
                                return context.label + ': $ ' +
                                    context.parsed.toLocaleString('es-VE', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }) +
                                    ' (' + porc.toFixed(1) + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
