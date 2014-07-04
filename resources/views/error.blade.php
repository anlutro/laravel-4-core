@extends('c::layout.main')

@section('title', $title)

@section('content')

<div class="jumbotron text-center">
	<h1>{{ $title }}</h1>

	@foreach ((array) $text as $paragraph)
	<p>{{ $paragraph }}</p>
	@endforeach

	<p>
	@unless (isset($hideBackUrl))
	<a href="javascript:history.back()" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-backward"></span> @lang('c::std.back')</a>
	@endunless

	@if (isset($homeUrl))
	<a href="{{ $homeUrl }}" class="btn btn-lg btn-default"><span class="glyphicon glyphicon-home"></span> @lang('c::std.home')</a>
	@endif
	</p>
</div>

@stop
