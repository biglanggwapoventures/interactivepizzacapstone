@extends('partials.content', ['c2a' => ['label' => 'Create new ingredient category', 'link' => route('ingredient-categories.create')]])

@section('title', 'Ingredient Categories')

@section('content')
@if($message = session('deleteError'))
	<div class="alert alert-danger"><i class="fa fa-warning"></i> {{ $message }}</div>
@endif
<div class="box">
	<div class="box-body no-padding">
		<table class="table">
			<thead>

				<tr>
					<th>Description</th>
					<th></th>
				</tr>

			</thead>
			@forelse($items AS $i)
				<tr>
					<td>{{ $i->description }}</td>
					<td>
						<a href="{{ route('ingredient-categories.edit', ['id' => $i->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Edit</a>
						{!! Form::open(['url' => route('ingredient-categories.destroy', ['id' => $i->id]), 'method' => 'DELETE', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
							<button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
						{!! Form::close() !!}
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="2" class="text-center">No recorded ingredient category</td>
				</tr>
			@endforelse
		</table>
	</div>
</div>
@endsection
