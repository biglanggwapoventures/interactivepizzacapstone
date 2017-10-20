@extends('partials.content', ['c2a' => ['label' => 'Create new beverage', 'link' => route('beverages.create')]])

@push('css')
    <style type="text/css">
        .table tbody td{
            vertical-align: middle!important;
        }
    </style>
@endpush
@section('title', 'Beverages')

@section('content')
        <div class="box">
            <div class="box-body no-padding">
                <table class="table">
                    <thead>

                        <tr>
                            <th>Description</th>
                            <th>Remaining Quantity</th>
                            <th ></th>
                        </tr>

                    </thead>
                    @forelse($items AS $i)
                        <tr>
                            <td>
                                {{ $i->description }}
                            </td>
                            <td>{{ number_format($i->remaining_quantity, 2) }}</td>
                            <td class="text-right">
                                <a href="{{ route('beverages.edit', ['id' => $i->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Edit</a>
                                <button type="button" data-toggle="modal" data-target="#add-stock" class="btn btn-xs btn-primary" data-ingredient-id="{{ $i->id }}"><i class="fa fa-plus"></i> Change stock</button>
                                {!! Form::open(['url' => route('beverages.destroy', ['id' => $i->id]), 'method' => 'DELETE', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
                                    <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No recorded beverages</td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>
@endsection


@push('modals')
<div class="modal fade" id="add-stock" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add stock to: <span id="ingredient-description"></span></h4>
            </div>
            {!! Form::open(['url' => route('admin.add-stock'), 'method' => 'POST']) !!}
            <div class="modal-body">
                {!! Form::hidden('ingredient_id', null) !!}
                {!! Form::bsText('quantity', 'Enter quantity to add:', null) !!}
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


@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#add-stock').on('show.bs.modal', function (e) {
                var $this = $(this),
                    btn = $(e.relatedTarget);

                $(this).find('[name=quantity]').val('');

                $this.find('#ingredient-description').text(btn.closest('tr').find('.media-body .media-heading').text())
                $this.find('[name=ingredient_id]').val(btn.data('ingredient-id'));
            })

            $('#add-stock form').submit(function (e) {
                e.preventDefault();

                var $this = $(this),
                    payload = {
                        quantity: parseInt($this.find('[name=quantity]').val()),
                        ingredient_id: $this.find('[name=ingredient_id]').val()
                    };

                if(!payload.quantity){
                    alert('Minimum additional stock quantity is 1');
                    return;
                }

                $this.find('[type=submit]').attr('disabled', 'disabled');

                $.post($this.attr('action'), payload)
                    .done(function (res) {
                        window.location.reload();
                    })
                    .fail(function () {
                        window.alert('An internal server error has occurred!');
                    })
                    .always(function () {
                        $this.find('[type=submit]').removeAttr('disabled');
                    })
            });
        });
    </script>
@endpush
