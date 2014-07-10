@extends('c::layout.main-generic')

@section('body')

<div class="layout-sidebar">

	<header class="site-header">
		<div class="container-fluid">
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				@include('c::menu')
			</nav>
		</div>
	</header>

	<main class="site-main">
		<div class="container-fluid" id="site-main-content">
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
			{{ is_array($footer) ? implode('<br>', $footer) : $footer }}
		</div>
</footer>

</div>

@stop
