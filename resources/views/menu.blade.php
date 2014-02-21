@if (Auth::check())

	{{ Menu::render('left') }}

	{{ Menu::render('right', ['class' => 'nav navbar-nav pull-right']) }}
	
@endif