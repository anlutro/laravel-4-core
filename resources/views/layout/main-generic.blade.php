<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{{ $lang }}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{{ $lang }}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{{ $lang }}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{{ $lang }}"> <!--<![endif]-->
	<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@yield('title') | {{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

@foreach($styles as $style)
<link media="all" type="text/css" rel="stylesheet" href="{{ asset($style) }}">
@endforeach

@yield('head')

@foreach($headScripts as $script)
<script type="text/javascript" src="{{ asset($script) }}"></script>
@endforeach

@foreach ($conditionals as $condition => $scripts)
<!--[if {{ $condition }}]>
@foreach ($scripts as $script)
{{ $script }}
@endforeach
<![endif]-->
@endforeach
	</head>
	<body class="@yield('bodyclasses')">

		<a class="sr-only sr-only-focusable sr-skip-to-content" href="#site-main-content">@lang('c::std.skip-to-content')</a>

		@yield('body')

		@foreach($bodyScripts as $script)
		<script type="text/javascript" src="{{ asset($script) }}"></script>
		@endforeach

		@yield('scripts')

		@if ($gaCode)
		<script>
			(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
			function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
			e=o.createElement(i);r=o.getElementsByTagName(i)[0];
			e.src='//www.google-analytics.com/analytics.js';
			r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
			ga('create','{{ $gaCode }}');ga('send','pageview');
		</script>
		@endif
	</body>
</html>
