@extends('c::layout.main-generic')

@section('body')

<header class="site-header">
	<div class="container-fluid">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			@include('c::menu')
		</nav>
	</div>
</header>

<main class="site-main">
	<div class="container-fluid">
		@include('c::alerts')
		@yield('content')
	</div>
</main>

<aside class="site-sidebar">
	<nav class="sidebar sidebar-default">
		{{ $sidebar->render() }}
	</nav>
</aside>

<footer class="site-footer">
	<div class="container-fluid">
		{{ Config::get('c::site.copyright-date') }} &copy; {{ Config::get('c::site.copyright-holder') }}<br>
		{{ Lang::get('c::site.made-by') }}
	</div>
</footer>

@stop
