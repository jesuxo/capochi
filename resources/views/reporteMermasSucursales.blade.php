@extends('layouts.master')
@section('title')
    Venta de productos por sucursal
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
    <div class="row">
        <div class="col-md-12 ">
            <form  method="post" name="form1" id="form1" action="/mermas/sucursales">

                <div class="row">
                    <div class="col-md-3  ">

                        <div class="input-group">
                            <input type="text" class="form-control" data-provider="flatpickr"
                                   data-range-date="true" data-date-format="d/m/Y"
                                   data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                            >
                            <div class="input-group-text bg-primary border-primary text-white">
                                <button type="submit" class="botoncal" >Consultar</button>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-3  ">

                        <div class="input-group">
                            <input type="text" class="form-control" data-provider="flatpickr"
                                   data-range-date="true" data-date-format="d/m/Y"
                                   data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                            >
                            <div class="input-group-text bg-primary border-primary text-white">
                                <button type="submit" class="botoncal" >Consultar</button>
                            </div>
                        </div>

                    </div>
                </div>
                @csrf
                @method('POST')
            </form>
        </div>
        <div class="col-md-12 ">

            <div class="table-responsive table-card ">
                <table  border="0" class="table table-borderless table-striped align-middle table-sm fs-14 mb-0 mt-3">

                @php  $ttt = 0; $tttsuc=[]; $n=0; $totallineinst = [];  @endphp
                @foreach($itemventas as $index => $item)
                    @php   $vectorsuc = [];  @endphp


                    <tr bgcolor="#f5f5f5">
                        <td align="center" height="100px" class="tdline" colspan="2"> Instancia </td>

                        @foreach($sucursales as $indexs  => $vals)
                            <td width="50px"   align="center" class="tdlineff titulo"   >
                                {{$vals}}
                            </td>
                        @endforeach
                        <td width="50px"   align="center" class="tdline"  > </td>
                        <td width="50px"   align="center" class="tdline"   >Totales        </td>
                    </tr>


                        <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                            <td align="left" class="tdline" colspan="2">VENTAS DE {{ $index }}  </td>
                            @php  $totalline  = 0; @endphp
                            @foreach($sucursales as $indexs => $vals)
                                @php
                                    $key = $item.$indexs;
                                    $totalline += (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                    $cantsuc = (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                    if(!isset($vectorsuc[$indexs]))
                                        $vectorsuc[$indexs] = 0;
                                    $vectorsuc[$indexs] += $cantsuc;
                                @endphp
                                <td width="50px"   align="right" class="tdline  " style="font-size:11px"  >
                                    @if(isset($cantsuc) and $cantsuc>0)
                                        {{ number_format($cantsuc,3,',','.') }}
                                    @endif
                                </td>
                            @endforeach
                            <td width="50px"   align="right" class="tdline"   > </td>
                            <td width="50px"   align="right" class="tdline"  >{{ number_format($totalline ,3,',','.') }}
                                @php
                                if(!isset($totallineinst[$item]))
                                        $totallineinst[$item] = 0;
                                $totallineinst[$item] +=$totalline;
                                @endphp

                            </td>
                        </tr>
                        <tr >
                            <td align="left"  colspan="2" class="tdline">Merma </td>

                            @foreach($sucursales as $indexs => $vals)
                                <td width="50px"   align="center" class="tdline   "  >
                                    &nbsp;
                                </td>
                            @endforeach
                            <td width="50px"   align="center" class="tdline" > </td>
                            <td width="50px"   align="center"  class="tdline"> </td>
                        </tr>
                    <tr >
                        <td align="left"  colspan="2" class="tdline">% </td>

                        @foreach($sucursales as $indexs => $vals)
                            <td width="50px"   align="center" class="tdline   "  >
                                &nbsp;
                            </td>
                        @endforeach
                        <td width="50px"   align="center" class="tdline" > </td>
                        <td width="50px"   align="center"  class="tdline"> </td>
                    </tr>

                        <tr >
                            <td align="left"  colspan="2"> </td>

                            @foreach($sucursales as $indexs => $vals)
                                <td width="50px"   align="center" class="  titulo"  >
                                   &nbsp;
                                </td>
                            @endforeach
                            <td width="50px"   align="center" > </td>
                            <td width="50px"   align="center" > </td>
                        </tr>

                @endforeach


            </table>

            <table  border="0" width="300px" class="mt-3" >

                @foreach($itemventas as $index => $item)

                    <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                        <td align="left" class="tdline"  >Total ventas de  {{ $index }}  </td>
                        <td width="50px"   align="right" class="tdline"  >{{ number_format($totallineinst[$item] ,3,',','.') }}   </td>
                        <td width="50px" rowspan="2"   align="right" class="tdline"  style="background-color: white" >   % </td>
                    </tr>
                    <tr >
                        <td align="left"    class="tdline">Merma </td>
                        <td width="50px"   align="center"  class="tdline"> </td>
                    </tr>
                    <tr >
                        <td align="left"    class=" ">  </td>
                        <td width="50px"   align="center"  class=" "> &nbsp; </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

@endsection
@section('scripts')


    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
