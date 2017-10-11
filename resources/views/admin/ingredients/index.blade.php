@extends('partials.content', ['c2a' => ['label' => 'Create new ingredient', 'link' => route('ingredients.create')]])

@push('css')
	<style type="text/css">
		.table tbody td{
			vertical-align: middle!important;
		}
		.table tbody td:first-child img{
			width:70px;height:auto;
			/*margin-right: 10px;*/
		}
		.table{
			/*table-layout: fixed;*/
		}
	</style>
@endpush
@section('title', 'Ingredients')

@section('content')
		<div class="box">
			<div class="box-body no-padding">
				<table class="table">
					<thead>

						<tr>
							<th style="width:70%">Description</th>
							<th ></th>
						</tr>

					</thead>
					@forelse($items AS $i)
						<tr>
							<td>
								<div class="media">
									<div class="media-left">
										<a href="#">
											<img src="{{ $i->photo }}" alt="{{ $i->description }}" class="media-object">
										</a>
									</div>
									<div class="media-body">
										<h4 class="media-heading">{{ $i->description }}</h4>
										<span class="label label-warning"> {{ $i->category->description }}</span>
										<span class="label label-info"> {{ number_format($i->unit_price, 2) }}</span>
									</div>
								</div>
							</td>
							<td class="text-right">
								<a href="{{ route('ingredients.edit', ['id' => $i->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Edit</a>
								{!! Form::open(['url' => route('ingredients.destroy', ['id' => $i->id]), 'method' => 'DELETE', 'onsubmit' => 'javascript:return confirm(\'Are you sure?\')']) !!}
									<button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
								{!! Form::close() !!}
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="4" class="text-center">No recorded ingredients</td>
						</tr>
					@endforelse
				</table>
			</div>
		</div>
@endsection
