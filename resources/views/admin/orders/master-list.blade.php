@extends('partials.content')

@section('title', 'Manage Orders')

@section('content')

@if($errors->count())
    <div class="alert alert-danger">
        <ul class="list-unstyled">
            <li>{{ implode($errors->all(), '</li><li>') }}</li>
        </ul>
    </div>
@endif
@if($lacking = session('lackingErrors'))
    <div class="alert alert-danger">
        <h4>Ooops!</h4>
        <ul class="list-unstyled">
            <li>{{ implode($lacking, '</li><li>') }}</li>
        </ul>
    </div>
@endif
{!! Form::open(['url' => route('admin.manage-orders'), 'method' => 'GET', 'class' => 'form-inline']) !!}
    {!! Form::bsText('transaction_code', 'Transaction Code') !!}
    {!! Form::bsDate('start_date', 'Start Date') !!}
    {!! Form::bsDate('end_date', 'End Date') !!}
    {!! Form::bsSelect('customer', 'Customer', $customers, null, ['id' => 'customer-select2']) !!}
    {!! Form::bsText('city', 'City') !!}
    {!! Form::bsSelect('type', 'Type', ['' => '** ALL TYPES **', 'DELIVERY' => 'Delivery', 'PICKUP' => 'Pickup']) !!}
    {!! Form::bsSelect('status', 'Status', ['' => '** ALL ORDERS **', 'PENDING' => 'Pending', 'PROCESSING' => 'Processing', 'DELIVERING' => 'Delivering', 'READY_FOR_PICKUP' => 'Ready for pickup']) !!}
  <button type="submit" class="btn btn-default">Filter</button>
{!! Form::close() !!}
<div class="box">
    <div class="box-body no-padding">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Transaction Code</th>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>City</th>
                    <th>Order Type</th>
                    <th class="text-right">Total Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>

            </thead>
            @forelse($items AS $i)
                <tr>
                    <td><a href="{{ route('admin.show.order-details', ['orderId' => $i->id]) }}">{{ $i->transaction_code }}</a></td>
                    <td>{{ date_create($i->created_at)->format('m/d/Y h:i A') }}</td>
                    <td>{{ $i->customer->fullname }}</td>
                    <td>{{ $i->customer->profile->city }}</td>
                    <td>
                        {{ $i->order_type }}
                    </td>
                    <td class="text-right">{{ number_format($i->total_amount, 2) }}</td>
                    <td>{{ $i->order_status }}</td>
                    <td>
                        @if($i->isNotReceived())
                            @if($i->isSetToBe('delivering'))
                                <button type="button" data-order-number="{{ $i->id }}" data-toggle="modal" data-target="#assign-delivery-personnel-modal" class="btn btn-default btn-sm">Set: {{ str_replace('_', ' ', $i->next_status) }}</button>
                            @else
                                {!! Form::open(['url' => route('admin.update-order-status'), 'method' => 'POST', 'onsubmit' => 'javascript:return confirm(\'Are you sure you want to update this order to: "'.$i->next_status.'?"\')']) !!}
                                    {!! Form::hidden('order_status', $i->next_status) !!}
                                    {!! Form::hidden('id', $i->id) !!}
                                    <button type="submit" class="btn btn-default btn-sm">Set: {{ str_replace('_', ' ', $i->next_status) }}</button>
                                {!! Form::close() !!}
                            @endif

                        @endif
                    </td>
                    <td>
                        {!! Form::open(['url' => route('admin.remove.order', ['orderId' => $i->id]), 'method' => 'DELETE', 'style' => 'display:inline-block', 'onsubmit' => 'javascript:return confirm(\'Are you sure? This cannot be undone.\')']) !!}
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                        {!!  Form::close() !!}
                    </td>
                </tr>
            @empty
                <td colspan="6" class="text-center">There are no recoreded orders</td>
            @endforelse
        </table>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="assign-delivery-personnel-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Assign delivery personnel for order # <span id="order-number"></span></h4>
            </div>
            {!! Form::open(['url' => route('admin.update-order-status'), 'method' => 'POST']) !!}
            <div class="modal-body">
                {!! Form::hidden('id', null, ['id' => 'order-id']) !!}
                {!! Form::hidden('order_status', 'DELIVERING') !!}
                {!! Form::bsSelect('delivery_personnel_id', 'Please assign a delivery personnel', $deliveryPersonnels->toArray()) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Continue</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endpush

@push('js')
    <script type="text/javascript" src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#customer-select2').select2();
            $('#assign-delivery-personnel-modal').on('show.bs.modal', function (e) {
                var btn = $(e.relatedTarget),
                    $this = $(this);

                $this.find('#order-number').text(btn.data('order-number'));
                $this.find('#order-id').val(btn.data('order-number'));
                console.log(btn.data('order-number'));
            })
        })
    </script>
@endpush
