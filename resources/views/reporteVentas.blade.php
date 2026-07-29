@extends('layouts.master')
@section('title')
    Venta de productos por sucursal
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
    </style>
@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #f95a02 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #f95a02 !important;
        }
    </style>
    @php $n=0;
                                        $tcancele    =0;
                                        $tcancelt    =0;
                                        $tdolares    =0;
                                        $ttransf     =0;
                                        $tpesos      =0;
                                        $tpeso_tranf =0;
                                        $teuros      =0;
                                        $tcredito    =0;
                                        $ttotalventa =0;
    @endphp

    <form  method="post" name="form1" id="form1" action="/reporte/venta">
        <div class="row">
            <div class="col-md-12  ">
                <div class="card-header mt-3 align-items-center justify-content-center text-center">
                    REPORTE DE VENTAS DESDE  {{$fecha1}} HASTA {{$fecha2}}   <br />   <br />
                </div>
            </div>

            <div class="col-md-3  ">

                <select class="form-select" data-choices onchange="$('#form1').submit()"
                        id="idsucu" name="fksucursal">
                    <option  {{($fksucursal=='' or $fksucursal== 0 )?'selected':''}} value="0">Todas las Sucursales</option>
                    @foreach($allsucursales as $sucu)
                        <option  {{($sucu->id == $fksucursal)?'selected':''}} value="{{$sucu->id}}">{!! $sucu->descrip !!}</option>
                    @endforeach
                </select>

            </div>
            <div class="col-md-3 ">
                @php
                    if($fechasreport != ''){
                        list($fecha1,$fecha2) = explode(" to ",$fechasreport);

                        if($fecha1 != $fecha2){
                            $fechasreport = "$fecha1 - $fecha2";
                        }
                    }
                @endphp
                <div class="input-group">
                    <input type="text" class="form-control" data-provider="flatpickr"
                           data-range-date="true" data-date-format="d/m/Y" id="fechasreport"
                           data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                    >
                    <div class="input-group-text bg-primary border-primary text-white">
                        <button type="submit" class="botoncal" >Consultar</button>
                    </div>
                </div>

            </div>

            @csrf
            @method('POST')

            @if($fechasreport != '')

                <div class="col-md-12 "><br />   <br />
                    <div class="table-responsive table-card ">
                        <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                            <tr bgcolor="#fff">
                                <td width="200px" height="30px" align="center" class="tdline" >SUCURSAL</td>
                                <td width="50px" align="center" class="tdlineff" > BS</td>
                                <td width="50px" align="center" class="tdlineff" > BS.T</td>
                                <td width="50px" align="center" class="tdlineff" > USD</td>
                                <td width="50px" align="center" class="tdlineff" > USD.T</td>
                                <td width="50px" align="center" class="tdlineff" > CREDITO </td>
                                <td width="50px" align="center" class="tdlineff" > TOTAL USD</td>
                            </tr>
                            @if(isset($sucursales))

                                @foreach($sucursales as $indexsuc => $sucursal)
                                    @php
                                        $tcancele    += $listado[$indexsuc]['cancele'];
                                        $tcancelt    += $listado[$indexsuc]['cancelt'];
                                        $tdolares    += $listado[$indexsuc]['dolares'];
                                        $ttransf     += $listado[$indexsuc]['transf'];
                                        $tpesos      += $listado[$indexsuc]['pesos'];
                                        $tpeso_tranf += $listado[$indexsuc]['peso_tranf'];
                                        $teuros      += $listado[$indexsuc]['euros'];
                                        $tcredito    += $listado[$indexsuc]['credito'];
                                        $ttotalventa += $listado[$indexsuc]['totalventa'];
                                    @endphp
                                    <tr @php if(($n%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                        <td width="" height="30"align="left" class="tdline" >

                                            <a href="javascript:;"  class="fw-medium fs-14 mb-0 reporteventasucursalmodal"
                                               data-fksucursal   = "{{$indexsuc}}"
                                               data-fechasreport = "{{$fechasreport}}"
                                               data-contado      = ""
                                               data-credito      = ""
                                               onclick="$('#titulorepventasucu').html('REPORTE DE VENTAS DE {{$sucursal}}')"
                                               data-bs-toggle="modal" data-bs-target="#reporteventasucursalmodal"
                                            >
                                                {{$sucursal}}
                                            </a>



                                        </td>
                                        <td width="" align="right" class="tdline" >  {{($listado[$indexsuc]['cancele']!=0)?number_format($listado[$indexsuc]['cancele'],2,',','.') : ''}}</td>
                                        <td width="" align="right" class="tdline" >  {{($listado[$indexsuc]['cancelt']!=0)?number_format($listado[$indexsuc]['cancelt'],2,',','.') : ''}}</td>
                                        <td width="" align="right" class="tdline" >  {{($listado[$indexsuc]['dolares']!=0)?number_format($listado[$indexsuc]['dolares'],2,',','.') : ''}}</td>
                                        <td width="" align="right" class="tdline" >  {{($listado[$indexsuc]['transf'] !=0)?number_format($listado[$indexsuc]['transf'] ,2,',','.') : ''}}</td>
                                        <td width="" align="right" class="tdline" >  {{($listado[$indexsuc]['credito']!=0)?number_format($listado[$indexsuc]['credito'],2,',','.') : ''}}</td>
                                        <td width="" align="right" class="tdline" >  {{($listado[$indexsuc]['totalventa']!=0)?number_format($listado[$indexsuc]['totalventa'],2,',','.') : ''}}</td>
                                    </tr>
                                    @php $n++; @endphp
                                @endforeach
                            @endif
                            <tr >
                                <td width="" height="30"align="left" class=" " > </td>
                                <td width="" align="center" class=" " > &nbsp; </td>
                                <td width="" align="center" class=" " >  </td>
                                <td width="" align="center" class="" >  </td>
                                <td width="" align="center" class="" >  </td>
                                <td width="" align="center" class="" >  </td>
                                <td width="" align="center" class="" >  </td>
                            </tr>
                            <tr bgcolor="#eee">
                                <td width="" height="30"align="left" class="tdline" >TOTALES</td>
                                <td width="" align="right" class="tdline" >{{ ($tcancele!=0)? number_format($tcancele,2,',','.') : ''}}     </td>
                                <td width="" align="right" class="tdline" >{{ ($tcancelt!=0)?number_format($tcancelt,2,',','.'): ''}}        </td>
                                <td width="" align="right" class="tdline" >{{ ($tdolares!=0)?number_format($tdolares,2,',','.'): ''}}       </td>
                                <td width="" align="right" class="tdline" >{{ ($ttransf!=0)?number_format($ttransf,2,',','.'): ''}}         </td>
                                <td width="" align="right" class="tdline" >{{ ($tcredito!=0)?number_format($tcredito,2,',','.'): ''}}       </td>
                                <td width="" align="right" class="tdline" >{{ ($ttotalventa!=0)?number_format($ttotalventa,2,',','.'): ''}} </td>
                            </tr>
                        </table>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>
            @endif
        </div>
    </form>

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

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
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
