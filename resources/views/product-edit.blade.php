@extends('layouts.master')
@section('title')
    Actualizaci&oacute;n de  producto
@endsection
@section('css')

@endsection
@section('content')
    <x-breadcrumb title="Modificacion de producto" pagetitle="Productos" />
    <form id="editproduct-form" autocomplete="off" class="needs-validation" method="post"
          novalidate action="{{route('productos.update',$id)}}">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1 ">Informaci&oacute;n</h5>
                                <p class="text-muted mb-0">Ingrese/Modifique los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="display:none !important;">
                            <label class="form-label">Product description</label>

                            <div id="ckeditor-classic">
                                <p>Tommy Hilfiger men striped pink sweatshirt. Crafted with cotton. Material composition is
                                    100% organic cotton. This is one of the world’s leading designer lifestyle brands and is
                                    internationally recognized for celebrating the essence of classic American cool style,
                                    featuring preppy with a twist designs.</p>
                                <ul>
                                    <li>Full Sleeve</li>
                                    <li>Cotton</li>
                                    <li>All Sizes available</li>
                                    <li>4 Different Color</li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <label class="form-label">Instancia de inventario</label>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/instancias" class="float-end text-decoration-underline">+1 Instancia</a>
                                </div>
                            </div>
                            <div>
                                <select onchange="$('.error-msg').hide();  " class="form-select" data-choices  required
                                        id="choices-category-input" name="codinst">
                                    @foreach($instancias as $instancia)
                                        <option  {{($instancia->codinst == $producto->codinst)?'selected':''}} style="margin-left: {{($instancia->nivel-1) * 14}}px !important;" value="{{$instancia->codinst}}">{!! $instancia->label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-msg mt-1">Por favor, seleccione una instancia del inventario para clasificar este producto.</div>
                        </div>
                    </div>
                </div>

                <div class="card "  >

                    <div class="card-body">
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod" disabled value="{{$producto->codprod}}"
                                           placeholder="" required onclick="$('#invalidcodprod').html('C&oacute;digo'); $('#invalidcodprod').removeClass('text-danger');">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"  value="{{$producto->refere}}"
                                           placeholder="Ej: C&oacute;digo Barra">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="edit">
                            <input type="text" class="form-control d-none" id="product-id-input">

                            <input type="text" class="form-control" id="descrip" value="{{$producto->descrip}}"
                                   placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2"   value="{{$producto->descrip2}}"
                                   placeholder="Descripci&oacute;n 2" >
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3"  value="{{$producto->descrip3}}"
                                   placeholder="Descripci&oacute;n 3" >
                        </div>
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="marca">Marca</label>
                                    <input type="text" class="form-control" id="marca"  value="{{$producto->marca}}" name="marca"
                                           placeholder="Ej: POLAR">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad"value="{{$producto->unidad}}"
                                           placeholder="Ej: Kg">
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="esexento" name="esexento"  {{($producto->esexento)?'checked':''}} value="1">
                                    <label class="form-check-label" for="esexento">Este producto es Exento?</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal" {{($producto->exdecimal)?'checked':''}}    value="1">
                                    <label class="form-check-label" for="exdecimal">Uso de decimales para este producto?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card" style="display: none">

                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-images"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Product Gallery</h5>
                                <p class="text-muted mb-0">Add product gallery images.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="dropzone my-dropzone">
                            <div class="dz-message">
                                <div class="mb-3">
                                    <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                </div>

                                <h5>Drop files here or click to upload.</h5>
                            </div>
                        </div>
                        <div class="error-msg mt-1">Please add a product images.</div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Modificar</button>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <div>

                            <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false name="activo">
                                <option value="1" {{( $producto->activo)?'selected':''}}>Activo</option>
                                <option value="0" {{(!$producto->activo)?'selected':''}}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>


                <div class="card"  >
                        <div class="card-header">
                            <h5 class="card-title mb-3">Informaci&oacute;n adicional</h5>
                            <label class="form-label" for="preciod">Costo</label>
                            <input type="text" class="form-control" id="preciod" value="{{$producto->preciod}}"  name="preciod"
                                   placeholder="Ej: 15" required>

                            <label class="form-label" for="costod">Precio1</label>
                            <input type="text" class="form-control" id="costod" value="{{$producto->costod}}" name="costod"
                                   placeholder="Ej: 18">

                            <label class="form-label" for="costod2">Precio2</label>
                            <input type="text" class="form-control" id="costod2" value="{{$producto->costod2}}"name="costod2"
                                   placeholder="Ej: 20">

                            <label class="form-label" for="costod3">Precio3</label>
                            <input type="text" class="form-control" id="costod3" value="{{$producto->costod3}}"name="costod3"
                                   placeholder="Ej: 22.5" required>
                        </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">Opcional</p>
                        <textarea class="form-control" name="observaciones" placeholder="Ej: solo vender en condiciones especificas" rows="3">{{$producto->observaciones}}</textarea>
                    </div>

                </div>

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/backend/edit-product.init.js') }}"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
