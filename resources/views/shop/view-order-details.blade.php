@extends('shop.layout')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">Transaction # {{ $order->transaction_code }} </h4>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right text-success">Net Amount</td>
                            <td class="text-right">{{ number_format($order->totalAmountWithoutVAT(), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right text-success">VAT</td>
                            <td class="text-right">{{ number_format($order->getVAT(), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right text-success">Payable Amount</td>
                            <td class="text-right"><strong>{{ number_format($order->getTotalAmount(), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($order->premadePizzaOrderDetails AS $premade)
                        <tr>
                            <td>{{ "{$premade->pizzaSize->pizza->name} *{$premade->pizzaSize->size}" }}</td>
                            <td class="text-right">{{ number_format($premade->pizzaSize->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($premade->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($premade->pizzaSize->unit_price * $premade->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                        @foreach($order->customPizzaOrder AS $custom)
                        <tr>
                            <td >{{ "Custom Pizza *{$custom->size}" }} <a data-toggle="modal" data-target="#custom-pizza-modal-{{ $custom->id }}" class="text-info fa fa-info-circle"></a> </td>
                            @php $unitPrice = $custom->usedIngredients->sum('ingredients.unit_price') @endphp
                            <td class="text-right">{{ number_format($unitPrice, 2) }}</td>
                            <td class="text-right">{{ number_format($custom->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($unitPrice * $custom->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Order Type</dt>
                        <dd>{{ $order->order_type }}</dd>
                        <dt>Order Date</dt>
                        <dd>{{ date_create($order->order_date)->format('F d, Y') }}</dd>
                        <dt>Customer Name</dt>
                        <dd>{{ $order->customer->fullname }}</dd>
                        <dt>Contact Number</dt>
                        <dd>{{ $order->customer->profile->contact_number }}</dd>
                        <dt>Transaction #</dt>
                        <dd><mark>{{ $order->transaction_code }}</mark></dd>
                        <dt style="margin-bottom:10px;">Status</dt>
                        <dd >{{ $order->order_status }}</dd>
                        @if($order->is('delivery'))
                            <dt style="border-top:1px dashed #eee;padding-top:10px;">Destination Type</dt>
                            <dd style="border-top:1px dashed #eee;padding-top:10px;">{{ str_replace('_', ' ', $order->delivery->destination_type) }}</dd>
                            <dt>Landmark</dt>
                            <dd>{{ $order->delivery->landmark }}</dd>
                            <dt>Address</dt>
                            <dd>{{ "{$order->delivery->street}, {$order->delivery->barangay}, {$order->delivery->city}" }}</dd>
                            <dt>Cash Amount</dt>
                            <dd>{{ number_format($order->delivery->cash_amount, 2) }}</dd>
                            <dt>Change</dt>
                            <dd>{{ number_format($order->delivery->cash_amount - $order->total_amount, 2) }}</dd>
                            <dt>Delivery Personnel</dt>
                            <dd>{{ $order->deliveryPersonnel ? $order->deliveryPersonnel->fullname : 'N/A' }}</dd>
                        @elseif($order->is('pickup'))
                            <dt style="border-top:1px dashed #eee;padding-top:10px;">Recipient</dt>
                            <dd style="border-top:1px dashed #eee;padding-top:10px;">{{ $order->pickup->recipient }}</dd>
                            <dt>Pickup Time</dt>
                            <dd>{{ date_create_from_format('H:i:s', $order->pickup->estimated_pickup_time)->format('h:i A') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            <a  href="{{ route('customer.show.order-history') }}"><i class="fa fa-arrow-left"></i> Go back to history</a>
        </div>
    </div>
@endsection

@push('modals')
    @foreach($order->customPizzaOrder AS $custom)
    <div class="modal fade" id="custom-pizza-modal-{{ $custom->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">{{ "Custom Pizza *{$custom->size}" }}</h4>
          </div>
          <div class="modal-body">
                <ul>
                    <li>{!! $custom->usedIngredients->implode('ingredients.description', '</li><li>') !!}</li>
                </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    @endforeach
@endpush
