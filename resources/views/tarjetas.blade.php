@extends('layouts.master')
@section('title')
    Panel de Instrumentos de Pago
@endsection
@section('css')
    <style>
        .alltarjetas{
            cursor: all-scroll !important;
        }
        .sucursales{
            min-height: 250px !important;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Lista de Instrumentos de Pago" pagetitle="Listado" />

    <div class="row" id="tarjetasList">
        <div class="col-4">
            <div class="card">
                <div class="card-body" >
                    <div class="tab_container" style="width:100%"  id="content-acordion">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-body" id="content-validos"></div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

        $(document).ready(function()
        {
            actfunctions();
            actualizarvalidos();
            actualizaracordion();
        });

        function actfunctions(){

            $('.quitarubicado').unbind('click').bind('click',function () {
                var idsucu   = $(this).attr('data-idsucu');
                var codtarj  = $(this).attr('data-codtarj');
                //console.log(idsucu + ' '+ codtarj);

                $.ajax({
                    type:'post',
                    data:{idsucu: idsucu, codtarj:codtarj},
                    url:'/tarjetas/quitarubicado',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(response) {
                        actualizarContentAcordion(idsucu);
                    }
                });
            });

            $( ".alltarjetas" ).draggable
            ({
                containment: 'document',
                opacity: 0.6,
                revert: 'invalid',
                helper: 'clone',
                zIndex: 100
            });

            $(".sucursales").droppable({
                drop:function(e, ui)
                {
                    var idsucu  = $(this).attr('data-idsucu');
                    var codtarj = $(ui.draggable).attr('codtarj');

                    $.ajax({
                        type:'post',
                        data:{idsucu: idsucu, codtarj:codtarj},
                        url:'/tarjetas/noubicado',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(response) {
                            $(".tarjeta"+codtarj).fadeOut();

                            actualizarContentAcordion(idsucu);
                        }
                    });

                }
            });
        }
        function actualizarvalidos()
        {
            $.ajax({
                type:'post',
                url:'/tarjetas/noubicado',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    document.getElementById("content-validos").innerHTML=response;
                    actfunctions();
                }
            });

        }

        function actualizaracordion()
        {
            $.ajax({
                type:'post',
                url:'/tarjetas/ubicados',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {
                    document.getElementById("content-acordion").innerHTML=response;
                    actfunctions();
                    $("#accordion").accordion();
                }
            });

        }

        function actualizarContentAcordion(idsucu)
        {
            $.ajax({
                type:'post',
                url:'/tarjetas/content/ubicado',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{idsucu: idsucu},
                success:function(response) {
                    $(".contentsucu"+idsucu).html(response);
                    actfunctions();
                }
            });

        }
    </script>

@endsection
