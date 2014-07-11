@extends('c::layout.main-generic')

@section('body')

<header class="site-header" id="site-header">
	<nav class="navbar navbar-inverse navbar-static-top" id="navbar" role="navigation">
		<div class="container">
			@include('c::menu')
		</div>
	</nav>
</header>

<div class="container layout-no-sidebar layout-static-navbar">

	<main class="site-main" id="site-main-content">
		@include('c::alerts')
		@yield('content')
	</main>

	<footer class="site-footer">
		{{ is_array($footer) ? implode('<br>', $footer) : $footer }}
	</footer>

</div>

@stop
