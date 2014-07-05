@extends('c::layout.email')

@section('title', Lang::get('c::auth.resetpass-title'))

@section('content')

<h2>@lang('c::auth.resetpass-title')</h2>

<p>@lang('c::auth.resetpass-text', ['sitename' => (Config::get('site.name') ?: Config::get('app.url'))])</p>
<p>{{ URL::action($action, ['token' => $token]) }}</p>

@stop