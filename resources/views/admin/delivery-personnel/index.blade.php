@extends('partials.content', ['c2a' => ['label' => 'Create new delivery personnel', 'link' => route('delivery-personnel.create')]])

@section('title', 'Delivery Personnel')

@section('content')

<div class="box">
	<div class="box-body no-padding">
		<table class="table">
			<thead>

				<tr>
					<th>Fullname</th>
					<th>Remarks</th>
					<th></th>
				</tr>

			</thead>
			@forelse($items AS $i)
				<tr>
					<td>{{ $i->fullname }}</td>
					<td>{{ $i->remarks }}</td>
					<td>
						<a href="{{ route('delivery-personnel.edit', ['id' => $i->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Edit</a>
						{!! Form::open(['url' => route('delivery-personnel.destroy', ['id' => $i->id]), 'method' => 'DELETE', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
							<button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
						{!! Form::close() !!}
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="3" class="text-center">No recorded delivery personnels</td>
				</tr>
			@endforelse
		</table>
	</div>
</div>
@endsection
