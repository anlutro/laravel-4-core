@extends('c::layout.email')

@section('title', Lang::get('c::auth.activate-title'))

@section('content')

<h2>@lang('c::auth.activate-title')</h2>

<p>@lang('c::auth.activate-text', ['sitename' => (Config::get('app.name') ?: Config::get('app.url'))])</p>
<p>{{ URL::action($action, ['activation_code' => $code]) }}</p>

@stop