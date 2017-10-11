@extends('layouts.app')

@section('body')
<div class="login-box">
    <div class="login-logo">
        <a><b>Interactive</b> Pizza</a>
    </div>
    <div class="login-box-body">
        {!! Form::open(['url' => route('login'), 'method' => 'POST']) !!}
            {!! Form::bsText('email', 'Email Address', null, ['placeholder' => 'Enter your email address']) !!}
            {!! Form::bsPassword('password', 'Password', ['placeholder' => 'Enter your password']) !!}
            <button type="submit" class="btn btn-danger btn-block btn-flat">Sign In</button>
        {!! Form::close() !!}

        <hr>
        <a href="#">I forgot my password</a><br>

    </div>
</div>
@endsection
