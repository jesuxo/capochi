@extends('layouts.master')
@section('title')
  OPERACIONES POR SUCURSAL
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
        .choices__inner{
            height: 45px;
        }
        .choices__list--single{
            margin-top: 3px !important;
        }
    </style>
    <div class="row">
        <form  method="post" target="" name="form1" id="form1" action="/operaciones/productos/sucursales">
            <input type="hidden" value="" id="fk_sucursal" name="fk_sucursal">
            <div class="row">

                <div class="col-md-3  ">

                        <select class="form-select" data-choices onchange="submitoper()"
                                id="codoperaciones" name="codoper">
                            <option  {{(!$codoper)?'selected':''}} value="">Todas las Operaciones</option>
                            @foreach($saoper as $oper)
                                <option  {{($oper->codoper == $codoper)?'selected':''}} value="{{$oper->codoper}}">{!! substr($oper->descrip,4,50) !!}</option>
                            @endforeach
                        </select>

                </div>
                <div class="col-md-3  ">

                    <div class="input-group">
                        <input type="text" class="form-control" data-provider="flatpickr"
                               data-range-date="true" data-date-format="d/m/Y"
                               data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                        >
                        <div class="input-group-text bg-primary border-primary text-white">
                            <button type="button" onclick="submitoper()" class="botoncal" >Consultar</button>
                        </div>
                    </div>

                </div>
                @csrf
                @method('POST')
            </div>
        </form>
        <div class="col-md-12 ">
            <div class="card-header mt-3 align-items-center justify-content-center text-center">
               OPERACIONES DE PRODUCTOS POR SUCURSAL
                <br />
                DESDE  {{$fecha1}} HASTA {{$fecha2}}
            </div>
            <div class="table-responsive table-card mt-3">
                <table  border="0" width="100%"  style="border-radius: 5px !important;" class="table table-borderless table-centered align-middle table-nowrap mb-0 mt-3" >

                    @php  $ttt = 0; $tttsuc=[]; $n=0;  @endphp
                    @foreach($itemopei as $index => $item)
                        @php   $vectorsuc = [];  @endphp
                        <tr bgcolor="#f5f5f5">
                            <td align="center" height="30px" class="tdline" colspan="2"> Producto</td>

                            @foreach($sucursales as $indexs  => $vals)
                                <td    align="center" class="tdlineff titulo"  onclick="submitpag('{{$indexs}}')" style="cursor:pointer;"  >
                                    {{$vals}}
                                </td>
                            @endforeach
                            <td    align="center" class="tdline"  > </td>
                            <td    align="center" class="tdline"   >Totales        </td>
                        </tr>

                        @foreach($item as $index2 => $productos)
                            @php  $totalline  = 0;
                            $exdecimal = $productos['exdecimal'];
                            @endphp
                            <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                <td align="left" class="tdline" colspan="2">{{ $productos['descrip'] }}</td>

                                @foreach($sucursales as $indexs => $vals)
                                    @php
                                        $key = $index2.$indexs;
                                        $totalline += (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;

                                        $cantsuc = (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                        if(!isset($vectorsuc[$indexs]))
                                                $vectorsuc[$indexs] = 0;
                                        $vectorsuc[$indexs] += $cantsuc;
                                    @endphp
                                    <td    align="right" class="tdline  " style="font-size:11px"  >
                                        @if(isset($cantsuc) and $cantsuc != 0)
                                            {{ $cantsuc+0}}
                                        @endif
                                    </td>
                                @endforeach
                                <td  align="right" class="tdline" style="font-size:11px" > </td>
                                <td  align="right" class="tdline" style="font-size:11px" >
                                    @if(isset($totalline) and $totalline != 0)
                                    {{($exdecimal)? number_format($totalline,3,',','.'): number_format($totalline,0,',','.')}}
                                    @endif
                                </td>
                            </tr>
                            @php    $n++; @endphp
                        @endforeach
                            <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                <td align="left" class="tdline" colspan="2">Total {{ $index }}</td>
                                @php  $totalline  = 0; @endphp
                                @foreach($sucursales as $indexs => $vals)
                                    <td   align="right" class="tdline titulo"  >
                                        @if(isset($vectorsuc[$indexs]) and $vectorsuc[$indexs] != 0)
                                        {{ number_format($vectorsuc[$indexs],3,',','.') }}
                                            @php
                                                if(!isset($tttsuc[$indexs]))
                                                        $tttsuc[$indexs] =0;
                                                $tttsuc[$indexs] +=$vectorsuc[$indexs];
                                                $totalline+=$vectorsuc[$indexs]; $ttt+=$vectorsuc[$indexs]; @endphp
                                        @endif
                                    </td>
                                @endforeach
                                <td    align="right" class="tdline"   > </td>
                                <td    align="right" class="tdline"  >{{ number_format($totalline ,3,',','.') }} </td>
                            </tr>
                            <tr >
                                <td align="left"  colspan="2"> </td>

                                @foreach($sucursales as $indexs => $vals)
                                    <td    align="center" class="  titulo"  >
                                       &nbsp;
                                    </td>
                                @endforeach
                                <td    align="center" > </td>
                                <td    align="center" > </td>
                            </tr>

                    @endforeach

                    <tr bgcolor="#f5f5f5">
                        <td align="center" height="30px" class="tdline" colspan="2">  </td>

                        @foreach($sucursales as $indexs  => $vals)
                            <td    align="center" class="tdlineff titulo"   >
                                {{$vals}}
                            </td>
                        @endforeach
                        <td    align="center" class="tdline"  > </td>
                        <td    align="center" class="tdline"   >Totales        </td>
                    </tr>
                    <tr >
                        <td align="left"  colspan="2" class="tdline">Totales </td>

                        @foreach($sucursales as $indexs => $vals)
                            <td    align="center" class=" tdline titulo"  >
                                {{  (isset($tttsuc[$indexs]))? $tttsuc[$indexs] + 0 : '' }}
                            </td>
                        @endforeach
                        <td    align="center" class="tdline" > </td>
                        <td    align="center" class="tdline" > {{  $ttt + 0}}</td>
                    </tr>
                </table>
            </div>
            <br>
            <br>
            <br>
        </div>
    </div>

@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>

        function submitoper(){
            $('#form1').prop('target','_self');
            $('#form1').attr('action','/operaciones/productos/sucursales');
            window.document.getElementById('form1').submit()
        }

        function submitpag(v){
            $('#form1').prop('target','_blank');
            $('#form1').attr('action','/operaciones/detallado/sucursal');
            $('#fk_sucursal').val(v);
            window.document.getElementById('form1').submit()
        }

        var productCategoryInput = new Choices('#codoperaciones', {
            searchEnabled: false,
            shouldSort: false,
        });
    </script>
@endsection
