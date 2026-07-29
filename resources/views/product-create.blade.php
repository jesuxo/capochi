@extends('layouts.master')
@section('title')
    Crear nuevo producto
@endsection
@section('css')
    <!-- extra css -->
    <script>
        function  verUltimoProd(codinst){
            $('#invalidcodprod').html('C&oacute;digo ');

            $.ajax({
                type: 'POST',
                url: '/sainsta/check/lastprod/'+codinst,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{ },
                success: function (data) {
                    lastprod = data.last;
                    if(lastprod)
                        $('#invalidcodprod').html('C&oacute;digo  [ '+lastprod+' &Uacute;ltimo producto creado ] ');

                }
            });

        }

    </script>
@endsection
@section('content')
    <x-breadcrumb title="Crear nuevo producto" pagetitle="Productos" />
    <form id="createproduct-form" autocomplete="off" class="needs-validation" method="post" novalidate action="{{route('productos.store')}}">
        @method('POST')
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
                                <p class="text-muted mb-0">Ingrese los datos del producto.</p>
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
                                <select onchange="$('.error-msg').hide(); $('.datosprod').fadeIn(); verUltimoProd(this.value)" class="form-select" data-choices  required
                                        id="choices-category-input" name="codinst">
                                    <option value=""> Seleccionar </option>
                                    @foreach($instancias as $instancia)
                                        <option style="margin-left: {{($instancia->nivel-1) * 14}}px !important;" value="{{$instancia->codinst}}">{!! $instancia->label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-msg mt-1">Por favor, seleccione una instancia del inventario para clasificar este producto.</div>
                        </div>
                    </div>
                </div>

                <div class="card datosprod" style="display: none">

                    <div class="card-body">
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod"
                                           placeholder="" required value=""
                                           onclick="$('#invalidcodprod').html('C&oacute;digo'); $('#invalidcodprod').removeClass('text-danger');">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"
                                           placeholder="Ej: C&oacute;digo Barra">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="add">
                            <input type="text" class="form-control d-none" id="product-id-input">
                            <input type="text" class="form-control" id="descrip" value=""
                                   placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2" value=""
                                   placeholder="Descripci&oacute;n 2" >
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3" value=""
                                   placeholder="Descripci&oacute;n 3" >
                        </div>
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="marca">Marca</label>
                                    <input type="text" class="form-control" id="marca"  name="marca"
                                           placeholder="Ej: POLAR">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad"
                                           placeholder="Ej: Kg">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="esexento" name="esexento"  value="1">
                                    <label class="form-check-label" for="esexento">Este producto es Exento?</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal"  value="1">
                                    <label class="form-check-label" for="exdecimal">Uso de decimales para este producto?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Enviar</button>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <div>

                            <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false>
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>


                <div class="card" style="display: none">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Publish Schedule</h5>
                    </div>

                    <div class="card-body">
                        <div>
                            <label for="datepicker-publish-input" class="form-label">Publish Date & Time</label>
                            <input type="text" id="datepicker-publish-input" class="form-control"
                                placeholder="Enter publish date" data-provider="flatpickr" data-date-format="d.m.y"
                                data-enable-time>
                        </div>
                    </div>
                </div>

                <div class="card" style="display: none">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Tags</h5>
                    </div>
                    <div class="card-body">
                        <div class="hstack gap-3 align-items-start">
                            <div class="flex-grow-1">
                                <input class="form-control" data-choices data-choices-multiple-remove="true"
                                    placeholder="Enter tags" type="text" value="Cotton">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card datosprod" style="display: none">
                    <div class="card-header">
                        <h5 class="card-title mb-3">Informaci&oacute;n adicional</h5>
                        <label class="form-label" for="preciod">Costo</label>
                        <input type="text" class="form-control" id="preciod" name="preciod"
                               placeholder="Ej: 15" required>

                        <label class="form-label" for="costod">Precio Mayor</label>
                        <input type="text" class="form-control" id="costod" name="costod"
                               placeholder="Ej: 18">

                        <label class="form-label" for="costod2">Precio Cr&eacute;dito</label>
                        <input type="text" class="form-control" id="costod2" name="costod2"
                               placeholder="Ej: 20">

                        <label class="form-label" for="costod3">Precio Detal</label>
                        <input type="text" class="form-control" id="costod3" name="costod3"
                               placeholder="Ej: 22.5" required>
                    </div>

                    <div class="card-body">
                        <p class="text-muted mb-2">Opcional</p>
                        <textarea class="form-control" name="observaciones" placeholder="Ej: solo vender en condiciones especificas" rows="3"></textarea>
                    </div>

                </div>


            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </form>
@endsection
@section('scripts')
    <!-- ckeditor -->

    <script src="{{ URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/ckeditor.js') }}"></script>
    <!-- dropzone js -->
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- create-product -->
    <script src="{{ URL::asset('build/js/backend/create-product.init.js') }}?version={{rand(0,500)}}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
