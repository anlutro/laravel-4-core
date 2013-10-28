@extends('layout.fullwidth')

@section('title', Lang::get('base.reminder-title'))

@section('content')
{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('username', Lang::get('base.username-field'), ['class' => 'control-label col-sm-3']) }}
		<div class="col-sm-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password', Lang::get('base.password-field'), ['class' => 'control-label col-sm-3']) }}
		<div class="col-sm-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password_confirmation', Lang::get('base.password-confirm'), ['class' => 'control-label col-sm-3']) }}
		<div class="col-sm-5">
			{{ Form::password('password_confirmation', ['class' => 'form-control']) }}
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<button type="submit" class="btn btn-primary">@glyph('save') @lang('base.save')</button>
		</div>
	</div>

{{ Form::hidden('token', $token) }}
{{ Form::close() }}
@stop