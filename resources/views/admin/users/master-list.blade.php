@extends('partials.content')

@section('title', 'Manage Users')

@section('content')

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="input-group">

                <input type="text" name="search_user" class="form-control" placeholder="Search for a user">

                <span class="input-group-btn">
                    <button type="button" class="btn btn-success">Search</button>
                </span>

            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-body no-padding">
        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <th>Fullname</th>
                    <th>Contact No.</th>
                    <th>Email</th>
                    <th>Street No.</th>
                    <th>Barangay</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users AS $u)
                    <tr>
                        <td>{{ $u->fullname }}</td>
                        <td>{{ $u->profile->contact_number }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->profile->street_number }}</td>
                        <td>{{ $u->profile->barangay }}</td>
                        <td>{{ $u->profile->city }}</td>
                        <td>
                            @if($u->banned_at)
                                {!! Form::open(['url' => route('admin.unban-user', ['id' => $u->id]), 'method' => 'PATCH', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
                                    <button class="btn btn-xs btn-danger"><i class="fa fa-ban"></i> Unban</button>
                                {!! Form::close() !!}   
                            @else
                                {!! Form::open(['url' => route('admin.ban-user', ['id' => $u->id]), 'method' => 'PATCH', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
                                    <button class="btn btn-xs btn-danger"><i class="fa fa-ban"></i> Ban</button>
                                {!! Form::close() !!}
                            @endif

                            {!! Form::open(['url' => route('admin-destroy-user', ['id' => $u->id]), 'method' => 'DELETE', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
                                    <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
                            {!! Form::close() !!}                            
                        </td>
                    </tr>
                @empty
                    <td colspan="6" class="text-center">There are no recoreded orders</td>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection