@extends('layout.main')

@section('title', Lang::get('base.admin-userlist'))

@section('content')

{{ Form::open(['method' => 'get', 'class' => 'form-inline', 'role' => 'form']) }}
	<div class="form-group">
		{{ Form::label('search', Lang::get('base.search'), ['class' => 'sr-only']) }}
		{{ Form::text('search', Input::get('search'), ['class' => 'form-control input-sm', 'placeholder' => Lang::get('base.search')]) }}
	</div>
	<div class="form-group">
		{{ Form::select('usertype', $userTypes, Input::get('usertype'), ['class' => 'form-control input-sm']) }}
	</div>
	{{ Form::submit(Lang::get('base.search'), ['class' => 'btn btn-default btn-sm']) }}

	<span class="pull-right">
		<a href="{{ $backUrl }}" class="btn btn-default btn-sm">@glyph('backward') @lang('base.back')</a>
		<a href="{{ $newUrl }}" class="btn btn-default btn-sm">@glyph('plus') @lang('base.new')</a> 
	</span>
{{ Form::close() }}

<hr>

{{ Form::open(['class' => 'form-inline', 'role' => 'form']) }}

<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th></th>
			<th>#</th>
			<th>@lang('base.name-field')</th>
			<th>@lang('base.email-field')</th>
			<th>@lang('base.phone-field')</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	@foreach ($users as $user)
		<tr <?php if ($user->isLoggedIn()) echo 'class="success"' ?>>
			<td>
				<input type="checkbox" name="bulk[{{ $user->id }}]">
			</td>
			<td>{{ $user->id }}</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->email }}</td>
			<td>{{ $user->phone }}</td>
			<td class="text-right">
				<a href="{{ URL::action($editAction, [$user->id]) }}" class="btn btn-xs btn-default">@glyph('pencil') @lang('base.edit')</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
</div>

<hr>

	@lang('base.with-selected')
	<div class="form-group">
		{{ Form::select('bulkAction', $bulkActions, null, ['class' => 'form-control input-sm']) }}
	</div>
	{{ Form::submit(Lang::get('base.execute'), ['class' => 'btn btn-default btn-sm']) }}
{{ Form::close() }}

@stop