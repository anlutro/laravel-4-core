@extends('layout.main')

@section('title', $pageTitle)

@section('content')

<div class="page-header">
	<h1>{{ $pageTitle }}</h1>
</div>

{{ Form::model($user, ['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('username', Lang::get('base.username-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}
		</div>
	</div>

	@if ($userTypes)
	<div class="form-group">
		{{ Form::label('usertype', Lang::get('base.usertype-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::select('usertype', $userTypes, $user->usertype, ['class' => 'form-control']) }}
		</div>
	</div>
	@endif

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

	@if($user->exists)
	<hr>
	<div class="row">
		<div class="col-sm-10 col-sm-offset-2">
			<p>@lang('base.updating-password-explanation')</p>
		</div>
	</div>
	@endif

	<div class="form-group">
		{{ Form::label('password', Lang::get('base.password-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password_confirmation', Lang::get('base.confirm-password'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::password('password_confirmation', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-6">
			<button type="submit" class="btn btn-primary">@glyph('save') @lang('base.save')</button>
			<a href="{{ $backUrl }}" class="btn btn-default">@glyph('backward') @lang('base.back')</a>
			@if (isset($deleteUrl))
			<button type="button" id="delete" class="btn btn-danger" data-deleteurl="{{ $deleteUrl }}">@glyph('trash') @lang('base.delete')</button>
			@endif
		</div>
	</div>

{{ Form::close() }}

@stop