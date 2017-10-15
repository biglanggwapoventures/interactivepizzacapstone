@extends('partials.content')

@section('title', "Profile")

@section('content')

    <div class="row">
        <div class="col-sm-6 ">
            {{ Form::model($user, ['url' => route('admin.update.profile', ['id' => auth::id()]), 'method' => 'PATCH']) }}
                <div class="panel panel-primary">
                    <div class="panel-body"> 
                        <h3>General Information</h3>
                        <hr>
                        @if($profileUpdated = session('profileUpdated'))
                            <div class="alert alert-success">
                                <p>{{ $profileUpdated }}</p>
                            </div>
                        @endif
                        <div class="row"> 
                            <div class="col-sm-12">
                                {{ Form::bsText('firstname', 'Firstname', $user->firstname) }}
                                {{ Form::bsText('lastname', 'Lastname',$user->lastname) }}
                                {{ Form::bsText('email', 'Email', $user->email) }}
                                {{ Form::bsText('barangay', 'House # and Barangay', $user->profile->barangay) }}
                                {{ Form::bsText('street_number', 'Street number', $user->profile->street_number) }}
                                {{ Form::bsText('city', 'City', $user->profile->city) }}
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>

        <div class="col-sm-6">
            {{ Form::model($user, ['url' => route('admin.update.profile', ['id' => auth::id()]), 'method' => 'PATCH']) }}
                <div class="panel panel-primary">
                    <div class="panel-body"> 
                        <h3>Change Password</h3>
                        <hr>
                        @if($passUpdated = session('passUpdated'))
                            <div class="alert alert-success">
                                <p>{{ $passUpdated }}</p>
                            </div>
                        @endif
                        @if($passFail = session('passFail'))
                            <div class="alert alert-danger">
                                <p>{{ $passFail }}</p>
                            </div>
                        @endif
                        <div class="row"> 
                            <div class="col-sm-12">
                                    {{ Form::bsPassword('old_password', 'Old Password') }}
                                    {{ Form::bsPassword('password', 'New Password') }}
                                    {{ Form::bsPassword('password_confirmation', 'Confirm Password') }}
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>

    </div>
@endsection
