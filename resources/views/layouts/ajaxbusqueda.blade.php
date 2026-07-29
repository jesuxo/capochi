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
    @php
        $pros = 0;
    @endphp
    @foreach($productos as $producto)
        @php
            $pros ++;
        @endphp
        <tr>
            <td>
                <!--<a href="product-overview" class="fw-medium link-primary">#00541</a>-->
                <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{$producto->codprod}}</a>
            </td>
            <td>
                <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{substr($producto->descrip,0,40)}}</a>
                <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary" style="float: right"><i class="bi-pencil-square"></i></a>
            </td>
            <td align="right"> {{number_format($producto->preciod+$producto->preciod2,2,',','.')}}  </td>
            <td align="right"> {{number_format($producto->costod3,2,',','.')}}</td>
            <td>
                {{date("d/m/Y h:i a",strtotime($producto->updated_at))}}
            </td>
        </tr><!-- end tr -->
    @endforeach
    @if($pros == 0)
        <tr>
            <td colspan="5">
                No se encontraron resultados
            </td>
        </tr>
    @endif
    </tbody>
</table>
