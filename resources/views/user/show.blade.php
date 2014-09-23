@extends('c::layout.main')

@section('title', $user->name ?: $user->username)

@section('content')

<div class="page-header">
	<h1>
		{{{ $user->name ?: $user->username }}}
		@if ($canEdit && isset($editUrl))
			<small><a href="{{ $editUrl }}">@lang('c::std.edit')</a></small>
		@endif
	</h1>
</div>

<dl class="dl-horizontal">
	<dt>@lang('c::user.username-field')</dt>
	<dd>{{{ $user->username }}}</dd>

	<dt>@lang('c::user.name-field')</dt>
	<dd>{{{ $user->name }}}</dd>

	<dt>@lang('c::user.email-field')</dt>
	<dd>{{ $user->email ? HTML::mailto($user->email) : '' }}</dd>

	<dt>@lang('c::user.phone-field')</dt>
	<dd>{{ $user->phone ? HTML::obfuscate($user->phone) : '' }}</dd>

@if ($canEdit)
	<hr>

	<dt>@lang('c::std.created')</dt>
	<dd>{{ $user->created_at->format(Lang::get('c::std.datetime-format')) }}</dd>

	<dt>@lang('c::std.updated')</dt>
	<dd>{{ $user->updated_at ? $user->updated_at->format(Lang::get('c::std.datetime-format')) : '-' }}</dd>

	<dt>@lang('c::std.deleted')</dt>
	<dd>{{ $user->deleted_at ? $user->deleted_at->format(Lang::get('c::std.datetime-format')) : '-' }}</dd>

	<dt>@lang('c::user.last-login')</dt>
	<dd>{{ $user->last_login ? $user->last_login->format(Lang::get('c::std.datetime-format')) : '-' }}</dd>
@endif

</dl>

@stop
