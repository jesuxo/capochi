@extends('layouts.master')
@section('title', 'Calendario de Sincronizaciones')

@section('css')
    <style>
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
        }

        .tabla-calendario th {
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
        }

        .tabla-calendario td {
            text-align: center;
            padding: 6px 4px;
            vertical-align: middle;
            border: 1px solid #e9ecef;
        }

        .celda-dia {
            min-width: 30px;
            max-width: 30px;
            cursor: default;
        }

        .celda-sync {
            display: inline-block;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            line-height: 28px;
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

        .celda-sync.no-habilitado {
            background: #e9ecef;
            color: #adb5bd;
            opacity: 0.5;
        }

        .celda-sync.sincronizado:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 8px rgba(117, 163, 115, 0.4);
        }

        .celda-sync.no-sincronizado:hover {
            transform: scale(1.15);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
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
        }

        .nombre-sucursal {
            font-weight: 600;
            min-width: 150px;
            text-align: left;
            padding-left: 15px !important;
        }

        .total-col {
            background: #f8f9fa;
            font-weight: bold;
            min-width: 80px;
        }

        .dia-semana-header {
            font-size: 11px;
            color: #6c757d;
            display: block;
            margin-top: 2px;
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

        @media (max-width: 768px) {
            .tabla-calendario {
                font-size: 11px;
            }
            .celda-sync {
                width: 22px;
                height: 22px;
                line-height: 22px;
                font-size: 9px;
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
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="calendario-container">
                <!-- Filtros -->
                <div class="filtros-container">
                    <div>
                        <label class="form-label fw-bold mb-1">🏪 Sucursal</label>
                        <select id="sucursalSelect" class="form-select form-select-sm" style="min-width: 200px;">
                            <option value="0">🌐 Todas las sucursales</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">🏪 {{ $sucursal->descrip }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-bold mb-1">📅 Mes</label>
                        <div class="btn-group-mes">
                            <button class="btn btn-sm" onclick="cambiarMes(-1)">‹</button>
                            <button class="btn btn-sm" id="mesActual" style="min-width: 120px; font-weight: bold;">{{ now()->translatedFormat('F Y') }}</button>
                            <button class="btn btn-sm" onclick="cambiarMes(1)">›</button>
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
                        <span>Sincronizado</span>
                    </div>
                    <div class="leyenda-item">
                        <span class="leyenda-circulo" style="background: #dc3545;"></span>
                        <span>No sincronizado</span>
                    </div>
                    <div class="leyenda-item">
                        <span class="leyenda-circulo" style="background: #e9ecef; border: 1px solid #ddd;"></span>
                        <span>Día no habilitado</span>
                    </div>
                    <div class="leyenda-item">
                        <span class="leyenda-circulo" style="background: #75a373; width: 28px; height: 28px; line-height: 28px; text-align: center; font-size: 11px; color: white; font-weight: bold;">✓</span>
                        <span>Día con sincronización</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalle de sincronización -->
    <div class="modal fade" id="modalDetalleDia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-check me-2"></i>
                        <span id="modalDetalleTitulo"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDetalleContenido">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Cargando detalle...</p>
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
        let mesActual = {{ now()->month }};
        let anioActual = {{ now()->year }};
        let datosCalendario = null;
        let sucursalSeleccionada = 0;

        $(document).ready(function() {
            cargarCalendario();

            // Evento cambio de sucursal
            $('#sucursalSelect').on('change', function() {
                sucursalSeleccionada = parseInt($(this).val());
                cargarCalendario();
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
                        <th style="min-width: 150px; text-align: left; padding-left: 15px;">
                            <i class="bi bi-building me-1"></i> Sucursal
                        </th>
    `;

            // Cabecera con días del mes
            let diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            let primerDia = new Date(data.anio, data.mes - 1, 1);
            let ultimoDia = new Date(data.anio, data.mes, 0);

            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                let fecha = new Date(data.anio, data.mes - 1, dia);
                let diaSemana = diasSemana[fecha.getDay()];
                let esFinDeSemana = fecha.getDay() === 0 || fecha.getDay() === 6;
                let claseDia = esFinDeSemana ? 'text-muted' : '';

                html += `
            <th class="celda-dia ${claseDia}" title="${diaSemana} ${dia}">
                ${dia}
                <small class="dia-semana-header">${diaSemana}</small>
            </th>
        `;
            }

            html += `
                    <th class="total-col" style="min-width: 80px;">
                        <i class="bi bi-graph-up me-1"></i> Cumpl.
                    </th>
                    <th class="total-col" style="min-width: 50px;">
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
            let cumplimientoPromedio = 0;

            data.sucursales.forEach((sucursal, index) => {
                let cumplimiento = sucursal.totales.cumplimiento;
                let claseCumplimiento = cumplimiento >= 80 ? 'cumplimiento-alto' : (cumplimiento >= 50 ? 'cumplimiento-medio' : 'cumplimiento-bajo');
                let colorFila = index % 2 === 0 ? '' : 'table-light';

                totalDiasHabilesGlobal += sucursal.totales.dias_habiles;
                totalDiasSyncGlobal += sucursal.totales.dias_sincronizados;

                html += `
            <tr class="fila-sucursal ${colorFila}" data-sucursal-id="${sucursal.id}">
                <td class="nombre-sucursal">
                    <i class="bi bi-building me-1"></i>
                    ${sucursal.nombre}
                    <span class="badge-productos ms-2">${sucursal.totales.dias_habiles} días</span>
                </td>
        `;

                // Días del mes
                sucursal.dias.forEach((dia) => {
                    let claseCelda = '';
                    let contenido = '';
                    let titulo = `${dia.dia_semana} ${dia.dia_numero}`;

                    if (dia.es_dia_habilitado) {
                        if (dia.tiene_sincronizacion) {
                            claseCelda = 'sincronizado';
                            contenido = dia.productos_sincronizados > 0 ? dia.productos_sincronizados : '✓';
                            titulo += ` - Sincronizado (${dia.productos_sincronizados} productos)`;
                        } else {
                            claseCelda = 'no-sincronizado';
                            contenido = '✗';
                            titulo += ' - NO sincronizado';
                        }
                    } else {
                        claseCelda = 'no-habilitado';
                        contenido = '○';
                        titulo += ' - Día no habilitado';
                    }

                    html += `
                <td>
                    <div class="celda-sync ${claseCelda}" title="${titulo}">
                        ${contenido}
                    </div>
                </td>
            `;
                });

                // Totales
                html += `
                    <td class="total-col">
                        <span class="cumplimiento-badge ${claseCumplimiento}">
                            ${cumplimiento}%
                        </span>
                    </td>
                    <td class="total-col">
                        ${sucursal.totales.dias_sincronizados}/${sucursal.totales.dias_habiles}
                    </td>
                </tr>
        `;
            });

            // Fila de resumen general
            cumplimientoPromedio = totalDiasHabilesGlobal > 0 ? round((totalDiasSyncGlobal / totalDiasHabilesGlobal) * 100, 1) : 0;
            let clasePromedio = cumplimientoPromedio >= 80 ? 'cumplimiento-alto' : (cumplimientoPromedio >= 50 ? 'cumplimiento-medio' : 'cumplimiento-bajo');

            html += `
                <tr class="table-primary">
                    <td class="fw-bold">
                        <i class="bi bi-award me-1"></i> PROMEDIO GENERAL
                    </td>
    `;

            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                html += `<td></td>`;
            }

            html += `
                    <td class="total-col">
                        <span class="cumplimiento-badge ${clasePromedio}">
                            ${cumplimientoPromedio}%
                        </span>
                    </td>
                    <td class="total-col">
                        ${totalDiasSyncGlobal}/${totalDiasHabilesGlobal}
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

        function exportarCalendarioExcel() {
            if (!datosCalendario) {
                alert('Primero consulte el calendario');
                return;
            }

            let data = [];

            // Cabecera
            let headerRow = ['Sucursal'];
            let fechas = [];
            let primerDia = new Date(datosCalendario.anio, datosCalendario.mes - 1, 1);
            let ultimoDia = new Date(datosCalendario.anio, datosCalendario.mes, 0);

            for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
                let fecha = new Date(datosCalendario.anio, datosCalendario.mes - 1, dia);
                let diaSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][fecha.getDay()];
                headerRow.push(`${dia} ${diaSemana}`);
                fechas.push(new Date(datosCalendario.anio, datosCalendario.mes - 1, dia));
            }
            headerRow.push('Cumplimiento %');
            headerRow.push('Sincronizados/Hábiles');
            data.push(headerRow);

            // Datos de sucursales
            datosCalendario.sucursales.forEach(sucursal => {
                let row = [sucursal.nombre];

                sucursal.dias.forEach(dia => {
                    if (dia.es_dia_habilitado) {
                        row.push(dia.tiene_sincronizacion ? '✓' : '✗');
                    } else {
                        row.push('○');
                    }
                });

                row.push(sucursal.totales.cumplimiento);
                row.push(`${sucursal.totales.dias_sincronizados}/${sucursal.totales.dias_habiles}`);
                data.push(row);
            });

            // Fila de resumen
            let totalSync = datosCalendario.sucursales.reduce((sum, s) => sum + s.totales.dias_sincronizados, 0);
            let totalHabiles = datosCalendario.sucursales.reduce((sum, s) => sum + s.totales.dias_habiles, 0);
            let promedio = totalHabiles > 0 ? round((totalSync / totalHabiles) * 100, 1) : 0;

            let resumenRow = ['PROMEDIO GENERAL'];
            for (let i = 0; i < fechas.length; i++) {
                resumenRow.push('');
            }
            resumenRow.push(promedio);
            resumenRow.push(`${totalSync}/${totalHabiles}`);
            data.push(resumenRow);

            // Crear y descargar Excel
            let ws = XLSX.utils.aoa_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Calendario');
            XLSX.writeFile(wb, `calendario_sincronizacion_${datosCalendario.nombre_mes}_${datosCalendario.anio}.xlsx`);
        }

        function capturarCalendario() {
            const element = document.querySelector('.calendario-container');
            if (!element) {
                alert('No hay datos para capturar');
                return;
            }

            mostrarNotificacion('Generando captura del calendario...', 'info');

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
                            mostrarNotificacion('✅ Calendario copiado al portapapeles!', 'success');
                        }).catch(() => {
                            descargarImagen(canvas, `calendario_${mesActual}_${anioActual}`);
                        });
                    } else {
                        descargarImagen(canvas, `calendario_${mesActual}_${anioActual}`);
                    }
                }, 'image/png');
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al capturar el calendario');
            });
        }

        function descargarImagen(canvas, nombre) {
            const link = document.createElement('a');
            link.download = `${nombre}_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }

        function mostrarNotificacion(mensaje, tipo = 'success') {
            // Limpiar notificaciones anteriores
            $('.screenshot-notification').remove();

            let bgColor = tipo === 'success' ? '#75a373' : (tipo === 'warning' ? '#ffc107' : '#75a373');
            let icono = tipo === 'success' ? 'check-circle-fill' : (tipo === 'warning' ? 'exclamation-triangle-fill' : 'info-circle-fill');
            let textColor = tipo === 'warning' ? '#000' : '#fff';

            let notification = $(`
        <div class="screenshot-notification alert" style="position: fixed; top: 80px; right: 20px; z-index: 99999; background-color: ${bgColor}; color: ${textColor}; animation: slideInRight 0.3s ease;">
            <i class="bi bi-${icono} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close btn-close-${tipo === 'warning' ? 'black' : 'white'}" data-bs-dismiss="alert"></button>
        </div>
    `);
            $('body').append(notification);
            setTimeout(() => notification.fadeOut(300, () => notification.remove()), 4000);
        }
    </script>
@endsection
