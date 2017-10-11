@extends('layouts.app')

@section('body')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header clearfix">
        <h1 class="pull-left">
            @yield('title')
        </h1>
        @if(isset($c2a))
            <a href="{{ $c2a['link'] }}" class="pull-right btn btn-success btn-sm">{{ $c2a['label'] }}</a>
        @endif
    </section>

    <!-- Main content -->
    <section class="content">

        @yield('content')
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
</div>

@endsection