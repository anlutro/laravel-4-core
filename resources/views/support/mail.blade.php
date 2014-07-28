@extends('c::layout.email')

@section('title', $title)

@section('content')

<p>
	{{ trans('c::support.subject-label') }}: {{{ $subject }}}
	{{ trans('c::support.email-label') }}: <a href="mailto:{{ $email }}">{{{ $email }}}</a>
	{{ trans('c::support.phone-label') }}: {{{ $phone }}}
	{{ ucfirst(trans('c::user.model-user')) }}: <a href="{{ route('c::user.edit', [$user->id]) }}">{{{ $user->username }}}</a>
</p>
<p>
	{{ nl2br(e($body)) }}
</p>

@stop
