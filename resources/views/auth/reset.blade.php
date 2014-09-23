@extends('c::layout.main')

@section('title', Lang::get('c::auth.resetpass-title'))

@section('content')

<div class="page-header">
	<h1 class="col-sm-offset-3">@lang('c::auth.resetpass-title')</h1>
</div>

{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('username', Lang::get('c::user.username-field'), ['class' => 'control-label col-sm-3']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password', Lang::get('c::user.password-field'), ['class' => 'control-label col-sm-3']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password_confirmation', Lang::get('c::auth.confirm-password'), ['class' => 'control-label col-sm-3']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::password('password_confirmation', ['class' => 'form-control']) }}
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-10">
			<button type="submit" class="btn btn-primary">
				<span class="glyphicon glyphicon-save"></span>
				@lang('c::auth.resetpass-title')
			</button>
		</div>
	</div>

{{ Form::hidden('token', $token) }}
{{ Form::close() }}

@stop
