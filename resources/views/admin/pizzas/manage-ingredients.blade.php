@extends('partials.content')

@section('title', 'Ingredients')

@section('content')

<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header with-border clearfix">
				<h3 class="box-title"> {{ "Update pizza ingredient for {$pizza->name}" }} </h3>
			</div>
			<div class="box-body">
				{!! Form::open(['url' => route('pizza.ingredients.save', ['id' => $pizza->id]), 'method' => 'PATCH']) !!}
					<div class="row">
						<div class="col-sm-5">
							{!! Form::hidden('size', $selectedSize) !!}
							{!! Form::bsStatic('Pizza Description', $pizza->description) !!}
							{!! Form::bsStatic('Pizza Size', $selectedSize) !!}
							{!! Form::bsText('unit_price', 'Unit Price', $size->unit_price) !!}
							<hr>
							{!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
							<a href="{{ route('pizzas.index') }}" class="btn btn-default">Go back</a>
						</div>
						<div class="col-sm-7">
							@php $count = 0; $ingredients = $size->ingredients->pluck('pivot.quantity', 'pivot.ingredient_id'); @endphp
							@forelse(Pizza::categorizedIngredients() AS $category)
								<table class="table table-condensed table-bordered" style="table-layout: fixed">
									<thead>
										<tr class="active"><th class="text-primary">{{ strtoupper($category->description) }}</th><th>Quantity</th><th class="text-right">Cost</th></tr>
									</thead>
									<tbody>
										@forelse($category->ingredients AS $item)
											<tr>
												<td>
													{!! Form::bsCheckbox("item[{$count}][id]", $item->description, $item->id, $ingredients->has($item->id), ['class' => 'item-id']) !!}
												</td>
												<td>
													{!! Form::bsText("item[{$count}][quantity]", null, $ingredients->get($item->id), ['class' => 'form-control input-sm item-quantity']) !!}
												</td>
												<td class="text-right">
													{{ number_format($item->unit_price, 2) }}
												</td>
											</tr>
											@php $count++ @endphp
										@empty
											<tr>
												<td colspan="2" class="text-center text-danger">Thera are no ingredients on {{ $category->description }}. Click <a href="{{ route('ingredients.create') }}">here</a> to add one!</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							@empty
								<div class="text-center text-danger">Thera are no ingredient categories recorded yet. Click <a href="{{ route('ingredient-categories.create') }}">here</a> to add one!</div>
							@endforelse
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection

@push('js')
	<script type="text/javascript">
		$(document).ready(function () {
			$('.item-id').change(function () {
				var $this = $(this),
					row = $this.closest('tr');
				if($this.prop('checked')){
					row.find('.item-quantity').removeAttr('disabled');
				}else{
					row.find('.item-quantity').attr('disabled', 'disabled');
				}
			}).trigger('change');
		})
	</script>
@endpush
