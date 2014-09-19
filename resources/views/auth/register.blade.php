@extends('c::layout.main')

@section('title', Lang::get('c::auth.register-title'))

@section('content')

<div class="page-header">
	<h1 class="col-sm-offset-2">@lang('c::auth.register-title')</h1>
</div>

{{ Form::model($user, ['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('username', Lang::get('c::user.username-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('name', Lang::get('c::user.name-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::text('name', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('email', Lang::get('c::user.email-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::email('email', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('phone', Lang::get('c::user.phone-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::text('phone', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password', Lang::get('c::user.password-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password_confirmation', Lang::get('c::auth.confirm-password'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-10 col-md-7 col-lg-5">
			{{ Form::password('password_confirmation', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary">
				<span class="glyphicon glyphicon-save"></span>
				@lang('c::auth.register-title')
			</button>
		</div>
	</div>

{{ Form::close() }}

@stop