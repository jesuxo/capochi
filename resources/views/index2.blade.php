@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class=" col-lg-3  ">
            <div class="row  ">
                <div class="col-12">
                    <!-- card -->
                    <a class="card card-animate" href="javascript:;" onclick="$('#form1').attr('action','/'); $('#form1').submit()">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-secondary opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Total ventas</p>
                                    <h4 class="fs-22 fw-semibold mb-3">$<span >{{number_format($contado+$credito,2,',','.')}}</span></h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-3">
                                        <i class="ph-wallet"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-12">
                    <!-- card -->
                    <a class="card card-animate" href="javascript:;" onclick="$('#form1').attr('action','/unidades/0'); $('#form1').submit()">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-info opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Unidades vendidas</p>
                                    <h4 class="fs-22 fw-semibold mb-3"><span>{{($unidadesvendidas)?number_format($unidadesvendidas,2,',','.'):0}}</span> </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                        <i class="ph-storefront"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-12">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <a href="/existencias" class="d-flex justify-content-between">
                                <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Costo Inventario </p>
                                    <h4 class="fs-22 fw-semibold mb-3">$<span >{{number_format($costoinven[0]->suma,2,',','.')}}</span> </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="ph-sketch-logo"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <a href="/cxc" class="d-flex justify-content-between">
                                <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Cuentas x cobrar </p>
                                    <h4 class="fs-22 fw-semibold mb-3"><span >{{(  $cxc[0]->saldo > 0)? '$'.number_format($cxc[0]->saldo,2,',','.') : ''}}</span> </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="ph-currency-dollar-bold"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class=" col-lg-9   ">
            <div class="card card-height-100">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-3   mt-3 mt-xxl-0">
                        <form  method="post" name="form1" id="form1">
                            @csrf
                            @method('POST')

                            <div class="input-group">
                                <input type="hidden" name="verinstancia" id="verinstancia" value="{{isset($instancia)?$instancia:'0'}}"/>
                                <input type="hidden" name="fk_sucursal" id="fk_sucursal" value="{{isset($fk_sucursal)?$fk_sucursal:''}}"/>
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
                                <a href="javascript:;" onclick="$('#fechasreport').val('{{$fechaanterior}}'); $('#form1').submit()"> << {{$fechaanterior}}</a>

                                <a href="javascript:;" onclick="$('#fechasreport').val('{{$fechaposterior}}'); $('#form1').submit()"> {{$fechaposterior}}  >> </a>
                                @php
                                    }
                                @endphp
                            </div>

                        </form>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0" style="display: none">
                            <div class="dropdown card-header-dropdown">
                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <span class="text-muted">Reportes<i class="mdi mdi-chevron-down ms-1"></i></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Ver detallado</a>
                                    <!--<a class="dropdown-item" href="#">Export</a>
                                    <a class="dropdown-item" href="#">Import</a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" data-simplebar  style="max-height: 436px;" >
                    @if(isset($sucursales))
                        <div class="table-responsive table-card ">
                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                <thead class="text-muted table-light">
                                <tr>
                                    @if(!$verunidades)
                                        <th width="50%" scope="col"> Reporte en Dolares por Sucursal  </th>
                                        <th  width="15%"scope="col" style="text-align: center !important" align="center">Contado</th>
                                        <th  width="15%"scope="col" style="text-align: center !important" align="center">Credito</th>
                                        <th  width="10%"scope="col" style="text-align: center !important" align="center">Facturas</th>
                                        <th  width="10%"scope="col" style="text-align: center !important" align="center">Devoluciones</th>
                                    @else
                                        <th width="80%" scope="col"> Reporte en Unidades  por Sucursal</th>
                                        <th  width="20%"scope="col" style="text-align: center !important" align="center">Unidades</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @php

                                    $porc     = 0;
                                    $tantomto = 0;
                                    if(isset($tantomto)){
                                        foreach ($sucursales as $sucursal){
                                            if(!$verunidades){
                                                $tantomto += $sucursal['contado']+$sucursal['credito'];
                                            }else{
                                                 $tantomto += $sucursal['unidades'];
                                            }
                                        }
                                    }
                                @endphp

                                @foreach($sucursales as $index => $sucursal)
                                    @php
                                        if(!$verunidades){

                                            $venta = $sucursal['contado']+$sucursal['credito'];
                                            if($tantomto>0)
                                                $porc = ($venta / $tantomto) *100;

                                        }else{

                                            $venta = $sucursal['unidades'];
                                            if($tantomto>0)
                                                $porc = ($venta / $tantomto) *100;

                                        }

                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="javascript:;" onclick="$('#fk_sucursal').val('{{($fk_sucursal == $sucursal['id'])? 0: $sucursal['id']}}');
                                                    $('#form1').submit()" class="fw-medium fs-14 mb-0">
                                                    {{$sucursal['descrip']}}
                                                </a>
                                                @if($fk_sucursal == $sucursal['id'])
                                                    <i class="ph-chart-line-up-thin text-primary" style="font-size: 25px" ></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td align="right">
                                            {{(!$verunidades)?"$":''}}
                                            {{(!$verunidades)?number_format($sucursal['contado'],2,',','.') : $venta }}
                                        </td>
                                        @if(!$verunidades)
                                            <td align="right">
                                                $ {{number_format($sucursal['credito'],2,',','.') }}
                                            </td>
                                            <td align="center" class="text-success">
                                                {{$sucursal['facturas'] }}
                                            </td>
                                            <td align="center" class="text-danger">
                                                {{$sucursal['devoluciones'] }}
                                            </td>
                                        @endif
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
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xxl-9 ">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Facturas/Devoluciones</h4>

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-xxl-8">
                            <div id="line_chart_datalabel" data-colors='["--tb-secondary", "--tb-danger", "--tb-success"]'
                                 class="apex-charts" dir="ltr">

                            </div>
                        </div>
                        <div class="col-xxl-4">

                            <div class="row g-0 text-center">
                                <div class="col-6 col-sm-6">
                                    <div class="p-3 border border-dashed border-bottom-0">
                                        <h5 class="mb-1">$<span>{{number_format($contadosuc,2,',','.')}}</span></h5>
                                        <p class="text-muted mb-0">Contado</p>
                                    </div>
                                </div>

                                <div class="col-6 col-sm-6">
                                    <div class="p-3 border border-dashed border-start-0 border-bottom-0">
                                        <h5 class="mb-1">$<span >{{number_format($creditosuc,2,',','.')}}</span>
                                        </h5>
                                        <p class="text-muted mb-0">Cr&eacute;dito</p>
                                    </div>
                                </div>

                                <div class="col-6 col-sm-6">
                                    <div class="p-3 border border-dashed">
                                        <h5 class="mb-1 text-success"><span>{{$facturas}}</span></h5>
                                        <p class="text-muted mb-0">Facturas</p>
                                    </div>
                                </div>

                                <div class="col-6 col-sm-6">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1 text-danger "><span >{{$devoluciones}}</span></h5>
                                        <p class="text-muted mb-0">Devoluciones</p>
                                    </div>
                                </div>
                                @if(isset($costoinvensuc[0]) and $costoinvensuc[0]->suma >0)
                                    <div class="col-12 col-sm-12">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1 text-primary "><span >{{number_format($costoinvensuc[0]->suma ,2,',','.')}}</span></h5>
                                            <p class="text-muted mb-0">Inventario Sucursal</p>
                                        </div>
                                    </div>
                                @endif


                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card overflow-hidden">
                <div class="position-absolute opacity-50 start-0 end-0 top-0 bottom-0"
                     style="background-image: url('build/images/sidebar/body-light-1.png');"></div>
                <div class="card-body d-flex justify-content-between align-items-center z-1">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            <i class="ph-storefront display-6"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="card-title fw-medium fs-17 mb-1"> Necesitas  crear o actualizar algun producto?</h5>
                            <p class="mb-0">visita el tablero de productos <a href="/productos">en este enlace</a> </p>
                        </div>
                    </div>
                    <div>
                        <a href="productos/create" class="btn btn-success btn-label btn-hover rounded-pill"><i
                                class="bi bi-box-seam label-icon align-middle rounded-pill fs-16 me-2"></i> O crea un producto nuevo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Top Productos</h4>
                </div>

                <div class="card-body" data-simplebar style="max-height: 445px;">
                    @php
                        $porc      = 0;
                        $tantosprd = 0;
                        if(isset($topprod)){
                            foreach ($topprod as $top){
                                $tantosprd += $top->salidas;
                            }
                        }
                    @endphp
                    @if(isset($topprod))
                        @foreach($topprod as $top)
                            @php
                                if($tantosprd>0)
                                 $porc = ($top->salidas / $tantosprd) *100;
                            @endphp

                            <div class="mb-4">
                                <span class="badge badge-soft-dark float-end">{{number_format($top->salidas,2,',','')}}</span>
                                <h6 class="mb-2"> {{(isset($top->producto) and isset($top->producto->descrip))? $top->producto->descrip: $top}}</h6>
                                <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                                     aria-valuenow="{{$porc}}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-success bg-opacity-50 progress-bar-striped progress-bar-animated"
                                         style="width: {{$porc}}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <!--
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">90%</span>
                        <h6 class="mb-2">Fashion & Clothing</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">64%</span>
                        <h6 class="mb-2">Lighting</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="64" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-warning bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 64%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">77%</span>
                        <h6 class="mb-2">Footwear</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="77" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-danger bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 77%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">53%</span>
                        <h6 class="mb-2">Electronics</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="53" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-info bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 53%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">81%</span>
                        <h6 class="mb-2">Beauty & Personal Care</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="81" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-primary bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 81%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">96%</span>
                        <h6 class="mb-2">Books</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="96" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-secondary bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 96%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="badge badge-soft-dark float-end">69%</span>
                        <h6 class="mb-2">Furniture</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="69" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 69%"></div>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-soft-dark float-end">63%</span>
                        <h6 class="mb-2">Mobile Accessories</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="Success example"
                            aria-valuenow="63" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-dark bg-opacity-50 progress-bar-striped progress-bar-animated"
                                style="width: 63%"></div>
                        </div>
                    </div>
                    -->
                </div>
            </div>
        </div>

    </div>

    <div class="row widget-responsive-fullscreen">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Productos recien actualizados</h4>
                    <div class="flex-shrink-0">
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#searchModal"  onclick="focusbusqueda(); $('#search-options').change(); $('#search-dropdown').fadeIn() " class="btn btn-soft-info btn-sm">
                            <i class="ri-file-list-3-line align-middle"></i> Ver todos
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                            <tr>
                                <th width="5%" scope="col">Codigo</th>
                                <th  width="70%"scope="col">Producto</th>

                                <th  width="10%"scope="col">Costo</th>
                                <th  width="10%"scope="col">Precio</th>

                                <th  width="5%"scope="col">Fecha Actualizado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($prdupdated) and count($prdupdated) > 0)
                                @foreach($prdupdated as $producto)
                                    <tr>
                                        <td>
                                            <!--<a href="product-overview" class="fw-medium link-primary">#00541</a>-->
                                            <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{$producto->codprod}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{$producto->descrip}}</a>
                                        </td>
                                        <td align="right"> {{number_format($producto->preciod+$producto->preciod2,2,',','.')}}  </td>
                                        <td align="right"> {{number_format($producto->costod3,2,',','.')}}</td>
                                        <td>
                                            {{date("d/m/Y h:i a",strtotime($producto->updated_at))}}
                                        </td>
                                    </tr><!-- end tr -->
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    <script src="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/world-merc.js') }}"></script>

    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>

    <!--Swiper slider js-->
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        //  Line chart datalabel
        var linechartDatalabelColors = getChartColorsArray("line_chart_datalabel");
        if (linechartDatalabelColors) {
            var options = {
                chart: {
                    height: 405,
                    zoom: {
                        enabled: true
                    },
                    toolbar: {
                        show: false
                    }
                },
                colors: linechartDatalabelColors,
                markers: {
                    size: 0,
                    colors: "#ffffff",
                    strokeColors: linechartDatalabelColors,
                    strokeWidth: 1,
                    strokeOpacity: 0.9,
                    fillOpacity: 1,
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    width: [2, 2, 2],
                    curve: 'smooth'
                },
                series: [{
                    name: "Ventas",
                    type: 'line',
                    data: [@foreach($intervalos as $index => $dato)
                        {{ number_format($dato,2,'.','').','}}
                        @endforeach ]
                }/*,
                    {
                        name: "Refunds",
                        type: 'area',
                        data: [100, 154, 302, 411, 300, 284, 273, 232, 187, 174, 152, 122]
                    },
                    {
                        name: "Earnings",
                        type: 'line',
                        data: [260, 360, 320, 345, 436, 527, 641, 715, 832, 794, 865, 933]
                    }*/
                ],
                fill: {
                    type: ['solid', 'gradient', 'solid'],
                    gradient: {
                        shadeIntensity: 1,
                        type: "vertical",
                        inverseColors: false,
                        opacityFrom: 0.3,
                        opacityTo: 0.0,
                        stops: [20, 80, 100, 100]
                    },
                },
                grid: {
                    row: {
                        colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
                        opacity: 0.2
                    },
                    borderColor: '#f1f1f1'
                },
                xaxis: {
                    categories: [
                        @foreach($intervalos as $index => $intervalo)
                            "{{$index}}",
                        @endforeach
                    ],
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    floating: true,
                    offsetY: -25,
                    offsetX: -5
                },
                responsive: [{
                    breakpoint: 600,
                    options: {
                        chart: {
                            toolbar: {
                                show: false
                            }
                        },
                        legend: {
                            show: false
                        },
                    }
                }]
            }

            var chart = new ApexCharts(
                document.querySelector("#line_chart_datalabel"),
                options
            );
            chart.render();
        }

        // world map with line & markers
        var vectorMapWorldLineColors = getChartColorsArray("world-map-line-markers");
        if (vectorMapWorldLineColors)
            var worldlinemap = new jsVectorMap({
                map: "world_merc",
                selector: "#world-map-line-markers",
                zoomOnScroll: false,
                zoomButtons: false,
                markers: [{
                    name: "Greenland",
                    coords: [71.7069, 42.6043],
                    style: {
                        image: "build/images/flags/gl.svg",
                    }
                },
                    {
                        name: "Canada",
                        coords: [56.1304, -106.3468],
                        style: {
                            image: "build/images/flags/ca.svg",
                        }
                    },
                    {
                        name: "Brazil",
                        coords: [-14.2350, -51.9253],
                        style: {
                            image: "build/images/flags/br.svg",
                        }
                    },
                    {
                        name: "Serbia",
                        coords: [44.0165, 21.0059],
                        style: {
                            image: "build/images/flags/rs.svg",
                        }
                    },
                    {
                        name: "Russia",
                        coords: [61, 105],
                        style: {
                            image: "build/images/flags/ru.svg",
                        }
                    },
                    {
                        name: "US",
                        coords: [37.0902, -95.7129],
                        style: {
                            image: "build/images/flags/us.svg",
                        }
                    },
                    {
                        name: "Australia",
                        coords: [25.2744, 133.7751],
                        style: {
                            image: "build/images/flags/au.svg",
                        }
                    },
                ],
                lines: [{
                    from: "Canada",
                    to: "Serbia",
                },
                    {
                        from: "Russia",
                        to: "Serbia"
                    },
                    {
                        from: "Greenland",
                        to: "Serbia"
                    },
                    {
                        from: "Brazil",
                        to: "Serbia"
                    },
                    {
                        from: "US",
                        to: "Serbia"
                    },
                    {
                        from: "Australia",
                        to: "Serbia"
                    },
                ],
                regionStyle: {
                    initial: {
                        stroke: "#9599ad",
                        strokeWidth: 0.25,
                        fill: vectorMapWorldLineColors,
                        fillOpacity: 1,
                    },
                },
                labels: {
                    markers: {
                        render(marker, index) {
                            return marker.name || marker.labelName || 'Not available'
                        }
                    }
                },
                lineStyle: {
                    animation: true,
                    strokeDasharray: "6 3 6",
                },
            });

        // Multi-Radial Bar
        var chartRadialbarMultipleColors = getChartColorsArray("multiple_radialbar");
        if (chartRadialbarMultipleColors) {
            var options = {
                series: [85, 69, 45, 78],
                chart: {
                    height: 300,
                    type: 'radialBar',
                },
                sparkline: {
                    enabled: true
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: true,
                                label: 'Sales',
                                formatter: function(w) {
                                    return 2922
                                }
                            }
                        }
                    }
                },
                labels: ['Fashion', 'Electronics', 'Groceries', 'Others'],
                colors: chartRadialbarMultipleColors,
                legend: {
                    show: false,
                    fontSize: '16px',
                    position: 'bottom',
                    labels: {
                        useSeriesColors: true,
                    },
                    markers: {
                        size: 0
                    },
                },
            };

            var chart = new ApexCharts(document.querySelector("#multiple_radialbar"), options);
            chart.render();
        }

        //  Spline Area Charts
        var areachartSplineColors = getChartColorsArray("area_chart_spline");
        if (areachartSplineColors) {
            var options = {
                series: [{
                    name: 'This Month',
                    data: [49, 54, 48, 54, 67, 88, 96]
                }, {
                    name: 'Last Month',
                    data: [57, 66, 74, 63, 55, 70, 85]
                }],
                chart: {
                    height: 250,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                fill: {
                    type: ['gradient', 'gradient'],
                    gradient: {
                        shadeIntensity: 1,
                        type: "vertical",
                        inverseColors: false,
                        opacityFrom: 0.3,
                        opacityTo: 0.0,
                        stops: [50, 70, 100, 100]
                    },
                },
                markers: {
                    size: 4,
                    colors: "#ffffff",
                    strokeColors: areachartSplineColors,
                    strokeWidth: 1,
                    strokeOpacity: 0.9,
                    fillOpacity: 1,
                    hover: {
                        size: 6,
                    }
                },
                grid: {
                    show: false,
                    padding: {
                        top: -35,
                        right: 0,
                        bottom: 0,
                        left: -6,
                    },
                },
                legend: {
                    show: false,
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [2, 2],
                    curve: 'smooth'
                },
                colors: areachartSplineColors,
                xaxis: {
                    labels: {
                        show: false,
                    }
                },
                yaxis: {
                    labels: {
                        show: false,
                    }
                },
            };

            var chart = new ApexCharts(document.querySelector("#area_chart_spline"), options);
            chart.render();
        }
    </script>
@endsection
