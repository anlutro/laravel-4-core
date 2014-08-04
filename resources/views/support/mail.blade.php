@extends('c::layout.email')

@section('title', $title)

@section('content')

<p>
	{{ trans('c::support.subject-label') }}: {{{ $subject }}}<br>
	{{ trans('c::support.email-label') }}: <a href="mailto:{{ $email }}">{{{ $email }}}</a><br>
	{{ trans('c::support.phone-label') }}: {{{ $phone }}}<br>
	{{ ucfirst(trans('c::user.model-user')) }}: <a href="{{ route('c::user.edit', [$user->id]) }}">{{{ $user->username }}}</a>
</p>
<p>
	{{ nl2br(e($body)) }}
</p>

@stop
