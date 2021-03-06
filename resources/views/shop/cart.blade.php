@extends('shop.layout')

@push('css')
	<style>
		.table tbody td{
			vertical-align: middle!important;
		}
	</style>
@endpush

@section('content')

	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-info">
				<h4><i class="fa fa-bullhorn"></i> Heads Up!</h4>
				<p>Orders with less than 5 pizzas will take 30 minutes or less. For orders greater than 5 pizzas, 1 hour minimum waiting time is given.</p>
				<p><button type="button" data-dismiss="alert" aria-label="Close" class="btn btn-info">Ok, got it!</button></p>
			</div>
			@include('shop.cart-message')
			<div class="row">
				<div class="col-sm-8">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								Cart Details
							</h4>
						</div>
						<table class="table table-striped table-bordered">
							<thead>
								<tr >
									<th colspan="2"></th>
									<th>Size</th>
									<th class="text-right">Price</th>
									<th class="text-right">Quantity</th>
									<th class="text-right">Amount</th>
									<th></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td colspan="5" class="text-right text-info">Amount</td>
									<td class="text-right">{{ number_format(MyCart::getGrossAmount(), 2) }}</td>
									<td class="active"></td>
								</tr>
								<tr>
									<td colspan="5" class="text-right text-info">Value Added Tax (12%)</td>
									<td class="text-right">{{ number_format(MyCart::getVatable(), 2) }}</td>
									<td class="active"></td>
								</tr>
								<tr>
									<td colspan="5" class="text-right text-info">Total Payable</td>
									<td class="text-right">
										<strong class="text-success" style="font-size:15px">{{ number_format(MyCart::getTotal(), 2) }}</strong>
									</td>
									<td class="active"></td>
								</tr>
							</tfoot>
							<tbody>
								@foreach($premadePizzas as $premade)
									<tr>
										<td>
											<img class="thumbnail" src="{{ $premade->pizza->photo }}" alt="{{ $premade->pizza->name }}" style="height:50px;width:auto;margin: 0 auto">
										</td>
										<td class="text-primary">{{ $premade->pizza->name }}</td>
										<td>{{ $premade->size }}</td>
										<td class="text-right">{{ number_format($premade->unit_price, 2) }}</td>
										<td>
											{!! Form::number('', $premade->ordered_quantity, ['class' => 'form-control input-sm text-right quantity']) !!}
										</td>
										<!-- <td class="text-right">{{ number_format($premade->ordered_quantity, 2) }}</td> -->
										<td class="text-right">{{ number_format($premade->total_amount, 2) }}</td>
										<td class="text-center">
											<button data-id="{{ $premade->id }}" data-item-type="PREMADE" type="button" class="btn btn-success btn-xs save-quantity"><i class="fa fa-check"></i></button>
											<button data-id="{{ $premade->id }}" data-item-type="PREMADE" type="button" class="btn btn-danger btn-xs remove"><i class="fa fa-times"></i></button>
										</td>
									</tr>
								@endforeach
								@foreach($customPizzas as $index => $custom)
									<tr>
										<td></td>
										<td class="text-primary">
											Custom Pizza {{ $loop->iteration }}
											@if(isset($errors[$index]))
												<ul class="text-danger">
													<li>{!! implode($errors[$index], '</li><li>') !!}</li>
												</ul>
											@endif
										</td>
										<td>{{ $custom['size'] }}</td>
										<td class="text-right">{{ number_format($custom['unit_price'], 2) }}</td>
										<td class="text-right">{{ $custom['ordered_quantity'] }}</td>
										<td class="text-right">{{ number_format($custom['total_amount'], 2) }}</td>
										<td class="text-center">
											<a class="btn btn-success btn-xs" href="{{ route('shop.show.custom-pizza-form', ['id' => $index]) }}"><i class="fa fa-pencil"></i></a>
											<button data-id="{{ $index }}" data-item-type="CUSTOM" type="button" class="btn btn-danger btn-xs remove"><i class="fa fa-times"></i></button>
										</td>
									</tr>
								@endforeach
								@foreach($orderedBeverages AS $beverage)
									<tr>
										<td class="text-center"><i class="fa fa-glass"></i></td>
										<td>{{ $beverage->description }}</td>
										<td>-</td>
										<td class="text-right">{{ number_format($beverage->unit_price, 2) }}</td>
										<td>
											{!! Form::number('', $beverage->ordered_quantity, ['class' => 'check-max form-control input-sm text-right quantity', 'data-max' => $beverage->remaining_quantity]) !!}
										</td>
										<td class="text-right">{{ number_format($beverage->amount, 2) }}</td>
										<td class="text-center">
											<button data-id="{{ $beverage->id }}" data-item-type="BEVERAGE" type="button" class="btn btn-success btn-xs save-quantity"><i class="fa fa-check"></i></button>
											<button data-id="{{ $beverage->id }}" data-item-type="BEVERAGE" type="button" class="btn btn-danger btn-xs remove"><i class="fa fa-times"></i></button>
										</td>
									</tr>
								@endforeach
								@if($premadePizzas->count() || $customPizzas->count())
									<tr>
										<td colspan="7" class="text-right">
											<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#beverage-modal"><i class="fa fa-plus"></i> Add beverages</button>
										</td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-sm-4">
					@if(Auth::check())

						@if($total > 0)
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">Additional Information</h4>
							</div>
							<div class="panel-body">

								{!! Form::open(['id' => 'order-form', 'url' => route('shop.do.confirm-order')]) !!}
									{!! Form::bsSelect('order_type', 'Order Type', ['' => '** SELECT AN ORDER TYPE **', 'PICKUP' => 'Pickup', 'DELIVERY' => 'Delivery']) !!}
									{!! Form::bsText('recipient', 'Recipient', Auth::user()->fullname, ['data-visible' => 'PICKUP']) !!}
									{!! Form::bsTime('pickup_time', 'Pick Time', null, ['data-visible' => 'PICKUP']) !!}

									<hr>
									<div class="row">
										<div class="col-sm-6">
											{!! Form::bsSelect('destination_type', 'Location', ['' => '** SELECT A LOCATION **', 'CITY_PROPER' => 'City Proper', 'OUTSIDE_CITY' => 'Outside City'], null, ['data-visible' => 'DELIVERY']) !!}
										</div>
										<div class="col-sm-6">
											{!! Form::bsText('cash_amount', 'Cash Amount', null, ['data-visible' => 'DELIVERY']) !!}
										</div>
									</div>


									{!! Form::bsText('street', 'Street Number', Auth::user()->profile->street_number, ['data-visible' => 'DELIVERY']) !!}
									{!! Form::bsText('barangay', 'Barangay', Auth::user()->profile->barangay, ['data-visible' => 'DELIVERY']) !!}
									{!! Form::bsText('city', 'City', Auth::user()->profile->city, ['data-visible' => 'DELIVERY']) !!}
									{!! Form::bsText('landmark', 'Landmark', null, ['data-visible' => 'DELIVERY']) !!}

									@if(count($errors))
										<div class="alert alert-danger">We are sorry but you cannot continue with the order. Please review the errors displayed in the cart details</div>
									@else
										<div class="checkbox">
											<label>
												{!! Form::checkbox('agreement', 1, null, ['id' => 'terms-checkbox']) !!}
												I have read and agreed to the <a data-toggle="modal" data-target="#terms">terms and conditions</a> of {{ config('app.name') }}
											</label>
										</div>
										<button type="submit" class="btn btn-default btn-block"><i class="fa fa-check"></i> Confirm Order!</button>
									@endif
								{!! Form::close() !!}
							</div>
						</div>
						@else
							<div class="alert alert-info text-center">
								<i class="fa fa-shopping-cart fa-3x"></i>
								<p class="">
									Your cart is empty
								</p>
							</div>
						@endif

					@else
						<div class="well well-sm text-center">
							You need to have an account to place an order! If you already have one, click <a href="{{ route('shop.show.login') }}">here</a> to sign in or click <a href="{{ route('shop.show.registration') }}">here</a> to create one.
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection

@push('modals')
<div class="modal fade" id="terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{{ config('app.name') }} Terms and Conditions</h4>
			</div>
			<div class="modal-body">
				<ul style="font-size: 15px;">
					<li>
						30 minutes guarantee applies up to the village/subdivision or building lobby,receipionist or basement
					</li>
					<li>
						Gurantee applies to a single receipt delivery transaction/minimum of 2 Pizza.
					</li>
					<li>
						Guaranteed time maybe suspended depending on the weather conditions for the safety of our delivery riders.
					</li>
					<li>
						Plus 20 pesos Standard Delivery Cost! Guarranted Hot!!!.
					</li>
					<li>
						All prices quoted are in Philippine pesos. Price and availabilityinformation is subject to change without notice.
					</li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@endpush


@push('js')
	<script type="text/javascript">
		var errors = @json($errors);
		$(document).ready(function () {
			$('select[name=order_type]').change(function () {
				var $this = $(this),
					orderType = $this.val();

				$('[data-visible="'+orderType+'"]').closest('.form-group').removeClass('hidden');
				$('#order-form [data-visible]:not([data-visible="'+orderType+'"])').closest('.form-group').addClass('hidden');
			}).trigger('change');

			$('#terms-checkbox').change(function () {
				var submitOrderBtn = $('#order-form [type=submit]');
				if($(this).prop('checked')){
					submitOrderBtn.removeAttr('disabled');
					return;
				}
				submitOrderBtn.attr('disabled', 'disabled');
			}).trigger('change');

			$('.remove').click(function () {

				if(!confirm('Are you sure you want to remove this item from cart?')){
					return;
				}

				var $this = $(this),
					itemType = $this.data('item-type'),
					id = $this.data('id');

				$this.attr('disabled', 'disabled');

				$.post("{{ route('shop.do.cart-remove-item') }}", {item_type: itemType, id: id})
					.done(function (res) {
						window.location.reload();
					})
					.fail(function() {
						window.alert('An internal server error has occured!');
					})
					.always(function () {
						$this.removeAttr('disabled');
					})

			});

			$('.save-quantity').click(function () {

				var $this = $(this),
					itemType = $this.data('item-type'),
					id = $this.data('id'),
					input = $this.closest('tr').find('input.quantity')
					quantity = input.val();

				if(input.hasClass('check-max')){
					var maxValue = parseInt(input.data('max')) || 0;
					if(maxValue < parseInt(quantity)){
						alert(input.closest('tr').find('td:eq(1)').text() + ' has '+maxValue + ' remaining!');
						return;
					}
				}

				$this.attr('disabled', 'disabled');

				$.post("{{ route('shop.do.cart-update-quantity') }}", {item_type: itemType, id: id, quantity:  quantity})
					.done(function (res) {
						window.location.reload();
					})
					.fail(function() {
						window.alert('An internal server error has occured!');
					})
					.always(function () {
						$this.removeAttr('disabled');
					})

			});
		})
	</script>
@endpush

@push('modals')

<div class="modal fade" id="beverage-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Beverages</h4>
			</div>
			{!! Form::open(['url' => route('shop.do.cart-update-beverages'), 'method' => 'POST']) !!}
			<div class="modal-body">
				<table class="table">
					<thead>
						<tr>
							<th>Item</th>
							<th class="text-right">Unit Price</th>
							<th class="text-right">Quantity</th>
						</tr>
					</thead>
					<tbody>
						@foreach($beverages AS $beverage)
							@php
								$ordered = $orderedBeverages->where('id', $beverage->id)->first();
							@endphp
							<tr>
								<td>
									{!! Form::hidden("beverages[{$loop->index}][id]", $beverage->id) !!}
									{{ $beverage->description }}
								</td>
								<td class="text-right">{{ number_format($beverage->unit_price, 2) }}</td>
								<td>
									{!! Form::number("beverages[{$loop->index}][quantity]", null, ['class' => 'form-control text-right input-sm', 'min' => 0, 'max' => $beverage->remaining_quantity - ($ordered ? $ordered->ordered_quantity : 0), 'step' => 1]) !!}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endpush
