@extends('shop.layout')

@section('content')
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">Login Form</h4>
			</div>
			<div class="panel-body">
				{!! Form::open(['url' => route('shop.do.login'), 'method' => 'POST']) !!}
					@if($errors->count())
						<div class="alert alert-danger">
							Ooops! Please review your details.
						</div>
					@endif
					{!! Form::bsText('email', 'Your email address') !!}
					{!! Form::bsPassword('password', 'Password') !!}
				<hr>
				<button type="submit" class="btn btn-primary">Sign in!</button>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
