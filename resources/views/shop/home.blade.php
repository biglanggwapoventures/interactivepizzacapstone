@extends('shop.layout')

@section('content')
	@include('shop.cart-message')
	<div class="row">
		@foreach($pizzas->split(4) AS $column)
			<div class="col-sm-3">
				@foreach($column AS $pizza)
				<div class="thumbnail"
					 data-id="{{ $pizza->id }}"
					 data-image="{{ $pizza->photo }}"
					 data-name="{{ $pizza->name }}"
					 data-description="{{ $pizza->description }}"
					 data-ingredients="{{ $pizza->ingredients->toJson() }}"
					 data-sizes="{{ $pizza->sizes->toJson() }}">
					<img src="{{ $pizza->photo }}" alt="{{ $pizza->name }}" class="img-responsive">
					<div class="caption text-center">
						<h3>{{ $pizza->name }}</h3>
						<p>{{ $pizza->description }}</p>
						<p><a href="#" class="btn btn-primary" role="button" data-toggle="modal" data-target="#order-pizza"><i class="glyphicon glyphicon-shopping-cart"></i> Buy</a></p>
						<p></p>
					</div>
				</div>
				@endforeach
			</div>
		@endforeach
	</div>


	<pre class="hidden">@php print_r($pizzas->toArray()) @endphp</pre>


@endsection

@push('modals')
<div class="modal fade" id="order-pizza" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modal title</h4>
			</div>
			{!! Form::open(['url' => route('shop.do.cart-add-pizza'), 'method' => 'POST', 'id' => 'add-cart-form']) !!}
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-4">
							<img src="" alt="" class="img-responsive center-block image thumbnail">
						</div>
						<div class="col-sm-8">
							<p class="description"></p>
							<p class="ingredients"></p>
							<table class="table order-table">
								<thead>
									<tr>
										<th>Size</th>
										<th>Price</th>
										<th>Quantity</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td colspan="3" class="text-right"><strong>GRAND TOTAL:</strong></td>
										<td class="grand-total text-success text-right">-</td>
									</tr>
								</tfoot>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-ok"></i> Add to cart</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endpush

@push('js')
<script type="text/javascript">
	$(document).ready(function () {
		$('#order-pizza').on('show.bs.modal', function (e) {
			var $this = $(this),
				pizza = $(e.relatedTarget).closest('.thumbnail'),
				sizes = pizza.data('sizes');

			console.log(typeof sizes)

			$this.find('.modal-title').text(pizza.data('name'))
			$this.find('p.description').text(pizza.data('description'))
			$this.find('p.ingredients').html(function () {
				return 'Contains: <span class="text-primary">'+Object.values(pizza.data('ingredients')).join(', ')+'</span>'
			})
			$this.find('img.image').attr('src', pizza.data('image'))

			$this.find('.order-table tbody').html(function () {
				var content = '';
				$.each(sizes, function (i, size) {
					content += '<tr data-price="'+size.unit_price+'"><td><input type="hidden" name="order['+i+'][pizza_size_id]" value="'+size.id+'">'+size.size+'</td><td class="text-right">'+size.unit_price+'</td><td><input type="number" class="text-right form-control input-sm quantity" name="order['+i+'][quantity]"></td><td class="line-amount text-right">-</td></tr>';
				});
				return content;
			})

			$this.find('.grand-total').text('-');
		});

		$('#order-pizza').on('change', '.quantity', function () {
			var $this = $(this),
				quantity = parseFloat($this.val()) || 0;

			if(quantity < 0){
				$this.val(0);
				$this.trigger('change');
				return;
			}

			var tr = $this.closest('tr'),
				unitPrice = parseFloat(tr.data('price')) || 0,
				lineAmount = (unitPrice * quantity).toFixed(2);

			tr.find('.line-amount').text(lineAmount);

			getGrandTotal();
		});

		function getGrandTotal() {
			var total = 0;
			$('.line-amount').each(function () {
				total += parseFloat($(this).text()) || 0;
			});
			$('.grand-total').text(total.toFixed(2));
		}

		$('#add-cart-form').submit(function (e) {
			e.preventDefault();
			var $this = $(this);
			$this.find('[type=submit]').attr('disabled', 'disabled');
			$.post($this.attr('action'), $this.serialize())
				.done(function (res) {
					window.location.reload();
				})
				.fail(function () {
					window.alert('An internal server error has occured!')
				})
				.always(function () {
					$this.find('[type=submit]').removeAttr('disabled');
				})
		})
	})
</script>
@endpush

@push('css')
	<style type="text/css">
		.table tbody td{
			vertical-align: middle!important;
		}
	</style>
@endpush
