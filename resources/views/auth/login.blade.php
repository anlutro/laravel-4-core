@extends('layout.main')

@section('title', Lang::get('c::auth.login-title'))

@section('content')

<div class="panel panel-default login-panel">
	<div class="panel-heading">
		<h3 class="panel-title">{{ Lang::get('c::auth.login-title') }}</h3>
	</div>
	<div class="panel-body">
		{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

			<div class="form-group">
				{{ Form::label('username', Lang::get('c::user.username-field'), ['class' => 'control-label col-sm-3']) }}
				<div class="col-sm-9">
					{{ Form::text('username', null, ['class' => 'form-control']) }}		
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('password', Lang::get('c::user.password-field'), ['class' => 'control-label col-sm-3']) }}
				<div class="col-sm-9">
					{{ Form::password('password', ['class' => 'form-control']) }}
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
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
	</div>
</div>

@stop