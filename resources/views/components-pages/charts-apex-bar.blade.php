@extends('components-layouts.master')
@section('title')
    Apex Bar Charts
@endsection
@section('css')

@endsection
@section('content')
    <!-- page title -->
    <x-breadcrumb title="Bar Charts" pagetitle="Apexcharts" />


    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Grouped Bar Chart</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="grouped_bar" data-colors='["--tb-primary", "--tb-success"]' class="apex-charts" dir="ltr">
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
    </div>
    <!-- end row -->
@endsection

@section('scripts')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- barcharts init -->
    <script src="{{ URL::asset('build/js/pages/apexcharts-bar.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
