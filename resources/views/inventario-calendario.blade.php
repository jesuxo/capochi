{{-- resources/views/inventario-calendario.blade.php --}}
@extends('layouts.master')
@section('title', 'Calendario de Sincronizaciones')

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

        /* Estilos del calendario */
        .calendario-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .mes-navegacion {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .mes-titulo {
            font-size: 24px;
            font-weight: bold;
            color: #75a373;
        }

        .tabla-calendario {
            font-size: 13px;
            margin-bottom: 0;
        }

        .tabla-calendario thead th {
            background: #f8f9fa;
            text-align: center;
            padding: 10px 5px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid #dee2e6;
        }

        .tabla-calendario tbody td {
            text-align: center;
            padding: 6px 4px;
            vertical-align: middle;
            border: 1px solid #e9ecef;
        }

        .tabla-calendario tbody tr:hover {
            background-color: rgba(117, 163, 115, 0.05) !important;
        }

        .celda-dia {
            min-width: 35px;
            max-width: 35px;
            cursor: default;
        }

        .celda-sync {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            line-height: 30px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            transition: all 0.2s ease;
        }

        .celda-sync.sincronizado {
            background: #75a373;
            color: white;
            box-shadow: 0 2px 4px rgba(117, 163, 115, 0.3);
        }

        .celda-sync.no-sincronizado {
            background: #dc3545;
            color: white;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .celda-sync.dia-descanso {
            background: #f0e6d3;
            color: #8a7a6a;
            font-size: 16px;
            border: 2px dashed #c4b5a5;
            width: 30px;
            height: 30px;
            line-height: 28px;
            text-align: center;
            display: inline-block;
            border-radius: 50%;
        }

        .celda-sync.sincronizado:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 8px rgba(117, 163, 115, 0.4);
            cursor: pointer;
        }

        .celda-sync.no-sincronizado:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
            cursor: pointer;
        }

        .celda-sync.dia-descanso:hover {
            transform: scale(1.1);
            background: #e8d5c0;
            cursor: default;
        }

        .fila-sucursal {
            transition: background-color 0.2s ease;
        }

        .fila-sucursal:hover {
            background-color: rgba(117, 163, 115, 0.05) !important;
        }

        .fila-sucursal.resaltada {
            background-color: rgba(117, 163, 115, 0.1) !important;
        }

        .cumplimiento-badge {
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            min-width: 60px;
            display: inline-block;
        }

        .cumplimiento-alto {
            background: #75a373;
            color: white;
        }

        .cumplimiento-medio {
            background: #ffc107;
            color: #000;
        }

        .cumplimiento-bajo {
            background: #dc3545;
            color: white;
        }

        .leyenda-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 15px;
        }

        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .leyenda-circulo {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
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

        .btn-group-mes {
            display: flex;
            gap: 5px;
        }

        .btn-group-mes .btn {
            padding: 5px 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            background: white;
            transition: all 0.2s ease;
        }

        .btn-group-mes .btn:hover {
            background: #75a373;
            color: white;
            border-color: #75a373;
        }

        .btn-group-mes .btn.active {
            background: #75a373;
            color: white;
            border-color: #75a373;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            border-radius: 10px;
        }

        .scroll-horizontal {
            overflow-x: auto;
            position: relative;
            max-height: 600px;
            overflow-y: auto;
        }

        .nombre-sucursal {
            font-weight: 600;
            min-width: 180px;
            text-align: left;
            padding-left: 15px !important;
        }

        .col-descanso {
            min-width: 100px;
            text-align: center;
            font-size: 12px;
        }

        .total-col {
            background: #f8f9fa;
            font-weight: bold;
            min-width: 80px;
        }

        .dia-semana-header {
            font-size: 10px;
            color: #6c757d;
            display: block;
            margin-top: 2px;
            font-weight: normal;
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

        .badge-productos {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .btn-capture {
            padding: 4px 8px;
            font-size: 12px;
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

        @media (max-width: 768px) {
            .tabla-calendario {
                font-size: 11px;
            }
            .celda-sync {
                width: 24px;
                height: 24px;
                line-height: 24px;
                font-size: 9px;
            }
            .celda-sync.dia-descanso {
                width: 24px;
                height: 24px;
                line-height: 22px;
                font-size: 12px;
            }
            .nombre-sucursal {
                min-width: 100px;
                font-size: 12px;
            }
            .mes-titulo {
                font-size: 18px;
            }
            .filtros-container {
                flex-direction: column;
                gap: 10px;
            }
            .resumen-general .stat-number {
                font-size: 20px;
            }
            .col-descanso {
                min-width: 70px;
                font-size: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-none">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-month me-2"></i>Calendario de Sincronizaciones
                    </h4>
                    <p class="text-white-50 mb-0 small">Control de sincronización diaria por sucursal</p>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="filtros-container">
                        <div>
                            <label class="form-label mb-1">🏪 Sucursal</label>
                            <select id="sucursalSelect" class="form-select form-select-sm" style="min-width: 200px;">
                                <option value="0">🌐 Todas las sucursales</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">🏪 {{ $sucursal->descrip }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label mb-1">📅 Mes</label>
                            <div class="btn-group-mes">
                                <button class="btn btn-sm btn-outline-secondary" onclick="cambiarMes(-1)">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button class="btn btn-sm btn-primary" id="mesActual" style="min-width: 140px; font-weight: bold;">
                                    {{ now()->translatedFormat('F Y') }}
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="cambiarMes(1)">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-primary" onclick="cargarCalendario()">
                                <i class="bi bi-search me-1"></i> Consultar
                            </button>
                            <button class="btn btn-sm btn-success" onclick="exportarCalendarioExcel()">
                                <i class="bi bi-file-excel me-1"></i> Exportar
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="capturarCalendario()">
                                <i class="bi bi-camera me-1"></i> Capturar
                            </button>
                        </div>
                    </div>

                    <!-- Contenedor del calendario -->
                    <div id="calendarioContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 text-muted">Cargando calendario de sincronizaciones...</p>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="leyenda-container">
                        <div class="leyenda-item">
                            <span class="leyenda-circulo" style="background: #75a373;"></span>
                            <span>Sincronizado <small class="text-muted">(✓)</small></span>
                        </div>
                        <div class="leyenda-item">
                            <span class="leyenda-circulo" style="background: #dc3545;"></span>
                            <span>No sincronizado <small class="text-muted">(✗)</small></span>
                        </div>
                        <div class="leyenda-item">
                            <span class="leyenda-circulo" style="background: #f0e6d3; border: 2px dashed #c4b5a5;"></span>
                            <span>Día de descanso <small class="text-muted">(✧)</small></span>
                        </div>
                        <div class="leyenda-item">
                            <span class="badge bg-success">≥80%</span>
                            <span>Cumplimiento Alto</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="badge bg-warning" style="color: #000;">50-79%</span>
                            <span>Cumplimiento Medio</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="badge bg-danger">&lt;50%</span>
                            <span>Cumplimiento Bajo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        let mesActual = {{ now()->month }};
        let anioActual = {{ now()->year }};
        let datosCalendario = null;
        let sucursalSeleccionada = 0;

        $(document).ready(function() {
            // Cargar calendario inicial
            cargarCalendario();

            // Evento cambio de sucursal
            $('#sucursalSelect').on('change', function() {
                sucursalSeleccionada = parseInt($(this).val());
                cargarCalendario();
            });

            // Evento tecla R para recargar
            $(document).on('keydown', function(e) {
                if ($(e.target).is('input, textarea, select')) return;
                if (e.key === 'r' || e.key === 'R') {
                    e.preventDefault();
                    cargarCalendario();
                    mostrarNotificacion('🔄 Calendario recargado', 'info');
                }
            });
        });

        function cambiarMes(delta) {
            let fecha = new Date(anioActual, mesActual - 1 + delta);
            mesActual = fecha.getMonth() + 1;
            anioActual = fecha.getFullYear();
            $('#mesActual').text(fecha.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }));
            cargarCalendario();
        }

        function cargarCalendario() {
            $('#calendarioContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Cargando calendario...</p>
        </div>
    `);

            $.ajax({
                url: '/inventario/calendario-sincronizacion/data',
                method: 'POST',
                data: {
                    mes: mesActual,
                    anio: anioActual,
                    sucursal_id: sucursalSeleccionada,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        datosCalendario = response;
                        renderCalendario(response);
                    } else {
                        mostrarError(response.message || 'Error al cargar datos');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    mostrarError('Error al cargar el calendario. Verifique la conexión.');
                }
            });
        }

        function renderCalendario(data) {
            if (!data.sucursales || data.sucursales.length === 0) {
                $('#calendarioContent').html(`
            <div class="alert alert-warning text-center py-4">
                <i class="bi bi-info-circle fs-2 d-block mb-3"></i>
                <h5>No hay sucursales para mostrar</h5>
                <p class="mb-0">No se encontraron sucursales activas para este comercial.</p>
            </div>
        `);
                return;
            }

            let html = `
        <div class="scroll-horizontal">
            <table class="table table-bordered table-sm tabla-calendario">
                <thead>
                    <tr>
                        <th style="min-width: 180px; text-align: left; padding-left: 15px;">
                            <i class="bi bi-building me-1"></i> Sucursal
                        </th>
                        <th style="min-width: 100px; text-align: center;">
                            <i class="bi bi-calendar2-week me-1"></i> Descanso
                        </th>
    `;

            // Cabecera con días del mes
            let diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            let primerDia = new Date(data.anio, data.mes - 1, 1);
            let ultimoDia = new Date(data.anio, data.mes, 0);
            let hoy = new Date();
            let hoyStr = hoy.toISOString().split('T')[0];

            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                let fecha = new Date(data.anio, data.mes - 1, dia);
                let diaSemana = diasSemana[fecha.getDay()];
                let fechaStr = fecha.toISOString().split('T')[0];
                let esHoy = fechaStr === hoyStr;

                html += `
            <th class="celda-dia ${esHoy ? 'table-primary' : ''}" title="${diaSemana} ${dia}">
                ${dia}
                <small class="dia-semana-header">${diaSemana}</small>
                ${esHoy ? '<span class="badge bg-primary" style="font-size: 8px; display: block;">HOY</span>' : ''}
            </th>
        `;
            }

            html += `
                    <th class="total-col" style="min-width: 80px; text-align: center;">
                        <i class="bi bi-graph-up me-1"></i> Cumpl.
                    </th>
                    <th class="total-col" style="min-width: 60px; text-align: center;">
                        <i class="bi bi-check-circle me-1"></i> ✓
                    </th>
                </tr>
            </thead>
            <tbody>
    `;

            // Estadísticas generales
            let totalSucursales = data.sucursales.length;
            let totalDiasHabilesGlobal = 0;
            let totalDiasSyncGlobal = 0;
            let totalDiasDescansoGlobal = 0;
            let sucursalesCumplimientoAlto = 0;
            let sucursalesCumplimientoMedio = 0;
            let sucursalesCumplimientoBajo = 0;

            data.sucursales.forEach((sucursal, index) => {
                let cumplimiento = sucursal.totales.cumplimiento;
                let claseCumplimiento = cumplimiento >= 80 ? 'cumplimiento-alto' : (cumplimiento >= 50 ? 'cumplimiento-medio' : 'cumplimiento-bajo');
                let colorFila = index % 2 === 0 ? '' : 'table-light';

                // Contar estadísticas
                if (cumplimiento >= 80) sucursalesCumplimientoAlto++;
                else if (cumplimiento >= 50) sucursalesCumplimientoMedio++;
                else sucursalesCumplimientoBajo++;

                totalDiasHabilesGlobal += sucursal.totales.dias_habiles;
                totalDiasSyncGlobal += sucursal.totales.dias_sincronizados;
                totalDiasDescansoGlobal += sucursal.totales.dias_descanso;

                // Icono del día de descanso
                let iconoDescanso = '🟢';
                switch(sucursal.dia_descanso) {
                    case 0: iconoDescanso = '🌅'; break;
                    case 1: iconoDescanso = '🌙'; break;
                    case 2: iconoDescanso = '🔥'; break;
                    case 3: iconoDescanso = '💧'; break;
                    case 4: iconoDescanso = '🌳'; break;
                    case 5: iconoDescanso = '🌟'; break;
                    case 6: iconoDescanso = '🎉'; break;
                }

                html += `
            <tr class="fila-sucursal ${colorFila}" data-sucursal-id="${sucursal.id}">
                <td class="nombre-sucursal">
                    <i class="bi bi-building me-1"></i>
                    ${sucursal.nombre}
                    <span class="badge-productos ms-2">${sucursal.totales.dias_habiles} días</span>
                </td>
                <td class="col-descanso">
                    ${iconoDescanso} ${sucursal.nombre_dia_descanso}
                </td>
        `;

                // Días del mes
                sucursal.dias.forEach((dia) => {
                    let claseCelda = '';
                    let contenido = '';
                    let titulo = `${dia.dia_semana} ${dia.dia_numero}`;

                    if (dia.es_dia_descanso) {
                        claseCelda = 'dia-descanso';
                        contenido = '✧';
                        titulo += ' - Día de descanso 🏖️';
                    } else if (dia.tiene_sincronizacion) {
                        claseCelda = 'sincronizado';
                        contenido = dia.productos_sincronizados > 0 ? dia.productos_sincronizados : '✓';
                        titulo += ` - Sincronizado (${dia.productos_sincronizados} productos) ✅`;
                    } else {
                        claseCelda = 'no-sincronizado';
                        contenido = '✗';
                        titulo += ' - NO sincronizado ⚠️';
                    }

                    // Si es hoy, agregar borde
                    let hoy = new Date();
                    let fechaStr = dia.fecha;
                    let esHoy = fechaStr === hoy.toISOString().split('T')[0];

                    html += `
                <td ${esHoy ? 'class="table-primary"' : ''}>
                    <div class="celda-sync ${claseCelda}" title="${titulo}">
                        ${contenido}
                    </div>
                </td>
            `;
                });

                // Totales
                html += `
                    <td class="total-col text-center">
                        <span class="cumplimiento-badge ${claseCumplimiento}">
                            ${cumplimiento}%
                        </span>
                    </td>
                    <td class="total-col text-center">
                        ${sucursal.totales.dias_sincronizados}/${sucursal.totales.dias_habiles}
                        <br><small class="text-muted" style="font-size: 9px;">Desc: ${sucursal.totales.dias_descanso}</small>
                    </td>
                </tr>
        `;
            });

            // Fila de resumen general
            let cumplimientoPromedio = totalDiasHabilesGlobal > 0 ? round((totalDiasSyncGlobal / totalDiasHabilesGlobal) * 100, 1) : 0;
            let clasePromedio = cumplimientoPromedio >= 80 ? 'cumplimiento-alto' : (cumplimientoPromedio >= 50 ? 'cumplimiento-medio' : 'cumplimiento-bajo');

            html += `
                <tr class="table-primary" style="font-weight: bold;">
                    <td colspan="2" class="text-center">
                        <i class="bi bi-award me-1"></i> PROMEDIO GENERAL
                    </td>
    `;

            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                html += `<td></td>`;
            }

            html += `
                    <td class="total-col text-center">
                        <span class="cumplimiento-badge ${clasePromedio}">
                            ${cumplimientoPromedio}%
                        </span>
                    </td>
                    <td class="total-col text-center">
                        ${totalDiasSyncGlobal}/${totalDiasHabilesGlobal}
                        <br><small class="text-muted" style="font-size: 9px;">Desc: ${totalDiasDescansoGlobal}</small>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    `;

            // Agregar resumen general
            let resumenHtml = `
        <div class="resumen-general">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-number">${totalSucursales}</div>
                    <div class="stat-label">Sucursales</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">${totalDiasHabilesGlobal}</div>
                    <div class="stat-label">Días hábiles totales</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number" style="color: #ffd700;">${totalDiasSyncGlobal}</div>
                    <div class="stat-label">Días sincronizados</div>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">${cumplimientoPromedio}%</div>
                    <div class="stat-label">Cumplimiento general</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="d-flex gap-3 flex-wrap">
                        <small class="opacity-75">
                            <i class="bi bi-info-circle me-1"></i>
                            Cada sucursal tiene su propio día de descanso semanal
                        </small>
                        <small class="opacity-75">
                            <span class="badge bg-success">${sucursalesCumplimientoAlto}</span> Alto
                            <span class="badge bg-warning" style="color: #000;">${sucursalesCumplimientoMedio}</span> Medio
                            <span class="badge bg-danger">${sucursalesCumplimientoBajo}</span> Bajo
                        </small>
                    </div>
                </div>
            </div>
        </div>
    `;

            $('#calendarioContent').html(`
        ${resumenHtml}
        ${html}
    `);

            // Agregar tooltip a las celdas
            $('.celda-sync').tooltip({ placement: 'top' });
        }

        function mostrarError(mensaje) {
            $('#calendarioContent').html(`
        <div class="alert alert-danger text-center py-4">
            <i class="bi bi-exclamation-triangle fs-2 d-block mb-3"></i>
            <h5>Error</h5>
            <p class="mb-0">${mensaje}</p>
        </div>
    `);
        }

        function round(number, decimals) {
            return Math.round(number * Math.pow(10, decimals)) / Math.pow(10, decimals);
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

        function exportarCalendarioExcel() {
            if (!datosCalendario) {
                mostrarNotificacion('⚠️ Primero consulte el calendario', 'warning');
                return;
            }

            let data = [];

            // Cabecera
            let headerRow = ['Sucursal', 'Día Descanso'];
            let ultimoDia = new Date(datosCalendario.anio, datosCalendario.mes, 0);

            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                let fecha = new Date(datosCalendario.anio, datosCalendario.mes - 1, dia);
                let diaSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][fecha.getDay()];
                headerRow.push(`${dia} ${diaSemana}`);
            }
            headerRow.push('Cumplimiento %');
            headerRow.push('Sincronizados/Hábiles');
            headerRow.push('Días Descanso');
            data.push(headerRow);

            // Datos de sucursales
            datosCalendario.sucursales.forEach(sucursal => {
                let row = [sucursal.nombre, sucursal.nombre_dia_descanso];

                sucursal.dias.forEach(dia => {
                    if (dia.es_dia_descanso) {
                        row.push('✧');
                    } else if (dia.tiene_sincronizacion) {
                        row.push('✓');
                    } else {
                        row.push('✗');
                    }
                });

                row.push(sucursal.totales.cumplimiento);
                row.push(`${sucursal.totales.dias_sincronizados}/${sucursal.totales.dias_habiles}`);
                row.push(sucursal.totales.dias_descanso);
                data.push(row);
            });

            // Fila de resumen
            let totalSync = datosCalendario.sucursales.reduce((sum, s) => sum + s.totales.dias_sincronizados, 0);
            let totalHabiles = datosCalendario.sucursales.reduce((sum, s) => sum + s.totales.dias_habiles, 0);
            let totalDescanso = datosCalendario.sucursales.reduce((sum, s) => sum + s.totales.dias_descanso, 0);
            let promedio = totalHabiles > 0 ? round((totalSync / totalHabiles) * 100, 1) : 0;

            let resumenRow = ['PROMEDIO GENERAL', ''];
            for (let i = 0; i < ultimoDia.getDate(); i++) {
                resumenRow.push('');
            }
            resumenRow.push(promedio);
            resumenRow.push(`${totalSync}/${totalHabiles}`);
            resumenRow.push(totalDescanso);
            data.push(resumenRow);

            // Crear y descargar Excel
            let ws = XLSX.utils.aoa_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Calendario');
            XLSX.writeFile(wb, `calendario_sincronizacion_${datosCalendario.nombre_mes}_${datosCalendario.anio}.xlsx`);

            mostrarNotificacion('✅ Calendario exportado a Excel', 'success');
        }

        function capturarCalendario() {
            const element = document.querySelector('#calendarioContent');
            if (!element) {
                mostrarNotificacion('⚠️ No hay datos para capturar', 'warning');
                return;
            }

            mostrarNotificacion('📸 Generando captura del calendario...', 'info');

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
                            mostrarNotificacion('✅ Calendario copiado al portapapeles! Puedes pegarlo con Ctrl+V', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, `calendario_${mesActual}_${anioActual}`);
                            mostrarNotificacion('⚠️ Se descargó la imagen', 'warning');
                        });
                    } else {
                        descargarImagen(canvas, `calendario_${mesActual}_${anioActual}`);
                        mostrarNotificacion('⚠️ Se descargó la imagen', 'warning');
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('❌ Error al capturar el calendario', 'error');
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
