{{-- resources/views/inventario-historico.blade.php --}}
@extends('layouts.master')
@section('title', 'Panel de Inventarios Históricos')

@section('css')
    <style>
        /* Estilos base del panel anterior */
        .summary-card {
            background: linear-gradient(135deg, #75a373 0%, #75a373 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .summary-number {
            font-size: 24px;
            font-weight: bold;
        }

        .summary-label {
            font-size: 12px;
            opacity: 0.9;
        }

        .filter-header {
            background: linear-gradient(135deg, #75a373 0%, #75a373 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 12px 15px;
        }

        .table-inventory {
            margin-bottom: 0;
        }

        .table-inventory th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-custom {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 20px;
        }

        /* Estilos nuevos para sincronización */
        .sync-compact {
            background: white;
            border-radius: 8px;
            padding: 0;
            margin-bottom: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .sync-header {
            background: linear-gradient(135deg, #75a373 0%, #75a373 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sync-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #75a373;
        }

        .sync-table {
            width: 100%;
            font-size: 13px;
        }

        .sync-table th {
            background: #f8f9fa;
            padding: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .sync-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-success { background-color: #75a373; box-shadow: 0 0 5px #75a373; }
        .status-warning { background-color: #ffc107; }
        .status-danger { background-color: #dc3545; }

        .btn-capture {
            padding: 4px 8px;
            font-size: 12px;
        }

        .sync-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .sync-card:hover {
            background-color: rgba(117, 163, 115, 0.05) !important;
        }
        .sync-today {
            background-color: rgba(117, 163, 115, 0.1) !important;
            border-left: 3px solid #75a373 !important;
        }
        .trend-up { color: #75a373; }
        .trend-down { color: #dc3545; }

        .matriz-table { font-size: 12px; }
        .matriz-table th, .matriz-table td {
            white-space: nowrap;
            padding: 8px 6px;
        }
        .matriz-table th {
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .total-col { background: #e9ecef; font-weight: bold; }
        .table-responsive-matriz {  overflow: auto; }

        .date-selector {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .sucursal-col {
            transition: all 0.2s ease;
        }

        .sucursal-col:hover {
            background-color: rgba(117, 163, 115, 0.1) !important;
        }

        .table-primary {
            background-color: rgba(117, 163, 115, 0.2) !important;
        }

        .producto-row {
            transition: background-color 0.2s ease;
        }

        .producto-row:hover {
            background-color: #f8f9fa;
        }

        #resumenSucursalesMatriz .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        #resumenSucursalesMatriz .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .screenshot-notification {
            position: fixed !important;
            top: 80px !important;
            right: 20px !important;
            z-index: 99999 !important;
            animation: slideInRight 0.3s ease;
        }

        .modal {
            z-index: 1050;
        }
        .modal-backdrop {
            z-index: 1040;
        }

        .accordion-button:not(.collapsed) {
            color: #fff !important;
            background-color: #75a373 !important;
        }
        .accordion-button:not(.collapsed)::after {
            filter: brightness(0) invert(1) !important;
        }
        /* Estilos para el árbol de categorías */
        .categoria-tree-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .categoria-tree-item:hover {
            background-color: rgba(13, 110, 253, 0.05) !important;
        }
        .categoria-tree-item.active {
            background-color: rgba(117, 163, 115, 0.15) !important;
            border-left: 3px solid #75a373;
        }
        .toggle-icon-cat {
            cursor: pointer;
            display: inline-block;
            width: 20px;
            margin-right: 5px;
            transition: transform 0.2s ease;
        }
        .toggle-icon-cat.collapsed {
            transform: rotate(-90deg);
        }
        .child-row-cat td:first-child {
            padding-left: 30px;
        }
        .level-2-cat td:first-child { padding-left: 50px; }
        .level-3-cat td:first-child { padding-left: 70px; }
        .level-4-cat td:first-child { padding-left: 90px; }

        .btn-group .btn-outline-secondary {
            transition: all 0.2s ease;
        }

        .btn-group .btn-outline-secondary:hover {
            background-color: #75a373;
            border-color: #75a373;
            color: white;
        }

        /* Estilo para los atajos de teclado */
        kbd {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 12px;
            font-family: monospace;
            box-shadow: 0 1px 0 rgba(0,0,0,0.2);
            display: inline-block;
            margin: 0 2px;
        }

        /* Estilo para los botones flotantes */
        #floatingNavSync .btn-group-vertical {
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }

        #floatingNavSync .btn {
            border-radius: 0;
            padding: 10px 15px;
        }

        #floatingNavSync .btn:first-child {
            border-radius: 10px 10px 0 0;
        }

        #floatingNavSync .btn:last-child {
            border-radius: 0 0 10px 10px;
        }
        .producto-row {
            transition: all 0.2s ease;
        }

        .producto-row:hover {
            background-color: rgba(117, 163, 115, 0.05) !important;
        }

        .btn-group-sm .btn {
            transition: all 0.2s ease;
        }

        .btn-group-sm .btn:hover {
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-none">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Panel de Control de Inventarios
                    </h4>
                    <p class="text-white-50 mb-0 small">Gestión y comparación de inventarios históricos</p>
                </div>
                <div class="card-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs mb-4" id="inventarioTabs" role="tablist">
                        @if(session('comercialid')== 1 or session('comercialid') == 5)
                        <li class="nav-item active">
                            <button class="nav-link active" id="sincronizacion-tab" data-bs-toggle="tab"
                                    data-bs-target="#sincronizacion" type="button" role="tab">
                                <i class="bi bi-cloud-check me-1"></i> Sincronización
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="matriz-tab" data-bs-toggle="tab"
                                    data-bs-target="#matriz" type="button" role="tab">
                                <i class="bi bi-table me-1"></i> Inventario por Sucursal
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="graficas-tab" data-bs-toggle="tab"
                                    data-bs-target="#graficas" type="button" role="tab">
                                <i class="bi bi-graph-up me-1"></i> Gráficas y Evolución
                            </button>
                        </li>
                        @endif
                        <li class="nav-item {{(session('comercialid')== 1 or session('comercialid') == 5) ? '' : 'active'}}">
                            <button class="nav-link" id="categorias-tab" data-bs-toggle="tab"
                                    data-bs-target="#categorias" type="button" role="tab">
                                <i class="bi bi-diagram-3 me-1"></i> Inventario por Categoría
                            </button>
                        </li>

                            <li class="nav-item">
                                <button class="nav-link" id="calendario-tab" data-bs-toggle="tab"
                                        data-bs-target="#calendario" type="button" role="tab">
                                    <i class="bi bi-calendar-month me-1"></i> Calendario
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" id="seguimiento-tab" data-bs-toggle="tab"
                                        data-bs-target="#seguimiento" type="button" role="tab">
                                    <i class="bi bi-arrow-left-right me-1"></i> Seguimiento Diario
                                </button>
                            </li>
                    </ul>

                    <div class="tab-content">

                        @if(session('comercialid')== 1 or session('comercialid') == 5)
                        <!-- Tab 1: Sincronización -->
                        <div class="tab-pane fade show active" id="sincronizacion" role="tabpanel">
                            <!-- Selector de fecha -->
                            <div class="date-selector">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        <i class="bi bi-calendar3 text-primary fs-5"></i>
                                        <label class="ms-1 fw-bold">Fecha a consultar:</label>
                                    </div>

                                    <input type="date" id="fechaSync" class="form-control form-control-sm" style="width: 180px;">
                                    <button class="btn btn-sm btn-primary" onclick="cargarSincronizacionPorFecha()">
                                        <i class="bi bi-search me-1"></i>Consultar
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="cargarSincronizacionHoy()">
                                        <i class="bi bi-calendar-today me-1"></i>Hoy
                                    </button>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="cambiarFechaSync(-1)" title="Día anterior" style="align-content: center;  align-items: center;display: flex;">
                                            <i class="ri-arrow-left-line"></i> Anterior
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="cambiarFechaSync(1)" title="Día siguiente" style="align-content: center;  align-items: center;display: flex;">
                                            Siguiente <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>
                                    <div class="ms-auto">
                                        <button class="btn btn-sm btn-outline-primary" onclick="capturarSincronizacion()">
                                            <i class="bi bi-camera"></i> Capturar
                                        </button>
                                        <button class="btn btn-sm btn-outline-success ms-2" onclick="exportarSincronizacionExcel()">
                                            <i class="bi bi-file-excel"></i> Exportar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenedor de sincronización -->
                            <div id="syncContent">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary"></div>
                                    <p class="mt-2">Cargando datos de sincronización...</p>
                                </div>
                            </div>

                            <!-- Modal de inventario -->
                            <div class="modal fade" id="modalInventarioSucursal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">
                                                <i class="bi bi-box-seam me-2"></i>
                                                <span id="modalTituloSucursal"></span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" id="modalContenidoInventario">
                                            <div class="text-center py-4">
                                                <div class="spinner-border text-primary"></div>
                                                <p class="mt-2">Cargando inventario...</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-success" onclick="capturarModalInventario()">
                                                <i class="bi bi-camera"></i> Capturar
                                            </button>
                                            <button class="btn btn-primary" onclick="exportarModalInventario()">
                                                <i class="bi bi-file-excel"></i> Exportar
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Seleccion fecha para ver cantidades de ese dia -->
                        <div class="tab-pane fade" id="matriz" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calendar3 me-1"></i>Fecha a consultar
                                    </label>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="fechaMatriz" class="form-control">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" onclick="cargarMatrizInventario()">
                                        <i class="bi bi-search me-2"></i>Consultar
                                    </button>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-success w-100" onclick="exportarMatrizExcel()">
                                        <i class="bi bi-file-excel me-2"></i>Exportar
                                    </button>
                                </div>
                            </div>


                            <!-- AGREGAR ESTE DIV QUE FALTA -->
                            <div id="resumenMatriz" class="row mb-4" style="display: none;"></div>

                            <div id="resultadoMatriz" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3 d-none">
                                    <h5 class="mb-0">
                                        <i class="bi bi-table me-2"></i>Detalle de Inventario
                                    </h5>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="capturarMatrizInventario()">
                                            <i class="bi bi-camera"></i> Capturar
                                        </button>

                                    </div>
                                </div>
                                <div class="table-responsive-matriz">
                                    <table class="table table-bordered table-sm matriz-table" id="tablaMatriz">
                                        <thead id="theadMatriz"></thead>
                                        <tbody id="tbodyMatriz"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Gráficas y Evolución -->
                        <div class="tab-pane fade" id="graficas" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Inicio</label>
                                    <input type="date" id="fechaInicioGrafica" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fecha Fin</label>
                                    <input type="date" id="fechaFinGrafica" class="form-control">
                                </div>
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    <button class="btn btn-primary w-100" onclick="cargarGraficas()">
                                        <i class="bi bi-graph-up me-2"></i>Actualizar
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="capturarGraficas()">
                                        <i class="bi bi-camera"></i>
                                    </button>
                                </div>
                            </div>


                            <div id="resumenPeriodo" class="row mb-4"></div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0 text-white">Evolución Temporal del Inventario</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="evolucionLineChart" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0 text-white">Distribución por Sucursal</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="sucursalesBarChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-warning text-white">
                                            <h6 class="mb-0">Top 10 Productos con Mayor Variación</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="topProductosChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- Tab 4: Inventario por Categoría -->
                        <div class="tab-pane fade {{(session('comercialid')== 1 or session('comercialid') == 5) ? '' : 'show active'}}" id="categorias" role="tabpanel">
                            <div class="row">
                                <!-- Filtro de sucursal -->
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="post" id="formExistencias" action="/existencias">
                                                @csrf @method('POST')
                                                <div class="row align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold">Seleccionar Sucursal</label>
                                                        <select class="form-select" id="idsucuCategorias" name="fksucursal">
                                                            <option value="0" selected>🌐 Seleccione una sucursal</option>
                                                            @foreach($allsucursales ?? [] as $sucu)
                                                                <option value="{{ $sucu->id }}">🏪 {{ $sucu->descrip }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-primary" onclick="cargarInventarioCategorias()">
                                                            <i class="bi bi-search me-1"></i> Consultar
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success ms-2" onclick="exportarCategoriasExcel()">
                                                            <i class="bi bi-file-excel me-1"></i> Exportar
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel izquierdo - Categorías -->
                                <div class="col-xl-5">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0 text-white"><i class="bi bi-tags me-2"></i>Categorías</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="p-2 border-bottom">
                                                <input type="text" id="buscarCategoria" class="form-control form-control-sm" placeholder="🔍 Buscar categoría...">
                                            </div>
                                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                                <table class="table table-sm table-hover mb-0" id="tablaCategorias">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th><i class="bi bi-folder me-1"></i> Categoría</th>
                                                        <th class="text-end"><i class="bi bi-box-seam me-1"></i> Unds</th>
                                                        <th class="text-end"><i class="bi bi-calculator me-1"></i> Costo</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="categoriasTreeBody">
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">
                                                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                                            Seleccione una sucursal para cargar categorías
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel derecho - Detalle de productos -->
                                <div class="col-xl-7">
                                    <div id="detalleProductosCategoria" class="min-vh-50">
                                        <div class="card shadow-sm">
                                            <div class="card-body text-center text-muted py-5">
                                                <i class="bi bi-folder2-open fs-1 d-block mb-3"></i>
                                                <h5>Selecciona una categoría</h5>
                                                <p class="mb-0">Haz clic en cualquier categoría del panel izquierdo para ver el detalle de productos</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="tab-pane fade" id="calendario" role="tabpanel">
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-month fs-1 d-block mb-3 text-primary"></i>
                                    <h5>📅 Calendario de Sincronizaciones</h5>
                                    <p>Visualiza el cumplimiento diario de sincronización por sucursal</p>
                                    <a href="{{ route('inventario.calendario') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-right-circle me-2"></i>Ir al Calendario
                                    </a>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="seguimiento" role="tabpanel">
                                <div class="text-center py-4">
                                    <i class="bi bi-arrow-left-right fs-1 d-block mb-3 text-primary"></i>
                                    <h5>📊 Seguimiento Diario de Inventario</h5>
                                    <p>Visualiza el inventario inicial, movimientos y mermas por producto</p>
                                    <a href="{{ route('inventario.seguimiento.diario') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-right-circle me-2"></i>Ir al Seguimiento
                                    </a>
                                </div>
                            </div>

                        <!-- Modal para ver existencias por categoría (fullscreen) -->
                        <div class="modal fade" id="modalExistenciasCategoria" aria-hidden="true" tabindex="-1">
                            <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <div class="d-flex gap-3 align-items-center justify-content-between w-100">
                                            <div class="position-relative flex-grow-1">
                                                <input type="text" style="padding-left: 60px;"
                                                       class="form-control form-control-lg border-2 busquedaCategoriaModal"
                                                       placeholder="Buscar producto por código, nombre, marca..."
                                                       autocomplete="off" id="busquedaCategoriaModalInput">
                                                <span class="bi bi-search fs-5" style="color: #f95a02; position: absolute; left: 18px; top: 12px;"></span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-light" onclick="exportarCategoriasModalExcel()">
                                                <i class="bi bi-file-excel"></i> Exportar a Excel
                                            </button>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body bg-light" id="contenidoCategoriaModal">
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status"></div>
                                            <p class="mt-3 text-muted">Cargando información...</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        let currentCodalte = '';
        let currentCategoriaNombre = '';
        let currentCategoriasData = null;

        // Modificar el $(document).ready para incluir la actualización de fechas
        $(document).ready(function() {
            let hoy = new Date().toISOString().split('T')[0];
            let hace30Dias = new Date();
            hace30Dias.setDate(hace30Dias.getDate() - 30);

            let hace30DiasStr = hace30Dias.toISOString().split('T')[0];

            $('#fechaSync, #fechaMatriz').val(hoy);
            $('#fechaInicioGrafica').val(hace30DiasStr);
            $('#fechaFinGrafica').val(hoy);

            cargarSincronizacionHoy();
            setTimeout(() => cargarGraficas(), 500);

            // Inicializar navegación por teclado
            initSyncKeyboardNavigation();
            agregarBotonesNavegacionFlotantes();

            // Agregar atajo para recargar (tecla R)
            $(document).on('keydown', function(e) {
                if (!$('#sincronizacion-tab').hasClass('active')) return;
                if ($(e.target).is('input, textarea, select')) return;

                if (e.key === 'r' || e.key === 'R') {
                    e.preventDefault();
                    recargarSyncActual();
                } else if (e.key === '?' || (e.shiftKey && e.key === '/')) {
                    e.preventDefault();
                    mostrarAyudaTeclado();
                }
            });

            // Tooltips para los botones
            $('[title]').tooltip();
        });

        function formatearFechaDDMMYYYY(fechaStr) {
            if (!fechaStr) return '';
            let partes = fechaStr.split('-');
            if (partes.length === 3) {
                return `${partes[2]}/${partes[1]}/${partes[0]}`;
            }
            return fechaStr;
        }

        // Función para formatear fecha para mostrar en gráficas (con día de semana opcional)
        function formatearFechaParaGrafica(fechaStr) {
            if (!fechaStr) return '';
            let partes = fechaStr.split('-');
            if (partes.length === 3) {
                let fecha = new Date(partes[0], partes[1] - 1, partes[2]);
                const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${diasSemana[fecha.getDay()]} ${partes[2]}/${meses[parseInt(partes[1])-1]}`;
            }
            return fechaStr;
        }

        function cargarInventarioCategorias() {
            let fksucursal = $('#idsucuCategorias').val();

            // Validar que haya una sucursal seleccionada
            if (!fksucursal || fksucursal == 0) {
                $('#categoriasTreeBody').html(`
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                    Seleccione una sucursal para cargar las categorías
                </td>
            </tr>
        `);
                return;
            }

            $('#categoriasTreeBody').html(`
        <tr>
            <td colspan="3" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                Cargando categorías...
            </tr>
        </tr>
    `);

            $.ajax({
                url: '/inventario/categorias-tree',
                method: 'POST',
                data: {
                    fksucursal: fksucursal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (response.categorias_tree && response.categorias_tree.length > 0) {
                            currentCategoriasData = response;
                            renderCategoriasTree(response.categorias_tree);
                            mostrarNotificacion(`✅ ${response.total_categorias} categorías cargadas`, 'success');
                        } else {
                            $('#categoriasTreeBody').html(`
                        <tr>
                            <td colspan="3" class="text-center text-warning py-4">
                                <i class="bi bi-folder2-open fs-4 d-block mb-2"></i>
                                No hay categorías con inventario para esta sucursal
                             </tr>
                        </tr>
                    `);
                        }
                    } else {
                        $('#categoriasTreeBody').html(`
                    <tr>
                        <td colspan="3" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-2"></i>
                            ${response.message || 'Error al cargar categorías'}
                         </tr>
                    </tr>
                `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $('#categoriasTreeBody').html(`
                <tr>
                    <td colspan="3" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle fs-4 d-block mb-2"></i>
                        Error al cargar categorías. Verifique la conexión.
                        <br><small class="text-muted">${error}</small>
                     </tr>
                </tr>
            `);
                }
            });
        }

        // Al cambiar la selección de sucursal, cargar automáticamente las categorías
        $('#idsucuCategorias').on('change', function() {
            cargarInventarioCategorias();
        });

        // También cargar al mostrar el tab por primera vez
        let categoriasTabLoaded = false;
        $('#categorias-tab').on('shown.bs.tab', function() {
            if (!categoriasTabLoaded) {
                // Verificar si hay una sucursal seleccionada
                let sucursalSeleccionada = $('#idsucuCategorias').val();
                if (sucursalSeleccionada && sucursalSeleccionada != 0) {
                    cargarInventarioCategorias();
                } else {
                    // Si no hay sucursal seleccionada, mostrar mensaje
                    $('#categoriasTreeBody').html(`
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-building fs-4 d-block mb-2"></i>
                        Seleccione una sucursal en el filtro superior
                     </tr>
                </tr>
            `);
                }
                categoriasTabLoaded = true;
            }
        });

        function renderCategoriasTree(categorias, nivel = 0) {
            if (!categorias || categorias.length === 0) {
                $('#categoriasTreeBody').html(`
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="bi bi-folder2-open fs-4 d-block mb-2"></i>
                    No hay categorías con inventario para esta sucursal
                 </tr>
            </tr>
        `);
                return;
            }

            let html = '';
            let totalUnidades = 0;
            let totalValor = 0;

            for (let cat of categorias) {
                totalUnidades += cat.existen;
                totalValor += cat.preciod;

                let tieneHijos = cat.hijos && cat.hijos.length > 0;
                let paddingLeft = nivel * 20;
                let rowId = 'cat_row_' + cat.codinst;

                html += `<tr id="${rowId}" class="categoria-tree-item" data-codinst="${cat.codinst}" data-codalte="${cat.codalte}" data-nivel="${nivel}" data-label="${cat.label.toLowerCase()}">`;
                html += `<td style="padding-left: ${paddingLeft}px">`;

                if (tieneHijos) {
                    html += `<span class="toggle-icon-cat collapsed" onclick="toggleCategoriaTree(${cat.codinst}, event)">`;
                    html += `<i class="bi bi-caret-right-fill"></i>`;
                    html += `</span>`;
                } else {
                    html += `<span class="toggle-icon-cat" style="visibility: hidden;"><i class="bi bi-caret-right-fill"></i></span>`;
                }

                html += `<a href="javascript:;" onclick="cargarDetalleCategoria(${cat.codinst}, '${cat.label.replace(/'/g, "\\'")}', '${cat.codalte}')" class="text-decoration-none text-dark">`;
                html += `<i class="bi bi-folder${nivel == 0 ? '-fill' : ''} text-warning me-2"></i>`;
                html += `${cat.label}`;
                html += `<i class="bi bi-box-arrow-up-right ms-2" style="font-size: 10px; color: #f95a02;"></i>`;
                html += `</a>`;
                html += `</td>`;
                html += `<td class="text-end"><span class="badge bg-info">${numberFormat(cat.existen, 0)}</span></td>`;
                html += `<td class="text-end"><span class="badge bg-success">$${numberFormat(cat.preciod, 2)}</span></td>`;
                html += `</tr>`;

                if (tieneHijos) {
                    let childContainerId = `cat_children_${cat.codinst}`;
                    html += `<tbody id="${childContainerId}" style="display: none;">`;
                    html += renderCategoriasTreeRecursive(cat.hijos, nivel + 1);
                    html += `</tbody>`;
                }
            }

            // Agregar total si es nivel 0 y hay categorías
            if (nivel === 0 && categorias.length > 0) {
                html += `<tr class="table-light fw-bold">
            <td>TOTAL CATEGORÍAS PRINCIPALES</td>
            <td class="text-end"><span class="badge bg-info">${numberFormat(totalUnidades, 0)}</span></td>
            <td class="text-end"><span class="badge bg-success">$${numberFormat(totalValor, 2)}</span></td>
         </tr>`;
            }

            $('#categoriasTreeBody').html(html);
        }

        function renderCategoriasTreeRecursive(categorias, nivel) {
            let html = '';
            for (let cat of categorias) {
                let tieneHijos = cat.hijos && cat.hijos.length > 0;
                let paddingLeft = nivel * 20;
                let rowId = 'cat_row_' + cat.codinst;

                html += `<tr id="${rowId}" class="categoria-tree-item" data-codinst="${cat.codinst}" data-codalte="${cat.codalte}" data-nivel="${nivel}" data-label="${cat.label.toLowerCase()}">`;
                html += `<td style="padding-left: ${paddingLeft}px">`;

                if (tieneHijos) {
                    html += `<span class="toggle-icon-cat collapsed" onclick="toggleCategoriaTree(${cat.codinst}, event)">`;
                    html += `<i class="bi bi-caret-right-fill"></i>`;
                    html += `</span>`;
                } else {
                    html += `<span class="toggle-icon-cat" style="visibility: hidden;"><i class="bi bi-caret-right-fill"></i></span>`;
                }

                html += `<a href="javascript:;" onclick="cargarDetalleCategoria(${cat.codinst}, '${cat.label.replace(/'/g, "\\'")}', '${cat.codalte}')" class="text-decoration-none text-dark">`;
                html += `<i class="bi bi-folder${nivel == 0 ? '-fill' : ''} text-warning me-2"></i>`;
                html += `${cat.label}`;
                html += `</a>`;
                html += `</td>`;
                html += `<td class="text-end"><span class="badge bg-info">${numberFormat(cat.existen, 0)}</span></td>`;
                html += `<td class="text-end"><span class="badge bg-success">$${numberFormat(cat.preciod, 2)}</span></td>`;
                html += `</tr>`;

                if (tieneHijos) {
                    let childContainerId = `cat_children_${cat.codinst}`;
                    html += `<tbody id="${childContainerId}" style="display: none;">`;
                    html += renderCategoriasTreeRecursive(cat.hijos, nivel + 1);
                    html += `</tbody>`;
                }
            }
            return html;
        }

        function toggleCategoriaTree(codinst, event) {
            event.stopPropagation();
            let childContainer = $('#cat_children_' + codinst);
            let toggleIcon = $('#cat_row_' + codinst + ' .toggle-icon-cat i');

            if (childContainer.is(':visible')) {
                childContainer.slideUp(200);
                toggleIcon.removeClass('bi-caret-down-fill').addClass('bi-caret-right-fill');
                $('#cat_row_' + codinst + ' .toggle-icon-cat').removeClass('expanded').addClass('collapsed');
            } else {
                childContainer.slideDown(200);
                toggleIcon.removeClass('bi-caret-right-fill').addClass('bi-caret-down-fill');
                $('#cat_row_' + codinst + ' .toggle-icon-cat').removeClass('collapsed').addClass('expanded');
            }
        }

        function cargarDetalleCategoria(codinst, categoriaNombre, codalte) {
            currentCodalte = codalte;
            currentCategoriaNombre = categoriaNombre;
            let fksucursal = $('#idsucuCategorias').val();

            $('.categoria-tree-item').removeClass('active');
            $('#cat_row_' + codinst).addClass('active');

            $('#detalleProductosCategoria').html(`
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">Cargando productos de ${categoriaNombre}...</p>
            </div>
        </div>
    `);

            $.ajax({
                type: 'post',
                data: {
                    codinst: codinst,
                    fksucursal: fksucursal,
                    _token: '{{ csrf_token() }}'
                },
                url: '/reporte/existen/php',
                success: function(response) {
                    $('#detalleProductosCategoria').html(`
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>${categoriaNombre}</h6>
                        <div>
                            <button class="btn btn-sm btn-outline-light me-2" onclick="abrirModalCategoria()">
                                <i class="bi bi-search"></i> Ver detalle completo
                            </button>
                            <button class="btn btn-sm btn-outline-light" onclick="exportarDetalleCategoriaExcel()">
                                <i class="bi bi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 550px;">
                            ${response}
                        </div>
                    </div>
                </div>
            `);
                },
                error: function() {
                    $('#detalleProductosCategoria').html(`
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5 text-danger">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                        <p class="mt-3">Error al cargar los productos</p>
                    </div>
                </div>
            `);
                }
            });
        }

        function abrirModalCategoria() {
            $('#modalExistenciasCategoria').modal('show');
            $('#contenidoCategoriaModal').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Cargando productos de ${currentCategoriaNombre}...</p>
        </div>
    `);
            cargarModalCategoria(currentCodalte, '');
        }

        function cargarModalCategoria(codalte, busqueda) {
            $.ajax({
                type: 'post',
                data: {
                    codalte: codalte || '',
                    busqueda: busqueda || '',
                    _token: '{{ csrf_token() }}'
                },
                url: '/saprod/viewprodinstsanciascodalte',
                success: function(response) {
                    $('#contenidoCategoriaModal').html(`
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="bi bi-tag me-2"></i>${currentCategoriaNombre}</h5>
                    <span class="badge bg-secondary">Total productos cargados</span>
                </div>
                ${response}
            `);
                },
                error: function() {
                    $('#contenidoCategoriaModal').html(`
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>Error al cargar los datos
                </div>
            `);
                }
            });
        }

        // Búsqueda en tiempo real en el modal
        $('#busquedaCategoriaModalInput').off('keyup').on('keyup', function() {
            let busqueda = $(this).val();
            $('#contenidoCategoriaModal').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Buscando: ${busqueda}</p>
        </div>
    `);
            cargarModalCategoria(currentCodalte, busqueda);
        });

        // Búsqueda de categorías en tiempo real
        $('#buscarCategoria').off('keyup').on('keyup', function() {
            let searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                $('#categoriasTreeBody tr').show();
                $('[id^="cat_children_"]').hide();
                $('.toggle-icon-cat').removeClass('expanded').addClass('collapsed');
                $('.toggle-icon-cat i').removeClass('bi-caret-down-fill').addClass('bi-caret-right-fill');
            } else {
                $('#categoriasTreeBody tr').hide();
                $('#categoriasTreeBody tr.categoria-tree-item').each(function() {
                    let categoriaNombre = $(this).data('label') || '';
                    if (categoriaNombre.includes(searchTerm)) {
                        $(this).show();
                        let currentRow = $(this);
                        while (currentRow.length) {
                            let parentContainer = currentRow.closest('tbody[id^="cat_children_"]');
                            if (parentContainer.length) {
                                let parentId = parentContainer.attr('id').replace('cat_children_', '');
                                let parentRow = $('#cat_row_' + parentId);
                                if (parentRow.length && parentRow.is(':hidden')) {
                                    parentRow.show();
                                    let parentChildContainer = $('#cat_children_' + parentId);
                                    if (parentChildContainer.is(':hidden')) {
                                        parentChildContainer.show();
                                        let toggleIcon = parentRow.find('.toggle-icon-cat i');
                                        toggleIcon.removeClass('bi-caret-right-fill').addClass('bi-caret-down-fill');
                                        parentRow.find('.toggle-icon-cat').removeClass('collapsed').addClass('expanded');
                                    }
                                }
                                currentRow = parentRow;
                            } else {
                                break;
                            }
                        }
                    }
                });
            }
        });

        function exportarCategoriasExcel() {
            if (!currentCategoriasData || !currentCategoriasData.categorias_tree) {
                alert('No hay datos para exportar');
                return;
            }
            let data = [];
            function extraerCategoriasParaExportar(cats, nivel = 0) {
                for (let cat of cats) {
                    data.push({
                        'Nivel': nivel,
                        'Código': cat.codinst,
                        'Categoría': cat.label,
                        'Unidades': cat.existen,
                        'Valor Total': cat.preciod
                    });
                    if (cat.hijos && cat.hijos.length) {
                        extraerCategoriasParaExportar(cat.hijos, nivel + 1);
                    }
                }
            }
            extraerCategoriasParaExportar(currentCategoriasData.categorias_tree);
            let ws = XLSX.utils.json_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Categorias_Inventario');
            XLSX.writeFile(wb, `categorias_inventario_${new Date().toISOString().split('T')[0]}.xlsx`);
            mostrarNotificacion('Categorías exportadas a Excel');
        }

        function exportarDetalleCategoriaExcel() {
            let tabla = $('#detalleProductosCategoria table');
            if (!tabla.length) {
                alert('No hay datos para exportar');
                return;
            }
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.table_to_sheet(tabla[0]);
            XLSX.utils.book_append_sheet(wb, ws, `Inventario_${currentCategoriaNombre}`);
            XLSX.writeFile(wb, `inventario_${currentCategoriaNombre}_${new Date().toISOString().split('T')[0]}.xlsx`);
            mostrarNotificacion('Detalle exportado a Excel');
        }

        function exportarCategoriasModalExcel() {
            let tabla = $('#contenidoCategoriaModal table');
            if (!tabla.length) {
                alert('No hay datos para exportar');
                return;
            }
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.table_to_sheet(tabla[0]);
            XLSX.utils.book_append_sheet(wb, ws, `Inventario_${currentCategoriaNombre}`);
            XLSX.writeFile(wb, `inventario_detalle_${currentCategoriaNombre}_${new Date().toISOString().split('T')[0]}.xlsx`);
            mostrarNotificacion('Detalle exportado a Excel');
        }


        let lineChart, barChart, topChart;
        let currentInventarioData = null;
        let currentSucursalNombre = '';
        let currentSyncFecha = '';

        // ==================== FUNCIONES DE SINCRONIZACIÓN ====================

        // ==================== FUNCIONES DE NAVEGACIÓN POR FECHA ====================

        // Función para cambiar la fecha en el tab de sincronización
        function cambiarFechaSync(direccion) {
            let fechaActual = $('#fechaSync').val();
            if (!fechaActual) {
                fechaActual = new Date().toISOString().split('T')[0];
            }

            let nuevaFecha = new Date(fechaActual);
            nuevaFecha.setDate(nuevaFecha.getDate() + direccion);

            let fechaFormateada = nuevaFecha.toISOString().split('T')[0];
            $('#fechaSync').val(fechaFormateada);

            // Cargar los datos con la nueva fecha
            currentSyncFecha = fechaFormateada;
            cargarDatosSincronizacion(fechaFormateada);
        }

        // Función para navegación por teclado en el tab de sincronización
        function initSyncKeyboardNavigation() {
            // Remover eventos anteriores para evitar duplicados
            $(document).off('keydown.syncNav');

            $(document).on('keydown.syncNav', function(e) {
                // Verificar que el tab de sincronización ESTÉ ACTIVO (usando la clase 'active' en el botón)
                // Bootstrap agrega la clase 'active' al botón de la pestaña cuando está activa
                const isSyncTabActive = $('#sincronizacion-tab').hasClass('active');

                // Si no está activo el tab de sincronización, no hacer nada
                if (!isSyncTabActive) return;

                // Verificar que el modal no esté abierto (para no interferir)
                if ($('#modalInventarioSucursal').is(':visible')) return;

                // Evitar que las teclas interactúen con inputs
                if ($(e.target).is('input, textarea, select, button')) return;

                // PREVENIR COMPLETAMENTE el comportamiento por defecto del navegador
                e.preventDefault();
                e.stopPropagation();

                // Flecha izquierda (←) - Día anterior
                if (e.key === 'ArrowLeft') {
                    cambiarFechaSync(-1);
                    mostrarNotificacion('📅 Día anterior', 'info');
                    return false;
                }
                // Flecha derecha (→) - Día siguiente
                else if (e.key === 'ArrowRight') {
                    cambiarFechaSync(1);
                    mostrarNotificacion('📅 Día siguiente', 'info');
                    return false;
                }
                // Tecla 'H' o 'h' - Ir a hoy
                else if (e.key === 'h' || e.key === 'H') {
                    cargarSincronizacionHoy();
                    mostrarNotificacion('📅 Hoy', 'success');
                    return false;
                }
                // Tecla 'R' o 'r' - Recargar
                else if (e.key === 'r' || e.key === 'R') {
                    recargarSyncActual();
                    return false;
                }
                // Tecla '?' o '/' - Mostrar ayuda
                else if (e.key === '?' || e.key === '/' || (e.shiftKey && e.key === '/')) {
                    mostrarAyudaTeclado();
                    return false;
                }

                return true;
            });

            // También prevenir el comportamiento de las flechas en el documento para inputs
            $(document).on('keydown', function(e) {
                if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                    // Si el foco NO está en un input, textarea o select, prevenir scroll
                    if (!$(e.target).is('input, textarea, select')) {
                        e.preventDefault();
                    }
                }
            });
        }

        // También puedes agregar botones flotantes o en el header
        function agregarBotonesNavegacionFlotantes() {
            // Verificar si ya existen los botones flotantes
            if ($('#floatingNavSync').length) return;

            let floatingNav = $(`
        <div id="floatingNavSync" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;  ">
            <div class="btn-group-vertical shadow" role="group">
                <button class="btn btn-primary btn-sm" onclick="cambiarFechaSync(-1)" >
                    <i class="ri-arrow-left-line"></i> Anterior
                </button>
                <button class="btn btn-primary btn-sm" onclick="cambiarFechaSync(1)"  >
                    Siguiente <i class="ri-arrow-right-line"></i>
                </button>
                <button class="btn btn-secondary btn-sm" onclick="cargarSincronizacionHoy()"  >
                    <i class="ri-calendar-today-line"></i> Hoy
                </button>
            </div>
        </div>
    `);

            $('body').append(floatingNav);

            // Mostrar/ocultar botones flotantes cuando el tab de sincronización está activo
            $('#sincronizacion-tab').on('shown.bs.tab', function() {
                $('#floatingNavSync').fadeIn(200);
            });

            $('#sincronizacion-tab').on('hidden.bs.tab', function() {
                $('#floatingNavSync').fadeOut(200);
            });
        }

        // Función mejorada para cargar sincronización por fecha (con validación)
        function cargarSincronizacionPorFecha() {
            let fecha = $('#fechaSync').val();
            if (!fecha) {
                mostrarNotificacion('⚠️ Seleccione una fecha', 'warning');
                return;
            }

            // Validar que la fecha no sea futura
            let fechaObj = new Date(fecha);
            let hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fechaObj > hoy) {
                mostrarNotificacion('⚠️ No se pueden consultar fechas futuras', 'warning');
                return;
            }

            currentSyncFecha = fecha;
            cargarDatosSincronizacion(fecha);
        }

        // Función mejorada para cargar sincronización hoy
        function cargarSincronizacionHoy() {
            let hoy = new Date().toISOString().split('T')[0];
            $('#fechaSync').val(hoy);
            currentSyncFecha = hoy;
            cargarDatosSincronizacion(hoy);
            mostrarNotificacion('📅 Mostrando datos de hoy', 'success');
        }

        // Función para mostrar atajos de teclado (opcional)
        function mostrarAyudaTeclado() {
            // Usar SweetAlert si está disponible, si no, usar modal normal
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '⌨️ Atajos de teclado',
                    html: `
                <div class="text-start">
                    <div class="mb-2">
                        <kbd>←</kbd> <strong>Flecha izquierda</strong> - Día anterior
                    </div>
                    <div class="mb-2">
                        <kbd>→</kbd> <strong>Flecha derecha</strong> - Día siguiente
                    </div>
                    <div class="mb-2">
                        <kbd>H</kbd> <strong>Tecla H</strong> - Ir a hoy
                    </div>
                    <div class="mb-2">
                        <kbd>R</kbd> <strong>Tecla R</strong> - Recargar datos actuales
                    </div>
                    <div class="mb-2">
                        <kbd>?</kbd> o <kbd>/</kbd> - Mostrar esta ayuda
                    </div>
                </div>
                <hr>
                <small class="text-muted">Los atajos funcionan solo en la pestaña "Sincronización"</small>
            `,
                    icon: 'info',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#75a373',
                    backdrop: true
                });
            } else {
                // Fallback si SweetAlert no está disponible
                alert('Atajos de teclado:\n\n← → : Navegar entre días\nH : Ir a hoy\nR : Recargar datos\n? : Mostrar ayuda');
            }
        }

        // Función para recargar datos actuales
        function recargarSyncActual() {
            if (currentSyncFecha) {
                cargarDatosSincronizacion(currentSyncFecha);
                mostrarNotificacion('🔄 Datos recargados', 'info');
            } else {
                cargarSincronizacionHoy();
            }
        }


        function cargarDatosSincronizacion(fecha) {
            $('#syncContent').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Cargando datos de sincronización para ${fecha}...</p>
                </div>
            `);

            $.ajax({
                url: '/inventario/sincronizacion-por-fecha',
                method: 'POST',
                data: {
                    fecha: fecha,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    mostrarSincronizacion(response);
                },
                error: function() {
                    $('#syncContent').html('<div class="alert alert-danger">Error al cargar los datos</div>');
                }
            });
        }

        function getDiaSemana(fechaStr) {
            // Si la fecha viene en formato YYYY-MM-DD
            let partes = fechaStr.split('-');
            let fecha = new Date(partes[0], partes[1] - 1, partes[2]);

            const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            return diasSemana[fecha.getDay()];
        }

        function mostrarSincronizacion(data) {

            let diaSemana = getDiaSemana(data.fecha);

            let html = `
                <div class="sync-compact">
                    <div class="sync-header">
                        <div>
                            <i class="bi bi-calendar-check me-2"></i>
                            <strong>Sincronización: ${diaSemana} ${data.fecha_formateada}</strong>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-info-circle"></i> ${data.total_sucursales} sucursales
                            </span>
                        </div>
                    </div>

                    <div class="sync-stats">
                        <div class="stat-card">
                            <div class="stat-number">${data.total_sucursales}</div>
                            <small>Total Sucursales</small>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #75a373;">${data.sincronizadas}</div>
                            <small>Sincronizadas Hoy</small>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: ${data.porcentaje_sync}%"></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #dc3545;">${data.no_sincronizadas}</div>
                            <small>Sin Sincronizar</small>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.porcentaje_sync}%</div>
                            <small>Cobertura</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm sync-table">
                            <thead>
                                <tr>
                                    <th width="40"></th>
                                    <th>Sucursal</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Productos</th>
                                    <th>Última Sincronización</th>
                                    <th width="80" class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            data.sucursales.forEach(sucursal => {
                let tieneSync = sucursal.sincronizado;
                let estadoColor = tieneSync ? 'success' : 'warning';
                let syncClass = tieneSync ? 'sync-today' : '';

                html += `
                    <tr class="sync-card ${syncClass}" style="cursor: pointer;" onclick="verInventarioSucursal('${data.fecha}', ${sucursal.id}, '${sucursal.descrip.replace(/'/g, "\\'")}')">
                        <td class="text-center">
                            <span class="status-badge status-${estadoColor}"></span>
                        </td>
                        <td>
                            <i class="bi bi-building me-1"></i>
                            <strong>${sucursal.descrip}</strong>
                        </td>
                        <td class="text-center">
                            ${tieneSync ?
                    '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Sincronizado</span>' :
                    '<span class="badge bg-warning"><i class="bi bi-exclamation-triangle me-1"></i>Pendiente</span>'
                }
                        </td>
                        <td class="text-center">
                            ${tieneSync ? `<span class="badge bg-info">${numberFormat(sucursal.total_productos, 0)}</span>` : '<span class="text-muted">---</span>'}
                        </td>
                        <td>
                            <small>
                                <i class="bi bi-calendar3 me-1"></i>
                                ${sucursal.ultima_fecha || 'Sin registros'}
                                ${sucursal.dias_desde ? `<span class="text-muted">(hace ${sucursal.dias_desde} días)</span>` : ''}
                            </small>
                        </td>
                        <td class="text-center">
                            ${tieneSync ? `
                                <button class="btn btn-sm btn-outline-primary btn-capture"
                                    onclick="event.stopPropagation(); capturarSucursal('${data.fecha}', ${sucursal.id}, '${sucursal.descrip.replace(/'/g, "\\'")}')"
                                    title="Capturar inventario">
                                    <i class="bi bi-camera"></i>
                                </button>
                            ` : '<span class="text-muted">---</span>'}
                        </td>
                    </tr>
                `;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            $('#syncContent').html(html);
        }

        // ==================== FUNCIONES DE CAPTURA ====================

        function capturarSincronizacion() {
            const element = document.querySelector('#syncContent .sync-compact');
            if (!element) {
                alert('No hay datos para capturar');
                return;
            }

            mostrarNotificacion('Generando captura...', 'info');

            html2canvas(element, {
                scale: 2.5,
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            }).then(canvas => {
                canvas.toBlob(function(blob) {
                    if (navigator.clipboard && navigator.clipboard.write) {
                        const clipboardItem = new ClipboardItem({ [blob.type]: blob });
                        navigator.clipboard.write([clipboardItem]).then(() => {
                            mostrarNotificacion('✅ Captura copiada al portapapeles! Puedes pegarla con Ctrl+V', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, `sincronizacion_${currentSyncFecha}`);
                            mostrarNotificacion('⚠️ Se descargó la imagen', 'warning');
                        });
                    } else {
                        descargarImagen(canvas, `sincronizacion_${currentSyncFecha}`);
                        mostrarNotificacion('⚠️ Se descargó la imagen', 'warning');
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al capturar la pantalla');
            });
        }

        function capturarModalInventario() {
            const element = document.querySelector('#modalInventarioSucursal .modal-content');
            if (!element) return;

            mostrarNotificacion('Generando captura...', 'info');

            html2canvas(element, {
                scale: 2.5,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                canvas.toBlob(function(blob) {
                    if (navigator.clipboard && navigator.clipboard.write) {
                        const clipboardItem = new ClipboardItem({ [blob.type]: blob });
                        navigator.clipboard.write([clipboardItem]).then(() => {
                            mostrarNotificacion('✅ Captura copiada al portapapeles!', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, `inventario_${currentSucursalNombre}`);
                        });
                    } else {
                        descargarImagen(canvas, `inventario_${currentSucursalNombre}`);
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al capturar');
            });
        }

        function capturarMatrizInventario() {
            const element = document.querySelector('#resultadoMatriz');
            if (!element || element.style.display === 'none') {
                alert('Primero consulte un inventario');
                return;
            }

            mostrarNotificacion('Generando captura de la matriz...', 'info');

            html2canvas(element, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                canvas.toBlob(function(blob) {
                    if (navigator.clipboard && navigator.clipboard.write) {
                        const clipboardItem = new ClipboardItem({ [blob.type]: blob });
                        navigator.clipboard.write([clipboardItem]).then(() => {
                            mostrarNotificacion('✅ Matriz copiada al portapapeles!', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, `matriz_${$('#fechaMatriz').val()}`);
                        });
                    } else {
                        descargarImagen(canvas, `matriz_${$('#fechaMatriz').val()}`);
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al capturar');
            });
        }

        function capturarGraficas() {
            const graficasContainer = document.querySelector('#graficas');
            if (!graficasContainer) return;

            mostrarNotificacion('Generando captura de gráficas...', 'info');

            html2canvas(graficasContainer, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                canvas.toBlob(function(blob) {
                    if (navigator.clipboard && navigator.clipboard.write) {
                        const clipboardItem = new ClipboardItem({ [blob.type]: blob });
                        navigator.clipboard.write([clipboardItem]).then(() => {
                            mostrarNotificacion('✅ Gráficas copiadas al portapapeles!', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, 'graficas_inventario');
                        });
                    } else {
                        descargarImagen(canvas, 'graficas_inventario');
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al capturar');
            });
        }

        function descargarImagen(canvas, nombre) {
            const link = document.createElement('a');
            link.download = `${nombre}_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }

        function capturarSucursal(fecha, sucursalId, sucursalNombre) {
            currentSucursalNombre = sucursalNombre;
            $('#modalTituloSucursal').text(`Inventario - ${sucursalNombre} (${fecha})`);
            $('#modalInventarioSucursal').modal('show');

            $.ajax({
                url: '/inventario/por-fecha',
                method: 'POST',
                data: {
                    fecha: fecha,
                    fksucursal: sucursalId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    currentInventarioData = response.data;
                    mostrarInventarioModal(response.data, response.resumen);
                },
                error: function() {
                    $('#modalContenidoInventario').html('<div class="alert alert-danger">Error al cargar el inventario</div>');
                }
            });
        }

        function verInventarioSucursal(fecha, sucursalId, sucursalNombre) {
            capturarSucursal(fecha, sucursalId, sucursalNombre);
        }

        function mostrarInventarioModal(inventario, resumen) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="alert alert-info">Total Productos: ${resumen.total_productos}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success">Total Unidades: ${numberFormat(resumen.total_unidades, 0)}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-primary">Valor Total: $${numberFormat(resumen.total_valor, 2)}</div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 500px;">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr><th>Código</th><th>Producto</th><th class="text-end">Existencias</th><th class="text-end">Valor Total</th></tr>
                        </thead>
                        <tbody>
            `;

            inventario.slice(0, 100).forEach(item => {
                let valorTotal = item.existen * (item.costod || 0);
                html += `<tr>
                    <td>${item.codprod}</td>
                    <td>${item.producto?.descrip || 'N/A'}</td>
                    <td class="text-end">${numberFormat(item.existen, 0)}</td>
                    <td class="text-end">$${numberFormat(valorTotal, 2)}</td>
                </tr>`;
            });

            if (inventario.length > 100) {
                html += `<tr><td colspan="4" class="text-center text-muted">... y ${inventario.length - 100} productos más</td></tr>`;
            }

            html += `</tbody></table></div>`;
            $('#modalContenidoInventario').html(html);
        }

        function mostrarNotificacion(mensaje, tipo = 'success') {
            $('.screenshot-notification').remove();

            let bgColor   = tipo === 'success' ? '#75a373' : (tipo === 'warning' ? '#ffc107' : '#75a373');
            let icono     = tipo === 'success' ? 'check-circle-fill' : (tipo === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill');
            let textColor = tipo === 'warning' ? '#000' : '#fff';

            let notification = $(`
                <div class="screenshot-notification alert" style="background-color: ${bgColor}; color: ${textColor};">
                    <i class="bi bi-${icono} me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close btn-close-${tipo === 'warning' ? 'black' : 'white'}" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(notification);
            setTimeout(() => notification.fadeOut(300, () => notification.remove()), 4000);
        }

        function exportarModalInventario() {
            if (!currentInventarioData) return;
            let data = currentInventarioData.map(item => ({
                'Código': item.codprod,
                'Producto': item.producto?.descrip || 'N/A',
                'Existencias': item.existen,
                'Valor Total': (item.existen * (item.costod || 0)).toFixed(2)
            }));
            let ws = XLSX.utils.json_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Inventario');
            XLSX.writeFile(wb, `inventario_${currentSucursalNombre}.xlsx`);
            mostrarNotificacion('Exportado a Excel');
        }

        function exportarSincronizacionExcel() {
            let tabla = document.querySelector('#syncContent table');
            if (!tabla) return;
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.table_to_sheet(tabla);
            XLSX.utils.book_append_sheet(wb, ws, 'Sincronizacion');
            XLSX.writeFile(wb, `sincronizacion_${currentSyncFecha}.xlsx`);
            mostrarNotificacion('Exportado a Excel');
        }

        // ==================== FUNCIONES DE MATRIZ ====================

        function cargarMatrizInventario() {
            let fecha = $('#fechaMatriz').val();
            if (!fecha) {
                alert('Seleccione una fecha');
                return;
            }

            $('#resultadoMatriz').hide();
            $('#tbodyMatriz').html('<tr><td colspan="100" class="text-center"><div class="spinner-border"></div></td></tr>');
            $('#resultadoMatriz').show();

            $.ajax({
                url: '/inventario/matriz',
                method: 'POST',
                data: {
                    fecha: fecha,
                    categoria: $('#categoriaMatriz').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    mostrarMatriz(response);
                },
                error: function() {
                    alert('Error al cargar los datos');
                }
            });
        }



        function mostrarMatriz(data) {
            // Mostrar resumen de productos
            let resumenHtml = `
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number">${Object.keys(data.matriz).length}</div>
                <div class="summary-label">Productos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number">${numberFormat(data.total_general_unidades, 0)}</div>
                <div class="summary-label">Unidades</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number">$${numberFormat(data.total_general_valor, 2)}</div>
                <div class="summary-label">Valor Total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number">${data.sucursales.length}</div>
                <div class="summary-label">Sucursales</div>
            </div>
        </div>
    `;
            $('#resumenMatriz').html(resumenHtml).show();

            let theadHtml = '<tr><th>Código</th><th>Producto</th><th>Categoría</th>';
            data.sucursales.forEach(suc => {
                let totalSuc = data.totales_sucursales[suc.id]?.unidades || 0;
                theadHtml += `<th class="text-end sucursal-col" data-sucursal-id="${suc.id}" onclick="resaltarSucursalEnTabla('${suc.id}', '${suc.descrip}')">
            ${suc.descrip.length > 15 ? suc.descrip.substring(0, 12) + '...' : suc.descrip}<br>
            <small class="text-muted">${numberFormat(totalSuc, 0)}</small>
        </th>`;
            });
            theadHtml += '<th class="text-end total-col">Total</th></tr>';
            $('#theadMatriz').html(theadHtml);

            let tbodyHtml = '';
            let productosMostrados = 0;
            const maxInicial = 20;

            data.matriz.forEach((producto, idx) => {
                let totalProducto = 0;
                let display = productosMostrados >= maxInicial ? 'none' : '';
                let nivel = idx < 10 ? 0 : 1; // Para agrupar por niveles

                tbodyHtml += `<tr class="producto-row" style="display: ${display}" data-nivel="${nivel}">
            <td><small>${producto.codprod}</small></td>
            <td><small>${producto.descrip.length > 40 ? producto.descrip.substring(0, 40) + '...' : producto.descrip}</small></td>
            <td><small>${producto.categoria}</small></td>`;

                data.sucursales.forEach(suc => {
                    let existencias = producto.sucursales[suc.id] || 0;
                    totalProducto += existencias;
                    tbodyHtml += `<td class="text-end ${existencias > 0 ? 'fw-bold' : 'text-muted'}">
                ${existencias > 0 ? numberFormat(existencias, 0) : '-'}
            </td>`;
                });

                tbodyHtml += `<td class="text-end total-col fw-bold">${numberFormat(totalProducto, 0)}</td>`;
                tbodyHtml += `</tr>`;
                productosMostrados++;
            });

            if (data.matriz.length > maxInicial) {
                tbodyHtml += `<tr class="table-light">
            <td colspan="${data.sucursales.length + 3}" class="text-center">
                <button class="btn btn-sm btn-outline-primary" onclick="$('.producto-row').show(); $(this).parent().parent().hide();">
                    <i class="bi bi-eye"></i> Ver ${data.matriz.length - maxInicial} productos más
                </button>
            </td>
        </tr>`;
            }

            tbodyHtml += '<tr class="total-col table-active">';
            tbodyHtml += '<td colspan="3" class="fw-bold">TOTALES</td>';
            data.sucursales.forEach(suc => {
                let totalSuc = data.totales_sucursales[suc.id]?.unidades || 0;
                tbodyHtml += `<td class="text-end fw-bold">${numberFormat(totalSuc, 0)}</td>`;
            });
            tbodyHtml += `<td class="text-end fw-bold">${numberFormat(data.total_general_unidades, 0)}</td>`;
            tbodyHtml += '</tr>';

            $('#tbodyMatriz').html(tbodyHtml);
            $('#resultadoMatriz').show();
        }

        function resaltarSucursalEnTabla(sucursalId, sucursalNombre) {
            $('.sucursal-col').removeClass('table-primary');
            $(`th[data-sucursal-id="${sucursalId}"]`).addClass('table-primary');
            $(`td[data-sucursal="${sucursalId}"]`).addClass('table-primary');
            mostrarNotificacion(`Mostrando: ${sucursalNombre}`, 'info');
        }

        // También puedes agregar una función para expandir/colapsar por niveles
        function expandirPorNivel(nivel) {
            $(`.producto-row[data-nivel="${nivel}"]`).show();
        }

        function colapsarPorNivel(nivel) {
            $(`.producto-row[data-nivel="${nivel}"]`).hide();
        }

        function exportarMatrizExcel() {
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.table_to_sheet(document.getElementById('tablaMatriz'));
            XLSX.utils.book_append_sheet(wb, ws, 'Inventario');
            XLSX.writeFile(wb, `inventario_${$('#fechaMatriz').val()}.xlsx`);
        }

        // ==================== FUNCIONES DE GRÁFICAS ====================

        function cargarGraficas() {
            let fechaInicio = $('#fechaInicioGrafica').val();
            let fechaFin = $('#fechaFinGrafica').val();
            if (!fechaInicio || !fechaFin) {
                alert('Seleccione ambas fechas');
                return;
            }

            $.ajax({
                url: '/inventario/evolucion',
                method: 'POST',
                data: {
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    mostrarResumenPeriodo(response.resumen_periodo);
                    mostrarGraficas(response);
                },
                error: function() { alert('Error al cargar gráficas'); }
            });
        }

        function mostrarResumenPeriodo(resumen) {
            let variacionClase = resumen.variacion_unidades >= 0 ? 'trend-up' : 'trend-down';

            // Formatear fechas
            let fechaInicialFormateada = formatearFechaDDMMYYYY(resumen.fecha_inicial);
            let fechaFinalFormateada = formatearFechaDDMMYYYY(resumen.fecha_final);

            $('#resumenPeriodo').html(`
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-number">${fechaInicialFormateada || 'N/A'}</div>
                <div class="summary-label">Inicial: ${numberFormat(resumen.unidad_inicial, 0)} und</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-number">${fechaFinalFormateada || 'N/A'}</div>
                <div class="summary-label">Final: ${numberFormat(resumen.unidad_final, 0)} und</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-number ${variacionClase}">${resumen.variacion_unidades >= 0 ? '+' : ''}${numberFormat(resumen.variacion_unidades, 0)}</div>
                <div class="summary-label">Variación: ${resumen.variacion_porcentaje >= 0 ? '+' : ''}${numberFormat(resumen.variacion_porcentaje, 1)}%</div>
            </div>
        </div>
    `);
        }

        function mostrarGraficas(data) {
            if (!data.tiene_datos) {
                $('#resumenPeriodo').html('<div class="col-12"><div class="alert alert-warning">No hay datos en el período seleccionado</div></div>');
                return;
            }

            // Formatear fechas para el gráfico de líneas
            let fechasFormateadas = data.line_chart.fechas.map(f => formatearFechaParaGrafica(f));

            // Formatear valores monetarios para tooltips
            let unidadesData = data.line_chart.unidades;
            let valoresData = data.line_chart.valores;

            if (lineChart) lineChart.destroy();
            lineChart = new Chart(document.getElementById('evolucionLineChart'), {
                type: 'line',
                data: {
                    labels: fechasFormateadas,
                    datasets: [
                        {
                            label: 'Unidades',
                            data: unidadesData,
                            borderColor: '#75a373',
                            backgroundColor: 'rgba(117,163,115,0.1)',
                            fill: true,
                            yAxisID: 'y',
                            tension: 0.3
                        },
                        {
                            label: 'Valor ($)',
                            data: valoresData,
                            borderColor: '#f95a02',
                            backgroundColor: 'rgba(249,90,2,0.1)',
                            fill: true,
                            yAxisID: 'y1',
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw;
                                    if (context.dataset.label === 'Valor ($)') {
                                        return `${label}: $${numberFormat(value, 2)}`;
                                    }
                                    return `${label}: ${numberFormat(value, 0)}`;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            title: { display: true, text: 'Unidades' },
                            ticks: {
                                callback: function(value) {
                                    return numberFormat(value, 0);
                                }
                            }
                        },
                        y1: {
                            position: 'right',
                            title: { display: true, text: 'Valor ($)' },
                            grid: { drawOnChartArea: false },
                            ticks: {
                                callback: function(value) {
                                    return '$' + numberFormat(value, 0);
                                }
                            }
                        }
                    }
                }
            });

            if (barChart) barChart.destroy();
            barChart = new Chart(document.getElementById('sucursalesBarChart'), {
                type: 'bar',
                data: {
                    labels: data.barras_sucursales.map(s => s.sucursal.length > 20 ? s.sucursal.substring(0, 18) + '...' : s.sucursal),
                    datasets: [{
                        label: 'Unidades',
                        data: data.barras_sucursales.map(s => s.unidades),
                        backgroundColor: '#75a373',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Unidades: ${numberFormat(context.raw, 0)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Unidades' },
                            ticks: {
                                callback: function(value) {
                                    return numberFormat(value, 0);
                                }
                            }
                        }
                    }
                }
            });

            if (topChart) topChart.destroy();
            topChart = new Chart(document.getElementById('topProductosChart'), {
                type: 'bar',
                data: {
                    labels: data.top_productos.map(p => p.descrip.length > 20 ? p.descrip.substring(0, 20) + '...' : p.descrip),
                    datasets: [{
                        label: 'Variación (Unidades)',
                        data: data.top_productos.map(p => Math.abs(p.variacion)),
                        backgroundColor: '#f95a02',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Variación: ±${numberFormat(context.raw, 0)} unidades`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Unidades' },
                            ticks: {
                                callback: function(value) {
                                    return numberFormat(value, 0);
                                }
                            }
                        }
                    }
                }
            });
        }


        function numberFormat(number, decimals) {
            if (number === null || number === undefined) return '0';
            // Asegurar que number sea número
            let num = typeof number === 'number' ? number : parseFloat(number);
            if (isNaN(num)) return '0';

            // Formato: puntos para miles, comas para decimales
            let options = {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
                useGrouping: true,
                // Para español de Venezuela
                style: 'decimal'
            };

            // Usar Intl.NumberFormat con locale 'es-VE' o 'es'
            return new Intl.NumberFormat('es-VE', options).format(num);
        }

        $(document).ready(function() {
            let hoy = new Date().toISOString().split('T')[0];
            let hace30Dias = new Date();
            hace30Dias.setDate(hace30Dias.getDate() - 30);

            $('#fechaSync, #fechaMatriz').val(hoy);
            $('#fechaInicioGrafica').val(hace30Dias.toISOString().split('T')[0]);
            $('#fechaFinGrafica').val(hoy);

            cargarSincronizacionHoy();
            setTimeout(() => cargarGraficas(), 500);

            // Inicializar navegación por teclado
            initSyncKeyboardNavigation();

            // Agregar botones flotantes (opcional)
            agregarBotonesNavegacionFlotantes();

            // Agregar atajo para recargar (tecla R)
            $(document).on('keydown', function(e) {
                if (!$('#sincronizacion-tab').hasClass('active')) return;
                if ($(e.target).is('input, textarea, select')) return;

                if (e.key === 'r' || e.key === 'R') {
                    e.preventDefault();
                    recargarSyncActual();
                } else if (e.key === '?' || (e.shiftKey && e.key === '/')) {
                    e.preventDefault();
                    mostrarAyudaTeclado();
                }
            });

            // Tooltips para los botones
            $('[title]').tooltip();


            $('#inventarioTabs button').on('shown.bs.tab', function(e) {
                const targetId = $(e.target).attr('aria-controls');
                if (targetId === 'sincronizacion') {
                    // Pequeño retraso para asegurar que el DOM está listo
                    setTimeout(() => {
                        mostrarNotificacion('Navegación por teclado activada (← → H R ?)', 'info');
                    }, 100);
                }
            });

        });
    </script>
@endsection
