@if ($errors->any())
	@foreach ($errors->all() as $error)
	<div class="alert alert-dismissable alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>{{ $error }}
	</div>
	@endforeach
@endif

@if (Session::has('warning'))
	<div class="alert alert-dismissable alert-warning">
		<button type="button" class="close" data-dismiss="alert">×</button>{{ Session::get('warning') }}
	</div>
@endif

@if (Session::has('info'))
	<div class="alert alert-dismissable alert-info">
		<button type="button" class="close" data-dismiss="alert">×</button>{{ Session::get('info') }}
	</div>
@endif

@if (Session::has('success'))
	<div class="alert alert-dismissable alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>{{ Session::get('success') }}
	</div>
@endif