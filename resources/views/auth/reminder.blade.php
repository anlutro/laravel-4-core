@extends('c::layout.main')

@section('title', Lang::get('c::auth.resetpass-title'))

@section('content')

<div class="page-header">
	<h1>@lang('c::auth.resetpass-title')</h1>
</div>

{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<p>@lang('c::auth.resetpass-instructions')</p>

	<div class="form-group">
		{{ Form::label('email', Lang::get('c::user.email-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::email('email', null, ['class' => 'form-control']) }}		
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-2">
			<button type="submit" class="btn btn-primary">
				<span class="glyphicon glyphicon-send"></span>
				@lang('c::auth.resetpass-send')
			</button>
		</div>
	</div>

{{ Form::close() }}

@stop