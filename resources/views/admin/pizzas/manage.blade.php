@extends('partials.content')

@section('title', 'Ingredients')

@section('content')

<div class="row">
	<div class="col-sm-6">
		<div class="box">
			<div class="box-header with-border clearfix">
				<h3 class="box-title"> {{ $data->id ? 'Update pizza' : 'Create new pizza'}}</h3>
			</div>
			<div class="box-body">
				@if($data->id)
				{!! Form::model($data, ['url' => route('pizzas.update', ['id' => $data->id]), 'method' => 'PATCH', 'files' => true]) !!}
				@else
				{!! Form::open(['url' => route('pizzas.store'), 'method' => 'POST', 'files' => true]) !!}
				@endif
					{!! Form::bsText('name', 'Pizza Name') !!}
					{!! Form::bsTextarea('description', 'Pizza Description') !!}
					{!! Form::bsFile('photo', 'Photo') !!}
					<hr>
					{!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
					<a href="{{ route('pizzas.index') }}" class="btn btn-default">Go back</a>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection