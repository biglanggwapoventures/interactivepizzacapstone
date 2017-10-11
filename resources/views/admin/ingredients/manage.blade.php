@extends('partials.content')

@section('title', 'Ingredients')

@section('content')

<div class="row">
	<div class="col-sm-5">
		<div class="box">
			<div class="box-header with-border clearfix">
				<h3 class="box-title"> {{ $data->id ? 'Update ingredient' : 'Create new ingredient'}}</h3>
			</div>
			<div class="box-body">
				@if($data->id)
				{!! Form::model($data, ['url' => route('ingredients.update', ['id' => $data->id]), 'method' => 'PATCH', 'files' => true]) !!}
				@else
				{!! Form::open(['url' => route('ingredients.store'), 'method' => 'POST', 'files' => true]) !!}
				@endif
					{!! Pizza::ingredientCategoryDropdown('ingredient_category_id', 'Ingredient Category', $data->ingredient_category_id) !!}
					{!! Form::bsText('description', 'Description') !!}
					{!! Form::bsText('unit_price', 'Unit Price') !!}
					{!! Form::bsFile('photo', 'Photo') !!}
					{!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
					<a href="{{ route('ingredients.index') }}" class="btn btn-default">Go back</a>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
