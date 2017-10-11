@extends('shop.layout')

@section('content')
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Registration Form</h4>
			</div>
			<div class="panel-body">
				{!! Form::open(['url' => route('shop.do.registration'), 'method' => 'POST']) !!}
					@if($errors->count())
						<div class="alert alert-danger">
							Ooops! Please review your details.
						</div>
					@endif
					<div class="row">
						<div class="col-sm-6">
							<div class="row">
								<div class="col-sm-6">
									{!! Form::bsText('firstname', 'First name') !!}
								</div>
								<div class="col-sm-6">
									{!! Form::bsText('lastname', 'Last name') !!}
								</div>
							</div>
							{!! Form::bsText('email', 'Your email address') !!}
							{!! Form::bsText('contact_number', 'Your contact number') !!}
							{!! Form::bsPassword('password', 'Desired password') !!}
							{!! Form::bsPassword('password_confirmation', 'Confirm your password') !!}
						</div>
						<div class="col-sm-6">
							<div class="well well-sm">
								{!! Form::bsText('barangay', 'House # and Barangay') !!}
								<div class="row">
									<div class="col-sm-6">
										{!! Form::bsText('street_number', 'Street number') !!}
									</div>
									<div class="col-sm-6">
										{!! Form::bsText('city', 'City') !!}
									</div>
								</div>
							</div>
						</div>
					</div>

				<hr>
				<button type="submit" class="btn btn-primary">Register now!</button>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
