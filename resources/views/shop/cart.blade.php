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
			<div class="page-header">
				<h1><i class="fa fa-shopping-cart"></i>	Cart</h1>
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
									<td class="text-right">{{ number_format($total, 2) }}</td>
									<td class="active"></td>
								</tr>
								<tr>
									<td colspan="5" class="text-right text-info">Value Added Tax (12%)</td>
									<td class="text-right">{{ number_format(floatval($total) * 0.12, 2) }}</td>
									<td class="active"></td>
								</tr>
								<tr>
									<td colspan="5" class="text-right text-info">Total Payable</td>
									<td class="text-right">
										<strong class="text-success" style="font-size:15px">{{ number_format((floatval($total) * 0.12) + floatval($total), 2) }}</strong>
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
											{!! Form::text('', $premade->ordered_quantity, ['class' => 'form-control input-sm text-right quantity']) !!}
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
										<td class="text-primary">Custom Pizza {{ $loop->iteration }}</td>
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
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">Additional Information</h4>
						</div>
						<div class="panel-body">
							{!! Form::open(['id' => 'order-form', 'url' => route('shop.do.confirm-order')]) !!}
								{!! Form::bsSelect('order_type', 'Order Type', ['' => '** SELECT AN ORDER TYPE **', 'PICKUP' => 'Pickup', 'DELIVERY' => 'Delivery']) !!}
								{!! Form::bsText('recipient', 'Recipient', null, ['data-visible' => 'PICKUP']) !!}
								{!! Form::bsText('pickup_time', 'Pick Time', null, ['data-visible' => 'PICKUP']) !!}

								<hr>
								<div class="row">
									<div class="col-sm-6">
										{!! Form::bsText('estimated_delivery_time', 'Pick Time', null, ['data-visible' => 'DELIVERY']) !!}
									</div>
									<div class="col-sm-6">
										{!! Form::bsText('cash_amount', 'Cash Amount', null, ['data-visible' => 'DELIVERY']) !!}
									</div>
								</div>

								{!! Form::bsSelect('destination_type', 'Location', ['' => '** SELECT A LOCATION **', 'CITY_PROPER' => 'City Proper', 'OUTSIDE_CITY' => 'Outside City'], null, ['data-visible' => 'DELIVERY']) !!}
								{!! Form::bsText('street', 'Street Number', null, ['data-visible' => 'DELIVERY']) !!}
								{!! Form::bsText('barangay', 'Barangay', null, ['data-visible' => 'DELIVERY']) !!}
								{!! Form::bsText('city', 'City', null, ['data-visible' => 'DELIVERY']) !!}
								{!! Form::bsText('landmark', 'Landmark', null, ['data-visible' => 'DELIVERY']) !!}
								<div class="checkbox">
									<label>
										{!! Form::checkbox('agreement', 1, null, ['id' => 'terms-checkbox']) !!}
										I have read and agreed to the <a data-toggle="modal" data-target="#terms">terms and conditions</a> of {{ config('app.name') }}
									</label>
								</div>

								<button type="submit" class="btn btn-default btn-block"><i class="fa fa-check"></i> Confirm Order!</button>

								@json($errors->all())
							{!! Form::close() !!}
						</div>
					</div>
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
					<li>
						Mode of payment are as follows:customers with paypal account can pay through paypal otherwise Cash on Delivery(COD).
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
					quantity = $this.closest('tr').find('input.quantity').val();

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
