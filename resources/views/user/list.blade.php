@extends('c::layout.main')

@section('title', Lang::get('c::user.admin-userlist'))

@section('content')

{{ Form::open(['method' => 'get', 'class' => 'form-inline', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('search', Lang::get('c::std.search'), ['class' => 'sr-only']) }}
		{{ Form::text('search', Input::get('search'), ['class' => 'form-control input-sm', 'placeholder' => Lang::get('c::std.search')]) }}
	</div>
	<div class="form-group">
		{{ Form::select('usertype', $userTypes, Input::get('usertype'), ['class' => 'form-control input-sm']) }}
	</div>
	<button class="btn btn-default btn-sm"><span class="glyphicon glyphicon-search"></span> @lang('c::std.search')</button>

	<span class="pull-right">
		<a href="{{ $backUrl }}" class="btn btn-default btn-sm">
			<span class="glyphicon glyphicon-backward"></span>
			@lang('c::std.back')
		</a>
		<a href="{{ $newUrl }}" class="btn btn-default btn-sm">
			<span class="glyphicon glyphicon-plus"></span>
			@lang('c::std.new')
		</a> 
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
				<th>@lang('c::user.name-field')</th>
				<th>@lang('c::user.email-field')</th>
				<th>@lang('c::user.phone-field')</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@foreach ($users as $user)
			<tr>
				<td>
					<input type="checkbox" name="bulk[{{ $user->id }}]">
				</td>
				<td>{{{ $user->id }}}</td>
				<td>
					{{{ $user->name }}}
					@if ($user->user_type !== 'user')
					<span class="label label-info">@lang('c::user.usertype-'.$user->user_type)</span>
					@endif
					@if (!$user->is_active)
					<span class="label label-default">@lang('c::user.user-inactive')</span>
					@endif
					@if ($user->deleted_at)
					<span class="label label-danger">@lang('c::user.user-deleted')</span>
					@endif
				</td>
				<td>{{{ $user->email }}}</td>
				<td>{{{ $user->phone }}}</td>
				<td class="text-right">
					<a href="{{ URL::action($showAction, [$user->id]) }}" class="btn btn-xs btn-default">
						<span class="glyphicon glyphicon-eye-open"></span>
						@lang('c::std.view')
					</a>
					<a href="{{ URL::action($editAction, [$user->id]) }}" class="btn btn-xs btn-primary">
						<span class="glyphicon glyphicon-pencil"></span>
						@lang('c::std.edit')
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

	<hr>

	@lang('c::std.with-selected')
	<div class="form-group">
		{{ Form::select('bulkAction', $bulkActions, null, ['class' => 'form-control input-sm']) }}
	</div>
	{{ Form::submit(Lang::get('c::std.execute'), ['class' => 'btn btn-default btn-sm']) }}

{{ Form::close() }}

@stop
