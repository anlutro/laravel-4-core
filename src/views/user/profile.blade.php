@extends('layout.main')

@section('title', Lang::get('base.profile-title'))

@section('content')
<div class="page-header">
	<h1>@lang('base.profile-title')</h1>
</div>

{{ Form::model(Auth::user(), ['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('username', Lang::get('base.username-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::text('username', null, ['class' => 'form-control', 'disabled' => 'disabled']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('name', Lang::get('base.name-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::text('name', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('email', Lang::get('base.email-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::email('email', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('phone', Lang::get('base.phone-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::text('phone', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('old_password', Lang::get('base.password-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('old_password', ['class' => 'form-control']) }}
		</div>
	</div>

	<hr>

	<div class="row">
		<div class="col-sm-10 col-sm-offset-2">
			<p>@lang('base.updating-password-explanation')</p>
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('new_password', Lang::get('base.new-password'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('new_password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('new_password_confirmation', Lang::get('base.confirm-password'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('new_password_confirmation', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-5">
			<button type="submit" class="btn btn-primary">@glyph('save') @lang('base.save')</button>
			<a href="{{ $backUrl }}" class="btn btn-default">@glyph('backward') @lang('base.back')</a>
		</div>
	</div>

{{ Form::close() }}
@stop