
    @foreach($sucursales as $sucursal)

        @if(isset($sucursal->tarjetas))
        <table width="100%" border="0">

                @foreach($sucursal->tarjetas as $index => $tarjeta)
                    <tr @if(($index%2)==0) bgcolor="#f2f2f2" @endif>
                        <td align="left" width="95%" style="font-size: 12px">{{$tarjeta->codtarj}} {{$tarjeta->descrip}}</td>
                        <td align="left" width="5%" >
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

    @endforeach

