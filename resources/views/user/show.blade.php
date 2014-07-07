@extends('c::layout.main')

@section('title', $user->name ?: $user->username)

@section('content')

<div class="page-header">
	<h1>{{{ $user->name ?: $user->username }}}</h1>
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
</dl>

@stop