
<div id="accordion" style="width: 100%; !important; margin-top: 5px;">
    @foreach($sucursales as $sucursal)
    <h3 >{{$sucursal->descrip}}</h3>
    <div class="sucursales contentsucu{{$sucursal->id}}" data-idsucu="{{$sucursal->id}}">
        @if(isset($sucursal->tarjetas))
        <table width="100%" border="0">

                @foreach($sucursal->tarjetas as $index => $tarjeta)
                    <tr @if(($index%2)==0) bgcolor="#f2f2f2" @endif>
                        <td align="left" width="95%" style="font-size: 12px">{{$tarjeta->codtarj}} {{$tarjeta->descrip}}</td>
                        <td align="left" width="5%" style="color: red">
                            <a href="javascript:void(0);"
                               data-idsucu="{{$sucursal->id}}" data-codtarj="{{$tarjeta->codtarj}}"
                               class="btn btn-ghost-primary btn-icon btn-sm quitarubicado">
                                <i class="mdi mdi-close"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach

        </table>
        @endif
    </div>
    @endforeach
</div>
