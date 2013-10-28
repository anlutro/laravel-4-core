@extends('layout.fullwidth')

@section('title', Lang::get('base.login-title'))

@section('content')
<div class="page-header">
	<h1>
		@lang('base.login-title')
		 - {{ Config::get('app.name') }}
	</h1>
</div>

{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group col-xs-12">
		{{ Form::label('username', Lang::get('base.username-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}		
		</div>
	</div>

	<div class="form-group col-xs-12">
		{{ Form::label('password', Lang::get('base.password-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group col-xs-12">
		<div class="col-sm-offset-2 col-sm-5">
			<button type="submit" class="btn btn-primary">
				@glyph('log-in') @lang('base.login-submit')
			</button>
			<a href="{{ $resetUrl }}" class="btn btn-default">
				@glyph('lock') @lang('base.resetpass-link')
			</a>
		</div>
	</div>

{{ Form::close() }}
@stop