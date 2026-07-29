@extends('layouts.master')
@section('title')
   Instancias
@endsection
@section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/gridjs/mermaid.min.css') }}">
    <style>
        .choices__inner, .choices__list--dropdown .choices__item {
            font-size: 12px !important;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Instancias" pagetitle="Productos" />

    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0" id="addCategoryLabel">Crear Instancia</h6>
                </div>
                <div class="card-body">
                    <form autocomplete="off" class="needs-validation createCategory-form" id="createCategory-form" novalidate>
                        <input type="hidden" id="categoryid-input" class="form-control" value="">
                        <div class="row">
                            <div class="col-xxl-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="SubcategoryTitle" class="form-label">Descripci&oacute;n <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="SubcategoryTitle"
                                        placeholder="..." required>
                                    <div class="invalid-feedback">Ingrese la descripci&oacute;n de la instancia</div>
                                </div>
                            </div>
                            <div class="col-xxl-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="categorySelect" class="form-label">Instancia Padre  </label>
                                    <select class="form-control" name="categorySelect" id="categorySelect">
                                        <option value="0">Seleccione</option>
                                        @if(isset($instanciaspadre))
                                            @foreach($instanciaspadre as $item)
                                                    <option value="{{$item->descrip}}">{!!  $item->label !!}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit" id="addNewCategory" class="btn btn-success">Ingresar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xxl-9">
            <div class="row justify-content-between mb-4">
                <div class="col-xxl-6 col-lg-6">
                    <div class="search-box mb-3 mb-lg-0">
                        <input type="text" class="form-control" id="searchResultList" autocomplete="off"
                            placeholder="Buscar instancia de inventario...">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>

                <!--end col-->
            </div>
            <!--end row-->

            <div class="card">
                <div class="card-body">
                    <div id="product-sub-categories" class="table-card"></div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <!-- removeItemModal -->
    <div id="removeItemModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" id="close-removecategoryModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-md-5">
                    <div class="text-center">
                        <div class="text-danger">
                            <i class="bi bi-trash display-4"></i>
                        </div>
                        <div class="mt-4 fs-15">
                            <h4 class="mb-1">Esta seguro?</h4>
                            <p class="text-muted mx-3 fs-16 mb-0">Desea eliminar esta instancia?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light btn-hover"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn w-sm btn-danger btn-hover" id="remove-category">Borrar</button>
                    </div>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('scripts')
    <!-- gridjs js -->
    <script src="{{ URL::asset('build/libs/gridjs/gridjs.umd.js') }}"></script>

    <!-- product-sub-categories js -->
    <script src="{{ URL::asset('build/js/backend/product-sub-categories.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
