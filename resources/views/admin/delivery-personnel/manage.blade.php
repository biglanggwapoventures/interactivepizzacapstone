@extends('partials.content')

@section('title', 'Delivery Personnel')

@section('content')

<div class="row">
	<div class="col-sm-5">
		<div class="box">
			<div class="box-header with-border clearfix">
				<h3 class="box-title"> {{ $data->id ? 'Update delivery personnel' : 'Create new delivery personnel'}}</h3>
			</div>
			<div class="box-body">
				@if($data->id)
				{!! Form::model($data, ['url' => route('delivery-personnel.update', ['id' => $data->id]), 'method' => 'PATCH']) !!}
				@else
				{!! Form::open(['url' => route('delivery-personnel.store'), 'method' => 'POST']) !!}
				@endif
					{!! Form::bsText('firstname', 'First Name') !!}
					{!! Form::bsText('lastname', 'Last Name') !!}
					{!! Form::bsText('mobile_number', 'Mobile Number', null, ['placeholder' => 'EG: 09233887588']) !!}
					{!! Form::bsTextarea('remarks', 'Remarks') !!}
					{!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
					<a href="{{ route('delivery-personnel.index') }}" class="btn btn-default">Go back</a>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
