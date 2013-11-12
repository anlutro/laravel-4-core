@extends('layout.email')

@section('title', Lang::get('c::auth.reminder-title'))

@section('content')

<h2>@lang('c::auth.reminder-title')</h2>

<p>@lang('c::auth.reminder-text', ['sitename' => (Config::get('app.name') ?: Config::get('app.url'))])</p>
<p>{{ URL::action('AuthController@reset', ['token' => $token]) }}</p>

@stop