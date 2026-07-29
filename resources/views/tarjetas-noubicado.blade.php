<div style="width:100%; height: 800px; overflow: auto">
    @foreach($tarjetas as $tarjeta)
        <div class="tarjeta{{$tarjeta->codtarj}} alltarjetas m-2  btn btn-light  "
             style="height:60px; width:160px; font-size: 13px" data-codtarj="{{$tarjeta->codtarj}}" codtarj="{{$tarjeta->codtarj}}">
            {{$tarjeta->codtarj}} {{ $tarjeta->descrip }}
        </div>
    @endforeach
</div>
