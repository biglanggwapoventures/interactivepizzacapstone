@extends('partials.content')

@section('title', 'Ingredient Categories')

@section('content')

<div class="row">
	<div class="col-sm-5">
		<div class="box">
			<div class="box-header with-border clearfix">
				<h3 class="box-title"> {{ $data->id ? 'Update ingredient category' : 'Create new ingredient category'}}</h3>
			</div>
			<div class="box-body">
				@if($data->id)
				{!! Form::model($data, ['url' => route('ingredient-categories.update', ['id' => $data->id]), 'method' => 'PATCH']) !!}
				@else
				{!! Form::open(['url' => route('ingredient-categories.store'), 'method' => 'POST']) !!}
				@endif
					{!! Form::bsText('description', 'Description') !!}
					<div class="row">
						<div class="col-sm-6">
							{!! Form::bsText('custom_pizza_sequence', 'Custom Pizza Sequence') !!}
						</div>
					</div>
					{!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
					<a href="{{ route('ingredient-categories.index') }}" class="btn btn-default">Go back</a>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
