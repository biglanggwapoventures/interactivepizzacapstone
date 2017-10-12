@extends('partials.content')

@section('title', 'Ingredients')

@section('content')

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
			<div class="row">
				<div class="col-sm-6">
					{!! Pizza::ingredientCategoryDropdown('ingredient_category_id', 'Ingredient Category', $data->ingredient_category_id) !!}
					{!! Form::bsText('description', 'Description') !!}
					{!! Form::bsFile('photo', 'Photo') !!}
				</div>
				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">Custom Pizza Configurations</h4>
						</div>
						<table class="table">
							<thead>
								<tr>
									<th>Size</th>
									<th>Unit Price</th>
									<th>Quantity</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>SMALL</td>
									<td>{!! Form::bsNumber('custom_unit_price_small', null, null, ['class' => 'form-control text-right']) !!}</td>
									<td>{!! Form::bsNumber('custom_quantity_needed_small', null, null, ['class' => 'form-control text-right', 'min' => 1, 'step' => 1]) !!}</td>
								</tr>

								<tr>
									<td>MEDIUM</td>
									<td>{!! Form::bsNumber('custom_unit_price_medium', null, null, ['class' => 'form-control text-right']) !!}</td>
									<td>{!! Form::bsNumber('custom_quantity_needed_medium', null, null, ['class' => 'form-control text-right', 'min' => 1, 'step' => 1]) !!}</td>
								</tr>
								<tr>
									<td>LARGE</td>
									<td>{!! Form::bsNumber('custom_unit_price_large', null, null, ['class' => 'form-control text-right']) !!}</td>
									<td>{!! Form::bsNumber('custom_quantity_needed_large', null, null, ['class' => 'form-control text-right', 'min' => 1, 'step' => 1]) !!}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			{!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
			<a href="{{ route('ingredients.index') }}" class="btn btn-default">Go back</a>
		{!! Form::close() !!}
	</div>
</div>
@endsection
