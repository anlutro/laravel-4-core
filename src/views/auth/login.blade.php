@extends('layout.fullwidth')

@section('title', Lang::get('c::auth.login-title'))

@section('content')
<div class="page-header">
	<h1>
		@lang('c::auth.login-title')
		 - {{ Config::get('app.name') ?: Config::get('app.url') }}
	</h1>
</div>

{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group col-xs-12">
		{{ Form::label('username', Lang::get('c::user.username-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}		
		</div>
	</div>

	<div class="form-group col-xs-12">
		{{ Form::label('password', Lang::get('c::user.password-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group col-xs-12">
		<div class="col-sm-offset-2 col-sm-5">
			<button type="submit" class="btn btn-primary">
				<span class="glyphicon glyphicon-log-in"></span>
				@lang('c::auth.login-submit')
			</button>
			@if (isset($resetUrl))
			<a href="{{ $resetUrl }}" class="btn btn-default">
				<span class="glyphicon glyphicon-lock"></span>
				@lang('c::auth.resetpass-link')
			</a>
			@endif
		</div>
	</div>

{{ Form::close() }}
@stop