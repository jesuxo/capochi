@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        .linkunderline:hover{
            text-decoration: underline;
        }

        .tdline{
            border:1px solid #75a373 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #75a373  !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">

        <div class=" col-lg-7 ">
            <div class=" row ">
                <div class=" col-12 ">
                    <div class="card card-height-100" style="background-color: #fafafa; border: 1px solid #f95a02;">
                        <div class="card-header d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3   mt-3 mt-xxl-0">
                                <form  method="post" name="form1" id="form1">
                                    @csrf
                                    @method('POST')

                                    <div class="input-group">
                                        <input type="text" class="form-control" data-provider="flatpickr"
                                               data-range-date="true" data-date-format="d/m/Y" id="fechasreport"
                                               data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                                        >
                                        <div class="input-group-text bg-primary border-primary text-white">
                                            <button type="submit" class="botoncal" >Consultar</button>
                                        </div>
                                    </div>

                                    <div class="mt-2" style="display: flex; justify-content: space-between">
                                        @php
                                            list($fecha1,$fecha2) = explode(" to ",$fechasreport);

                                            if($fecha1 != $fecha2){
                                                $fechasreport = "$fecha1 - $fecha2";
                                        @endphp
                                        <p class="text-muted mb-2">{{$fechasreport}}</p>
                                        @php
                                            }else{
                                                $fechasreport = "$fecha1";
                                                list($d,$m,$y)=explode('/',$fecha1);
                                                $fechaanterior = date('d/m/Y',strtotime("$y-$m-$d -1 day"));
                                                $fechaposterior = date('d/m/Y',strtotime("$y-$m-$d +1 day"));

                                        @endphp
                                        <a href="javascript:;" onclick="$('#fechasreport').val('{{$fechaanterior}}'); loadingreport('/resumenVentas') "> << {{$fechaanterior}}</a>

                                        <a href="javascript:;" onclick="$('#fechasreport').val('{{$fechaposterior}}'); loadingreport('/resumenVentas') "> {{$fechaposterior}}  >> </a>
                                        @php
                                            }
                                        @endphp
                                    </div>

                                </form>
                            </div>
                            <a  href="javascript:;" onclick="loadingreport('/reporte/venta')"
                               class="d-flex align-items-center p-1 m-2 linkunderline" style="text-align: center; border: 1px solid #f95a02; border-radius: 5px;">
                                Reporte Ventas
                            </a>
                        </div>
                        <div class="card-body" id="contentReport" data-simplebar  style="height: 214px; max-height: 214px;" >

                            @if(isset($sucursales))
                                <div class="table-responsive table-card ">
                                    <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                        <thead class="text-muted table-light">
                                        <tr>
                                            <th width="30%" class="tdlineff">  Sucursal  </th>
                                            <th  width="15%"class="tdlineff" style="text-align: center !important" align="center">Total</th>
                                            <th  width="15%"class="tdlineff"style="text-align: center !important" align="center">Contado</th>
                                            <th  width="15%"class="tdlineff" style="text-align: center !important" align="center">Cr&eacute;dito</th>
                                            <th  width="10%"class="tdlineff"style="text-align: center !important" align="center">Facts</th>
                                            <th  width="10%"class="tdlineff" style="text-align: center !important" align="center">Devs</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php

                                            $porc     = 0;
                                            $tantomto = 0;
                                            if(isset($sucursales)){
                                                foreach ($sucursales as $sucursal){
                                                    $tantomto += $sucursal['contado']+$sucursal['credito'];
                                                }
                                            }
                                        @endphp
                                        @if(isset($sucursales))
                                            @foreach($sucursales as $index => $sucursal)
                                            @php
                                                $venta = $sucursal['contado']+$sucursal['credito'];
                                                if($tantomto>0)
                                                    $porc = ($venta / $tantomto) *100;

                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                                         data-fksucursal   = "{{$sucursal['id']}}"
                                                         data-fechasreport = "{{$fechasreport}}"
                                                         data-contado      = ""
                                                         data-credito      = ""
                                                        onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal['descrip']}}')"
                                                        data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                                        >
                                                            {{$sucursal['descrip']}}
                                                        </a>
                                                    </div>
                                                </td>
                                                <td align="right">
                                                    $ {{  number_format(($sucursal['contado']+$sucursal['credito']),2,',','.')  }}
                                                </td>

                                                <td align="right">
                                                    $ {{  number_format($sucursal['contado'],2,',','.')  }}
                                                </td>
                                                <td align="right">
                                                    <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                                       data-fksucursal   = "{{$sucursal['id']}}"
                                                       data-fechasreport = "{{$fechasreport}}"
                                                       data-contado      = "1"
                                                       data-credito      = "1"
                                                       onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal['descrip']}}')"
                                                       data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                                    >
                                                        $ {{number_format($sucursal['credito'],2,',','.') }}
                                                    </a>

                                                </td>
                                                <td align="center" class="text-success">
                                                    {{$sucursal['facturas'] }}
                                                </td>
                                                <td align="center" class="text-danger">
                                                    {{$sucursal['devoluciones'] }}
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                                                         aria-valuenow="{{$porc}}" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar bg-success bg-opacity-50 progress-bar-striped progress-bar-animated"
                                                             style="width: {{$porc}}%">

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                        <div class="card-body mt-4"  style="height: 198px">

                            @if(isset($sucursales))
                                <div class="table-responsive table-card  " data-simplebar  style="height: 191px; max-height: 191px;">
                                    <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                        <thead class="text-muted table-light">
                                        <tr>
                                            <th width="30%" class="tdlineff">  Sucursal  </th>
                                            <th  width="20%"class="tdlineff" style="text-align: right !important" align="right">Cobranza ($)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $cobranzas  = 0;
                                            $tcobranzas = 0;
                                        @endphp
                                        @if(isset($sucursales))
                                            @foreach($sucursales as $index => $sucursal)
                                                @if($sucursal['cobranzas'] >0)
                                                    @php
                                                        $cobranzas  += $sucursal['cobranzas'];
                                                        $tcobranzas += $sucursal['tcobranzas'];
                                                    @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                                               data-fksucursal   = "{{$sucursal['id']}}"
                                                               data-fechasreport = "{{$fechasreport}}"
                                                               data-contado      = ""
                                                               data-credito      = ""
                                                               onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal['descrip']}}')"
                                                               data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                                            >
                                                                {{$sucursal['descrip']}}
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td align="right">
                                                        $ {{  number_format($sucursal['cobranzas'],2,',','.')  }}
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-12">

                </div>
            </div>
        </div>
        <div class=" col-lg-5 ">
            <div class="row g-0 text-center">
                <div class="card card-animate" style="border: 1px solid #f95a02;">
                    <div class="card-body row">
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-bottom-0">
                                <h5 class="mb-1">$<span>{{number_format($contado,2,',','.')}}</span></h5>
                                <p class="text-muted mb-0">Contado</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-start-0 border-bottom-0">
                                <h5 class="mb-1">$<span >{{number_format($credito,2,',','.')}}</span>
                                </h5>
                                <p class="text-muted mb-0">Cr&eacute;dito</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-start-0 border-bottom-0">
                                <h5 class="mb-1">$<span >{{number_format($cobranzas,2,',','.')}}</span>
                                </h5>
                                <p class="text-muted mb-0">$ Cobrado</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed">
                                <h5 class="mb-1 text-success"><span>{{$facturas}}</span></h5>
                                <p class="text-muted mb-0">Facturas</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-start-0">
                                <h5 class="mb-1 text-danger "><span >{{$devoluciones}}</span></h5>
                                <p class="text-muted mb-0">Devoluciones</p>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 p-0">
                            <div class="p-1 pt-3 pb-3 border border-dashed border-start-0">
                                <h5 class="mb-1 text-primary "><span >{{$tcobranzas}}</span></h5>
                                <p class="text-muted mb-0">Cobranzas</p>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 p-0">
                            <div class="p-5 pt-3 d-flex justify-content-between pb-3 border border-dashed border-start-0">
                                <p class="text-muted mb-0">Total (Contado+Cr&eacute;dito)</p>
                                <h5 class="mb-1 text-primary "><span >$<span >{{number_format($credito+$contado,2,',','.')}}</h5>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card"  data-simplebar  style="height: 269px; background-color: #fafafa;  border: 1px solid #f95a02;">
                <div class="card-body mt-4"   >

                    @if(isset($clases))
                        <div class="table-responsive table-card  "  >
                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                <thead class="text-muted table-light">
                                <tr>
                                    <th width="30%"   class="tdlineff">  Clasificaci&oacute;n  </th>
                                    <th  width="20%"  class="tdlineff" style="text-align: right !important; cursor:pointer;" align="right" onclick="loadingreport('/reporte/instpagobs')"><i class="bi bi-link"></i> Monto (BS)</th>
                                    <th  width="20%"  class="tdlineff" style="text-align: right !important; cursor:pointer;" align="right" onclick="loadingreport('/reporte/instpagodolares')"><i class="bi bi-link"></i> Monto (USD)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $tbs  = 0;
                                    $usd = 0;
                                @endphp
                                @if(isset($clases))
                                    @foreach($clases as $index => $clase)
                                        @if($index  !='')
                                            @php
                                                $tbs  += ( isset($listado[$index])    and $listado[$index]  >0)? $listado[$index]: 0;
                                                $usd  += ( isset($listadousd[$index]) and $listadousd[$index]  >0)? $listadousd[$index]: 0;
                                            @endphp
                                            <tr>
                                                <td align="left"> {{$index}}</td>
                                                <td align="right">   {{ (isset($listado[$index]))? ' '.number_format($listado[$index],2,',','.'):''  }}  </td>
                                                <td align="right">   {{ (isset($listadousd[$index]))? '$ '.number_format($listadousd[$index],2,',','.'):''  }} </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                <tr>
                                    <td class="tdlineff">   </td>
                                    <td align="right" class="tdlineff">  {{  (isset($tbs) and $tbs >0)? number_format($tbs ,2,',','.'): ''  }}  </td>
                                    <td align="right" class="tdlineff">   {{  (isset($tbs) and $tbs >0)?'$ '.number_format($usd ,2,',','.') : ''  }}  </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="reporteventasucursalmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulorepventasucu">REPORTE DE VENTAS POR SUCURSAL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body" id="contentreporteventasucu">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>

        function loadingreport(action){
            $('#form1').attr('action',action);
            $('#contentReport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $('#form1').submit()
        }

        $('.reporteventasucursalmodal').unbind('click').bind('click',function () {
            var fksucursal   = $(this).attr('data-fksucursal');
            var contado      = $(this).attr('data-contado');
            var credito      = $(this).attr('data-credito');
            var fechasreport = $(this).attr('data-fechasreport');

            $('#contentreporteventasucu').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
            $.ajax({
                type:'post',
                data:{credito: (credito)? credito : '', contado: (contado)? contado : '', fechasreport: (fechasreport)? fechasreport : '',fksucursal: (fksucursal)? fksucursal : '' },
                url:'/reporte/venta/sucu',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                   $('#contentreporteventasucu').html(response);
                }
            });
        });
    </script>

@endsection
