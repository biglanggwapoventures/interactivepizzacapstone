@extends('shop.layout')

@section('content')
<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">Create your own pizza from different ingredients!</h4>
			</div>
			<div class="panel-body">
				<div>
					<!-- Nav tabs -->
					<div style="text-align:center">
						<ul class="nav nav-pills" role="tablist" style="margin-bottom:20px;display:inline-block;">
							@foreach($categories->pluck('description') AS $label)
								<li role="presentation" class="{{ $loop->first ? 'active' : '' }}"><a href="#{{ lcfirst($label) }}" aria-controls="{{ lcfirst($label) }}" role="tab" data-toggle="tab" style="font-size:">{{ $label }}</a></li>
							@endforeach
						</ul>
					</div>

					<!-- Tab panes -->
					<div class="tab-content">
						@php $presetItems = $preset ? array_column($preset['item'], 'items', 'category_id') : null @endphp
						@foreach($categories AS $category)
							<div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ lcfirst($category->description) }}">
								@foreach($category->ingredients->chunk(4) AS $group)
									<div class="row" role="tabpanel" id="{{ lcfirst($category->description) }}" style="margin-bottom: 10px;">
										@foreach($group AS $item)
											<div class="col-xs-3 nopad text-center">
												<label class="image-checkbox">
													<img class="img-responsive" src="{{ $item->photo }}" style=""/>
													{!! Form::checkbox(
														'choice[]',
														$item->id,
														in_array($item->id, array_get($presetItems, $category->id, [])),
														[
															'data-category' => $category->id,
															'data-description' => $item->description,
															'data-price' => $item->unit_price,
															'class' => 'choice',
															'data-prices' => json_encode($item->customized_prices),
															'data-quantites' => json_encode($item->customized_quantities)
														]
													) !!}
													<i class="fa fa-check hidden"></i>
													<p class="text-primary">{{ $item->description }}</p>
												</label>
											</div>
										@endforeach
									</div>
								@endforeach
							</div>
						@endforeach
					</div>

				</div>
			</div>
		</div>
		<!-- @print_r($categories->toArray()) -->
	</div>
	<div class="col-sm-4">
		<div class="panel panel-primary">
			<div class="panel-heading ">
				<h4 class="panel-title">
					Pizza Summary
				</h4>
			</div>
			<div class="panel-body choice-summary">
				<ul class="list-unstyled">
					@foreach($categories->pluck('description', 'id') AS $id => $label)
						<li data-id="{{ $id }}" style="margin-bottom:10px;">
							<span>{{ $label }}</span>
							<ol class="ingredient-list">

							</ol>
						</li>
					@endforeach
					<hr>
					<li>
						<div class="row">
							<div class="col-sm-6">
								{!! Form::bsText('quantity', 'How many?', array_get($preset, 'quantity'), ['class' => 'form-control quantity text-right']) !!}
							</div>
							<div class="col-sm-6">
								{!! Form::bsSelect('size', '...and size?', ['SMALL' => 'Small', 'MEDIUM' => 'Medium', 'LARGE' => 'Large'], array_get($preset, 'size')) !!}
							</div>
						</div>
					</li>
					<hr>
					<li>
						<span>Total Amount:</span>
						<strong class="pull-right total-amount" style="font-size:15px">-</strong>
					</li>
				</ul>
				<!-- <hr> -->
				@if(is_null($preset))
					<button type="button" class="btn btn-success btn-block" id="submit-order"><i class="fa fa-check"></i> Add to cart</button>
				@else
					<button type="button" class="btn btn-success btn-block"  id="submit-order"><i class="fa fa-check"></i> Save changes</button>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@push('css')
<style type="text/css">
	/*image gallery*/
	.image-checkbox {
		cursor: pointer;
		box-sizing: border-box;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		border: 4px solid transparent;
		margin-bottom: 0;
		outline: 0;
		border-radius: 4px;
	}
	.image-checkbox input[type="checkbox"] {
		display: none;
	}

	.image-checkbox-checked {
		border-color: #469408;
	}
	.image-checkbox .fa {
		position: absolute;
		color: #f0f0f0;
		background-color: #469408;
		padding: 10px;
		top: 5px;
    	right: 20px;
		border-radius: 50%;
	}
	.image-checkbox-checked .fa {
	  	display: block !important;
	}
</style>
@endpush


@push('js')
<script type="text/javascript">
	// image gallery
	// init the state from the input
	$(".image-checkbox").each(function () {
		if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
			$(this).addClass('image-checkbox-checked');
		}else {
			$(this).removeClass('image-checkbox-checked');
		}
	});

	// sync the state to the input
	$(".image-checkbox").on("click", function (e) {

		$(this).toggleClass('image-checkbox-checked');
		var $checkbox = $(this).find('input[type="checkbox"]');
		$checkbox.prop("checked",!$checkbox.prop("checked")).trigger('change');

		e.preventDefault();
	});

	var payload = [],
		totalAmount = 0;

	// console.log(typeof totalAmount)

	$('.choice').change(function () {

		var $this = $(this),
			category = $this.data('category'),
			value = $this.val(),
			selectedSize = $('#size').val(),
			extras = {itemDescription:$this.data('description'), itemPrice: parseFloat($this.data('prices')[selectedSize])};
			// console.log($this.prop('checked'))
			// console.log('category_id = %s, item_id = %s', category, value);
		$this.prop('checked')
			? addToPayload(value, category, extras)
			: removeFromPayload(value, category, extras);

		showTotal();

	});

	$(document).ready(function () {
		$('.choice:checked').trigger('change');
		$('#size').change(function () {
			payload = [];
			totalAmount = 0;
			$('.ingredient-list').empty();
			$('.choice:checked').trigger('change');
		});
	})

	$('#submit-order').click(submitCustomPizza)
	$('.quantity').change(showTotal)

	function showTotal() {
		var quantity = parseInt($('.quantity').val()) || 0;
		$('.total-amount').text((totalAmount * quantity).toFixed(2))
	}

	function addToPayload(itemId, categoryId, extra) {
		// consl.e
		console.log(totalAmount)
		totalAmount += extra.itemPrice;
		console.log(totalAmount)

		$('.choice-summary [data-id='+categoryId+'] ol').append('<li data-item-id="'+itemId+'">'+extra.itemDescription+'<span class="pull-right text-right">'+extra.itemPrice.toFixed(2)+'</span></li>');

		for (var i = 0; i < payload.length; i++) {
			if (payload[i].category_id == categoryId) {
				payload[i].items.push(itemId);
				return;
			}
		}

		payload.push({
			category_id: categoryId,
			items: [itemId]
		})

	}

	function removeFromPayload(itemId, categoryId, extra) {
		totalAmount -= extra.itemPrice;

		$('.choice-summary [data-id='+categoryId+'] ol li[data-item-id='+itemId+']').remove();

		for (var i = 0; i < payload.length; i++) {
			if (payload[i].category_id == categoryId) {
				var idx = payload[i].items.indexOf(itemId);
				 payload[i].items.splice(idx, 1);
				 return;
			}
		}
	}

	function submitCustomPizza() {

		var categories =  @json($categories->pluck('description', 'id')),
			quantity = parseInt($('#quantity').val()) || 0,
			size = $('select[name=size]').val();

		$(payload).each(function (i, v) {
			if(typeof categories[v.category_id] !== 'undefined' && v.items.length > 0){
				delete categories[v.category_id];
			}
		});

		var missing = Object.values(categories);
		if(missing.length){
			alert('Please choose items for '+missing.join(', '));
			return;
		}
		if(!quantity){
			alert('Please input quantity');
			return;
		}
		if(!size.trim().length){
			alert('Please choose a size');
			return;
		}

		var body = {
			item: payload,
			quantity:quantity,
			size:size
		};

		@if(is_null($preset))
			var url = "{{ route('shop.do.cart-add-custom-pizza') }}";
		@else
			var url = "{{ route('shop.do.cart-update-custom-pizza') }}";
			body.id = "{{ request()->id }}"
		@endif

		var request = $.post(url , body)
			.done(function(res) {
				window.location.href = "{{ route('shop.show.cart') }}";
			})
			.fail(function () {
				window.alert('An internal server error has occured!');
			})
			.always(function () {

			});
	}
</script>
@endpush
