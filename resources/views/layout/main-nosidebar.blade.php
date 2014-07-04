@extends('c::layout.main-generic')

@section('body')

<div class="container layout-no-sidebar">

	<header class="site-header">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				@include('c::menu')
			</div>
		</nav>
	</header>

	<main class="site-main">
		@include('c::alerts')
		@yield('content')
	</main>

	<footer class="site-footer">
		{{ Config::get('c::site.copyright-date') }} &copy; {{ Config::get('c::site.copyright-holder') }}<br>
		{{ Lang::get('c::site.made-by') }}
	</footer>

</div>

@stop
