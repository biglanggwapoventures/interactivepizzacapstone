@extends('partials.content')

@section('title', 'Beverages')

@section('content')

<div class="row">
    <div class="col-sm-6">
        <div class="box">
            <div class="box-header with-border clearfix">
                <h3 class="box-title"> {{ $data->id ? 'Update beverage' : 'Create new beverage'}}</h3>
            </div>
            <div class="box-body">
                @if($data->id)
                {!! Form::model($data, ['url' => route('beverages.update', ['id' => $data->id]), 'method' => 'PATCH', 'files' => true]) !!}
                @else
                {!! Form::open(['url' => route('beverages.store'), 'method' => 'POST', 'files' => true]) !!}
                @endif
                    {!! Form::bsText('description', 'Description') !!}
                    {!! Form::bsNumber('unit_price', 'Unit Price') !!}
                    {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                    <a href="{{ route('beverages.index') }}" class="btn btn-default">Go back</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
