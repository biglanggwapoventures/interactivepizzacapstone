@extends('partials.content', ['c2a' => ['label' => 'Create new pizza', 'link' => route('pizzas.create')]])

@section('title', 'Pizzas')

@section('content')

@push('css')
<style type="text/css">
	.table tbody td{
	vertical-align:middle!important;
}
</style>
@endpush

<div class="box">
	<div class="box-body no-padding">
		<table class="table">
			<thead>

				<tr>
					<th>Photo</th>
					<th>Name</th>
					<th style="width:15%">Description</th>
					<th>Ingredients</th>
					<th>STOCK</th>
					<th></th>
				</tr>

			</thead>
			@forelse($items AS $i)
				<tr>
					<td><img src="{{ $i->photo }}" alt="{{ $i->name }}" style="height:50px;width:auto;"></td>
					<td>{{ $i->name }}</td>
					<td>{{ $i->description }}</td>
					<td>
						<a class="btn btn-xs btn-primary" href="{{ route('pizza.ingredients.show', ['pizza' => $i->id, 'size' => 'SMALL']) }}">S</a>
						<a class="btn btn-xs btn-primary" href="{{ route('pizza.ingredients.show', ['pizza' => $i->id, 'size' => 'MEDIUM']) }}">M</a>
						<a class="btn btn-xs btn-primary" href="{{ route('pizza.ingredients.show', ['pizza' => $i->id, 'size' => 'LARGE']) }}">L</a>
					</td>
					<td>
						<ul class="list-unstyled">
							@foreach($i->sizes AS $size)
								<li>{{ "{$size->size}: {$stocks->get($size->id)}" }}</li>
							@endforeach
						</ul>
					</td>
					<td>
						<a href="{{ route('pizzas.edit', ['id' => $i->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Edit</a>
						{!! Form::open(['url' => route('pizzas.destroy', ['id' => $i->id]), 'method' => 'DELETE', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
							<button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
						{!! Form::close() !!}
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="4" class="text-center">No recorded pizzas</td>
				</tr>
			@endforelse
		</table>
	</div>
</div>
@endsection
