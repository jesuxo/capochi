@extends('layouts.master')
@section('title', 'Seguimiento Diario de Inventario')

@section('css')
    <style>
        .summary-card {
            background: linear-gradient(135deg, #75a373 0%, #5a8a58 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.2s ease;
        }
        .summary-card:hover {
            transform: translateY(-3px);
        }
        .summary-number {
            font-size: 28px;
            font-weight: bold;
        }
        .summary-label {
            font-size: 12px;
            opacity: 0.9;
        }
        .filtros-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            align-items: flex-end;
        }
        .filtros-container .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }
        .tabla-seguimiento {
            font-size: 13px;
        }
        .tabla-seguimiento thead th {
            background: #f8f9fa;
            padding: 10px 8px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .tabla-seguimiento tbody td {
            padding: 8px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        .tabla-seguimiento tbody tr:hover {
            background-color: rgba(117, 163, 115, 0.05);
        }
        .tabla-seguimiento tbody tr.table-danger:hover {
            background-color: #f8d7da !important;
        }
        .scroll-horizontal {
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }
        .resumen-general {
            background: linear-gradient(135deg, #75a373 0%, #5a8a58 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .resumen-general .stat-number {
            font-size: 28px;
            font-weight: bold;
        }
        .resumen-general .stat-label {
            font-size: 13px;
            opacity: 0.9;
        }
        .screenshot-notification {
            position: fixed !important;
            top: 80px !important;
            right: 20px !important;
            z-index: 99999 !important;
            animation: slideInRight 0.3s ease;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .badge-productos {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .detalle-modal {
            max-height: 500px;
            overflow-y: auto;
        }
        .detalle-modal .list-group-item {
            border-left: 3px solid transparent;
        }
        .detalle-modal .list-group-item.tipo-cargo {
            border-left-color: #007bff;
        }
        .detalle-modal .list-group-item.tipo-descargo {
            border-left-color: #ffc107;
        }
        .detalle-modal .list-group-item.tipo-compra {
            border-left-color: #28a745;
        }
        .detalle-modal .list-group-item.tipo-venta {
            border-left-color: #dc3545;
        }
        .detalle-modal .list-group-item.tipo-devolucion-compra {
            border-left-color: #fd7e14;
        }
        .detalle-modal .list-group-item.tipo-devolucion-venta {
            border-left-color: #20c997;
        }
        .badge-movimiento {
            font-size: 10px;
            padding: 3px 8px;
        }
        .debug-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .debug-container pre {
            font-size: 11px;
            max-height: 200px;
            overflow: auto;
            background: #fff;
            padding: 10px;
            border-radius: 4px;
        }

        .diferencia-badge {
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }
        .diferencia-alta {
            background: #dc3545;
            color: white;
        }
        .diferencia-media {
            background: #ffc107;
            color: #000;
        }
        .diferencia-baja {
            background: #75a373;
            color: white;
        }
        .diferencia-positiva {
            background: #28a745;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="filtros-container">
                        <div>
                            <label class="form-label mb-1">🏪 Sucursal</label>
                            <select id="sucursalSelect" class="form-select form-select-sm" style="min-width: 200px;">
                                <option value="0">🌐 Seleccione una sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">🏪 {{ $sucursal->descrip }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label mb-1">📅 Fecha</label>
                            <input type="date" id="fechaSeguimiento" class="form-control form-control-sm" style="width: 180px;" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary" onclick="cargarSeguimiento()">
                                <i class="bi bi-search me-1"></i> Consultar
                            </button>
                            <button class="btn btn-sm btn-success" onclick="exportarSeguimientoExcel()">
                                <i class="bi bi-file-excel me-1"></i> Exportar
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="capturarSeguimiento()">
                                <i class="bi bi-camera me-1"></i> Capturar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleDebug()">
                                <i class="bi bi-bug me-1"></i> Depurar
                            </button>
                        </div>
                    </div>

                    <!-- Contenedor de resultados -->
                    <div id="seguimientoContent">
                        <div class="text-center py-5">
                            <i class="bi bi-arrow-repeat fs-1 d-block mb-3 text-muted"></i>
                            <h5>Seleccione una sucursal y fecha para ver el seguimiento</h5>
                            <p class="text-muted">El sistema mostrará el inventario inicial, movimientos y diferencias del día</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalle de producto -->
    <div class="modal fade" id="modalDetalleProducto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-box-seam me-2"></i>
                        <span id="modalDetalleTitulo"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDetalleContenido">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Cargando detalle...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        let datosSeguimiento = null;
        let debugVisible = false;

        $(document).ready(function() {
            // Cargar automáticamente si hay una sucursal seleccionada por defecto
            let sucursalSelect = $('#sucursalSelect');
            if (sucursalSelect.val() != 0) {
                cargarSeguimiento();
            }

            // Evento cambio de sucursal
            sucursalSelect.on('change', function() {
                if ($(this).val() != 0) {
                    cargarSeguimiento();
                } else {
                    $('#seguimientoContent').html(`
                        <div class="text-center py-5">
                            <i class="bi bi-building fs-1 d-block mb-3 text-muted"></i>
                            <h5>Seleccione una sucursal para ver el seguimiento</h5>
                        </div>
                    `);
                }
            });
        });

        function cargarSeguimiento() {
            let sucursalId = $('#sucursalSelect').val();
            let fecha = $('#fechaSeguimiento').val();

            if (!sucursalId || sucursalId == 0) {
                mostrarNotificacion('⚠️ Seleccione una sucursal', 'warning');
                return;
            }

            if (!fecha) {
                fecha = new Date().toISOString().split('T')[0];
                $('#fechaSeguimiento').val(fecha);
            }

            $('#seguimientoContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">Cargando seguimiento de inventario...</p>
                </div>
            `);

            $.ajax({
                url: '/inventario/seguimiento-diario/data',
                method: 'POST',
                data: {
                    sucursal_id: sucursalId,
                    fecha: fecha,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        datosSeguimiento = response;
                        renderSeguimiento(response);
                    } else {
                        mostrarError(response.message || 'Error al cargar datos');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    mostrarError('Error al cargar el seguimiento. Verifique la conexión.');
                }
            });
        }

        function renderSeguimiento(data) {
            // Verificar si hay datos
            if (!data.data || data.data.length === 0) {
                let mensaje = data.resumen?.mensaje || 'No hay movimientos para esta fecha';
                let html = `
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-info-circle fs-2 d-block mb-3"></i>
                        <h5>${mensaje}</h5>
                `;

                if (data.resumen?.ultima_fecha_trabajada) {
                    html += `
                        <p class="mb-0">
                            Última fecha trabajada: <strong>${data.resumen.ultima_fecha_trabajada}</strong>
                            ${data.resumen.dias_sin_sincronizar > 1 ? `(hace ${data.resumen.dias_sin_sincronizar} días)` : ''}
                        </p>
                    `;
                }

                html += `</div>`;
                $('#seguimientoContent').html(html);
                return;
            }

            let fechaAnteriorMostrar = data.resumen.fecha_anterior_trabajada || 'Sin dato anterior';
            let fechaActualMostrar = data.resumen.fecha_actual || data.fecha_formateada;

            let html = `
                <!-- Resumen General -->
                <div class="resumen-general">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="stat-number">${data.resumen.total_productos}</div>
                            <div class="stat-label">Productos</div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-number">${numberFormat(data.resumen.inventario_inicial_total, 3)}</div>
                            <div class="stat-label">Inv. Inicial (${fechaAnteriorMostrar})</div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-number">${numberFormat(data.resumen.inventario_final_total, 3)}</div>
                            <div class="stat-label">Inv. Final (${fechaActualMostrar})</div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-number" style="color: ${data.resumen.diferencia_total >= 0 ? '#ffd700' : '#28a745'}">
                                ${data.resumen.diferencia_total >= 0 ? '+' : ''}${numberFormat(data.resumen.diferencia_total, 3)}
                            </div>
                            <div class="stat-label">Diferencia</div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-number">${numberFormat(data.resumen.total_compras + data.resumen.total_cargos, 3)}</div>
                            <div class="stat-label">Entradas</div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-number">${numberFormat(data.resumen.total_ventas + data.resumen.total_descargos, 3)}</div>
                            <div class="stat-label">Salidas</div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="opacity-75">
                                <i class="bi bi-info-circle me-1"></i>
                                ${data.sucursal} - Período: ${fechaAnteriorMostrar} → ${fechaActualMostrar}
                                ${data.resumen.dias_sin_sincronizar > 1 ? `<span class="badge bg-warning text-dark ms-2">⚠️ ${data.resumen.dias_sin_sincronizar} días sin sincronizar</span>` : ''}
                                <span class="badge bg-primary ms-2">Cargos: ${numberFormat(data.resumen.total_cargos, 3)}</span>
                                <span class="badge bg-warning ms-1">Descargos: ${numberFormat(data.resumen.total_descargos, 3)}</span>
                                <span class="badge bg-success ms-1">Compras: ${numberFormat(data.resumen.total_compras, 3)}</span>
                                <span class="badge bg-danger ms-1">Ventas: ${numberFormat(data.resumen.total_ventas, 3)}</span>
                            </small>
                        </div>
                    </div>
                </div>
            `;

            // Advertencia por días sin sincronizar
            if (data.resumen.dias_sin_sincronizar > 1) {
                html += `
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> La última fecha trabajada fue
                        <strong>${data.resumen.fecha_anterior_trabajada}</strong>
                        (hace ${data.resumen.dias_sin_sincronizar} días).
                        Las comparaciones se realizan contra esa fecha.
                    </div>
                `;
            }

            // Tabla de seguimiento - Encabezados con Diferencia en lugar de Merma
            html += `
                <div class="scroll-horizontal">
                    <table class="table table-bordered table-sm tabla-seguimiento">
                        <thead>
                            <tr>
                                <th style="min-width: 80px;">Código</th>
                                <th style="min-width: 180px;">Producto</th>
                                <th style="min-width: 120px;">Categoría</th>
                                <th style="min-width: 70px; text-align: center;" title="Inventario de la fecha anterior trabajada">Inv. Inicial</th>
                                <th style="min-width: 70px; text-align: center;" class="text-primary">Cargos (+)</th>
                                <th style="min-width: 70px; text-align: center;" class="text-warning">Descargos (-)</th>
                                <th style="min-width: 70px; text-align: center;" class="text-success">Compras (+)</th>
                                <th style="min-width: 70px; text-align: center;" class="text-danger">Ventas (-)</th>
                                <th style="min-width: 70px; text-align: center;" title="Inventario que debería haber según los movimientos">Debe Haber</th>
                                <th style="min-width: 70px; text-align: center;" title="Inventario real de la fecha actual">Inv. Final</th>
                                <th style="min-width: 80px; text-align: center;">Diferencia</th>
                                <th style="min-width: 50px; text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.data.forEach(item => {
                let diffClass = '';
                let diffColor = '';
                let diffTexto = '';
                let absDiff = Math.abs(item.diferencia);

                if (item.diferencia > 0) {
                    diffClass = 'diferencia-alta';
                    diffColor = '#dc3545';
                    diffTexto = `-${numberFormat(absDiff, 3)}`;
                } else if (item.diferencia < 0) {
                    diffClass = 'diferencia-positiva';
                    diffColor = '#28a745';
                    diffTexto = `+${numberFormat(absDiff, 3)}`;
                } else {
                    diffClass = 'diferencia-baja';
                    diffColor = '#6c757d';
                    diffTexto = '0';
                }

                let rowClass = item.diferencia > 100 ? 'table-danger' : '';
                if (item.diferencia > 20 && item.diferencia <= 100) {
                    rowClass = 'table-warning';
                }

                html += `
                    <tr class="${rowClass}">
                        <td><strong>${item.codprod}</strong></td>
                        <td>${item.descrip}</td>
                        <td><small>${item.categoria}</small></td>
                        <td class="text-center fw-bold">${numberFormat(item.inventario_inicial, 3)}</td>
                        <td class="text-center text-primary">${item.cargos > 0 ? '+' + numberFormat(item.cargos, 3) : '-'}</td>
                        <td class="text-center text-warning">${item.descargos > 0 ? '-' + numberFormat(item.descargos, 3) : '-'}</td>
                        <td class="text-center text-success">${item.compras > 0 ? '+' + numberFormat(item.compras, 3) : '-'}</td>
                        <td class="text-center text-danger">${item.ventas > 0 ? '-' + numberFormat(item.ventas, 3) : '-'}</td>
                        <td class="text-center fw-bold">${numberFormat(item.deberia_haber, 3)}</td>
                        <td class="text-center fw-bold">${numberFormat(item.inventario_final, 3)}</td>
                        <td class="text-center">
                            <span class="diferencia-badge ${diffClass}" style="color: ${item.diferencia <= 0 ? 'white' : 'black'}; background-color: ${diffColor};">
                                ${diffTexto}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" onclick="verDetalleProducto('${item.codprod}')" title="Ver detalle de movimientos">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            // Fila de totales
            if (data.data.length > 0) {
                let totalInicial = data.data.reduce((sum, item) => sum + item.inventario_inicial, 0);
                let totalCargos = data.data.reduce((sum, item) => sum + item.cargos, 0);
                let totalDescargos = data.data.reduce((sum, item) => sum + item.descargos, 0);
                let totalCompras = data.data.reduce((sum, item) => sum + item.compras, 0);
                let totalVentas = data.data.reduce((sum, item) => sum + item.ventas, 0);
                let totalDebeHaber = data.data.reduce((sum, item) => sum + item.deberia_haber, 0);
                let totalFinal = data.data.reduce((sum, item) => sum + item.inventario_final, 0);
                let totalDiferencia = data.data.reduce((sum, item) => sum + item.diferencia, 0);

                let diffTotalClass = totalDiferencia > 100 ? 'diferencia-alta' : (totalDiferencia > 20 ? 'diferencia-media' : 'diferencia-baja');
                let diffTotalColor = totalDiferencia > 100 ? '#dc3545' : (totalDiferencia > 20 ? '#ffc107' : '#75a373');
                let diffTotalTexto = totalDiferencia > 0 ? `-${numberFormat(Math.abs(totalDiferencia), 3)}` : (totalDiferencia < 0 ? `+${numberFormat(Math.abs(totalDiferencia), 3)}` : '0');

                html += `
                    <tr class="table-primary fw-bold">
                        <td colspan="3" class="text-center">TOTALES</td>
                        <td class="text-center">${numberFormat(totalInicial, 3)}</td>
                        <td class="text-center text-primary">${totalCargos > 0 ? '+' + numberFormat(totalCargos, 3) : '-'}</td>
                        <td class="text-center text-warning">${totalDescargos > 0 ? '-' + numberFormat(totalDescargos, 3) : '-'}</td>
                        <td class="text-center text-success">${totalCompras > 0 ? '+' + numberFormat(totalCompras, 3) : '-'}</td>
                        <td class="text-center text-danger">${totalVentas > 0 ? '-' + numberFormat(totalVentas, 3) : '-'}</td>
                        <td class="text-center">${numberFormat(totalDebeHaber, 3)}</td>
                        <td class="text-center">${numberFormat(totalFinal, 3)}</td>
                        <td class="text-center">
                            <span class="diferencia-badge ${diffTotalClass}" style="background-color: ${diffTotalColor}; color: ${totalDiferencia <= 0 ? 'white' : 'black'};">
                                ${diffTotalTexto}
                            </span>
                        </td>
                        <td></td>
                    </tr>
                `;
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            // Pie de página
            html += `
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                Fecha de consulta: ${data.fecha_formateada}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-building me-1"></i>
                                Sucursal: ${data.sucursal}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-clock-history me-1"></i>
                                Generado: ${new Date().toLocaleString('es-ES')}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                ${data.data.length} productos con movimientos
                            </small>
                        </div>
                    </div>
                </div>
            `;

            $('#seguimientoContent').html(html);
            $('[title]').tooltip({ placement: 'top' });
        }

        function toggleDebug() {
            $('#debugContent').toggle();
            debugVisible = !debugVisible;
        }

        function mostrarError(mensaje) {
            $('#seguimientoContent').html(`
                <div class="alert alert-danger text-center py-4">
                    <i class="bi bi-exclamation-triangle fs-2 d-block mb-3"></i>
                    <h5>Error</h5>
                    <p class="mb-0">${mensaje}</p>
                </div>
            `);
        }

        function verDetalleProducto(codprod) {
            let sucursalId = $('#sucursalSelect').val();
            let fecha = $('#fechaSeguimiento').val();

            if (!sucursalId || sucursalId == 0) {
                mostrarNotificacion('⚠️ Seleccione una sucursal', 'warning');
                return;
            }

            $('#modalDetalleTitulo').text(`Detalle de movimientos - ${codprod}`);
            $('#modalDetalleProducto').modal('show');
            $('#modalDetalleContenido').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">Cargando detalle del producto...</p>
                </div>
            `);

            $.ajax({
                url: '/inventario/seguimiento-diario/detalle-producto',
                method: 'POST',
                data: {
                    codprod: codprod,
                    fecha: fecha,
                    sucursal_id: sucursalId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        renderDetalleProducto(response);
                    } else {
                        $('#modalDetalleContenido').html(`
                            <div class="alert alert-danger">${response.message || 'Error al cargar detalle'}</div>
                        `);
                    }
                },
                error: function() {
                    $('#modalDetalleContenido').html(`
                        <div class="alert alert-danger">Error al cargar el detalle</div>
                    `);
                }
            });
        }

        function renderDetalleProducto(data) {
            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Producto:</strong> ${data.producto.codprod} - ${data.producto.descrip}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-secondary">
                            <strong>Categoría:</strong> ${data.producto.categoria}
                            <span class="badge bg-primary ms-2">${data.total_movimientos} movimientos</span>
                        </div>
                    </div>
                </div>
            `;

            if (data.detalle.length === 0) {
                html += `<div class="alert alert-warning">No hay movimientos registrados para este producto en esta fecha</div>`;
            } else {
                html += `
                    <div class="detalle-modal">
                        <div class="list-group">
                `;

                data.detalle.forEach(item => {
                    let tipoClass = 'tipo-' + item.tipo.toLowerCase().replace(' ', '-');
                    let badgeClass = '';
                    let icon = '';

                    switch(item.tipo) {
                        case 'CARGO':
                            badgeClass = 'bg-primary';
                            icon = 'bi-arrow-down-circle';
                            break;
                        case 'DESCARGO':
                            badgeClass = 'bg-warning';
                            icon = 'bi-arrow-up-circle';
                            break;
                        case 'COMPRA':
                            badgeClass = 'bg-success';
                            icon = 'bi-arrow-down-circle';
                            break;
                        case 'VENTA':
                            badgeClass = 'bg-danger';
                            icon = 'bi-arrow-up-circle';
                            break;
                        case 'DEVOLUCIÓN COMPRA':
                            badgeClass = 'bg-orange';
                            icon = 'bi-arrow-up-circle';
                            break;
                        case 'DEVOLUCIÓN VENTA':
                            badgeClass = 'bg-teal';
                            icon = 'bi-arrow-down-circle';
                            break;
                        default:
                            badgeClass = 'bg-secondary';
                            icon = 'bi-circle';
                    }

                    let cantidadClass = item.cantidad > 0 ? 'text-success' : 'text-danger';
                    let cantidadTexto = item.cantidad > 0 ? `+${numberFormat(item.cantidad, 3)}` : numberFormat(item.cantidad, 3);

                    html += `
                        <div class="list-group-item ${tipoClass}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge ${badgeClass} badge-movimiento">
                                        <i class="bi ${icon} me-1"></i>
                                        ${item.tipo}
                                    </span>
                                    <span class="ms-2">
                                        <i class="bi bi-receipt me-1"></i>
                                        ${item.referencia}
                                    </span>
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-clock me-1"></i>
                                        ${item.fecha}
                                    </small>
                                </div>
                                <div>
                                    <span class="fw-bold ${cantidadClass}">${cantidadTexto}</span>
                                </div>
                            </div>
                            ${item.observacion ? `<div class="mt-1"><small class="text-muted"><i class="bi bi-chat me-1"></i>${item.observacion}</small></div>` : ''}
                            ${item.items ? `<div class="mt-1"><small class="text-muted"><i class="bi bi-box me-1"></i>${item.items}</small></div>` : ''}
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            }

            $('#modalDetalleContenido').html(html);
        }

        function numberFormat(number, decimals) {
            if (number === null || number === undefined) return '0';
            let num = typeof number === 'number' ? number : parseFloat(number);
            if (isNaN(num)) return '0';

            // Convertir a string y separar parte entera y decimal
            let str = num.toString();
            let parts = str.split('.');
            let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            let decimalPart = parts[1] || '';

            // Si tiene decimales, mostrarlos sin redondear
            if (decimalPart.length > 0) {
                // Limitar a 3 decimales pero sin redondear
                let maxDecimals = Math.min(decimalPart.length, 3);
                let decimalTrimmed = decimalPart.substring(0, maxDecimals);
                // Eliminar ceros al final
                decimalTrimmed = decimalTrimmed.replace(/0+$/, '');
                if (decimalTrimmed.length > 0) {
                    return integerPart + ',' + decimalTrimmed;
                }
                return integerPart;
            }
            return integerPart;
        }

        function mostrarNotificacion(mensaje, tipo = 'success') {
            $('.screenshot-notification').remove();

            let bgColor = tipo === 'success' ? '#75a373' : (tipo === 'warning' ? '#ffc107' : '#75a373');
            let icono = tipo === 'success' ? 'check-circle-fill' : (tipo === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill');
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

        function exportarSeguimientoExcel() {
            if (!datosSeguimiento || !datosSeguimiento.data || datosSeguimiento.data.length === 0) {
                mostrarNotificacion('⚠️ No hay datos para exportar', 'warning');
                return;
            }

            let data = [];

            // Cabecera - SIN columnas de Merma
            data.push([
                'Código', 'Producto', 'Categoría', 'Inv. Inicial', 'Cargos', 'Descargos',
                'Compras', 'Ventas', 'Debe Haber', 'Inv. Final', 'Diferencia'
            ]);

            // Datos
            datosSeguimiento.data.forEach(item => {
                data.push([
                    item.codprod,
                    item.descrip,
                    item.categoria,
                    item.inventario_inicial,
                    item.cargos,
                    item.descargos,
                    item.compras,
                    item.ventas,
                    item.deberia_haber,
                    item.inventario_final,
                    item.diferencia
                ]);
            });

            let ws = XLSX.utils.aoa_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Seguimiento');
            XLSX.writeFile(wb, `seguimiento_inventario_${datosSeguimiento.fecha}.xlsx`);
            mostrarNotificacion('✅ Seguimiento exportado a Excel', 'success');
        }

        function capturarSeguimiento() {
            const element = document.querySelector('#seguimientoContent');
            if (!element || !datosSeguimiento || datosSeguimiento.data.length === 0) {
                mostrarNotificacion('⚠️ No hay datos para capturar', 'warning');
                return;
            }

            mostrarNotificacion('📸 Generando captura...', 'info');

            html2canvas(element, {
                scale: 2.5,
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true,
                windowHeight: element.scrollHeight
            }).then(canvas => {
                canvas.toBlob(function(blob) {
                    if (navigator.clipboard && navigator.clipboard.write) {
                        const clipboardItem = new ClipboardItem({ [blob.type]: blob });
                        navigator.clipboard.write([clipboardItem]).then(() => {
                            mostrarNotificacion('✅ Captura copiada al portapapeles!', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, `seguimiento_${datosSeguimiento.fecha}`);
                        });
                    } else {
                        descargarImagen(canvas, `seguimiento_${datosSeguimiento.fecha}`);
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('❌ Error al capturar', 'error');
            });
        }

        function descargarImagen(canvas, nombre) {
            const link = document.createElement('a');
            link.download = `${nombre}_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }
    </script>
@endsection
