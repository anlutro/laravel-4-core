@extends('layout.main')

@section('title', $title)

@section('content')

<div class="page-header">
	<h1>{{ $title }}</h1>
</div>

@foreach ((array) $text as $paragraph)
<p>{{ $paragraph }}</p>
@endforeach
<p>
<a href="javascript:history.back()">@lang('c::std.back')</a> &mdash; 
<a href="{{ $homeUrl }}">@lang('c::std.home')</a>
</p>

@stop