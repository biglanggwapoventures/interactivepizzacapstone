@extends('shop.layout')

@section('content')
    @if(session('messageFromCart'))
        <div class="alert alert-success"><i class="fa fa-check"></i> {{ session('messageFromCart') }}</div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">View Order History</h4>
                </div>
                <table class="table  table-striped">
                    <thead>

                        <tr>
                            <th>Transaction Code</th>
                            <th>Order Date</th>
                            <th>Order Type</th>
                            <th class="text-right">Total Amount</th>
                            <th class="text-center">Status</th>
                        </tr>

                    </thead>
                    @forelse($items AS $i)
                        <tr>
                            <td><a href="{{ route('customer.show.order-details', ['order' => $i->id]) }}">{{ $i->transaction_code }}</a></td>
                            <td>{{ date_create($i->created_at)->format('m/d/Y h:i A') }}</td>
                            <td>{{ $i->order_type }}</td>
                            <td class="text-right">{{ number_format($i->total_amount, 2) }}</td>
                            <td class="text-center">{{ str_replace('_', '', $i->order_status) }}</td>
                        </tr>
                    @empty
                        <td colspan="6" class="text-center">There are no recoreded orders</td>
                    @endforelse
                </table>
            </div>
        </div>

    </div>
@endsection
