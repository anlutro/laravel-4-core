@extends('c::layout.main')

@section('title', $pageTitle)

@section('content')

<div class="page-header">
	<h1>{{ $pageTitle }}</h1>
</div>

{{ Form::model($user, ['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<div class="form-group">
		{{ Form::label('username', Lang::get('c::user.username-field'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::text('username', null, ['class' => 'form-control']) }}
		</div>
	</div>

	@if ($userTypes)
	<div class="form-group">
		{{ Form::label('user_type', Lang::get('c::user.usertype-field'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::select('user_type', $userTypes, $user->user_type, ['class' => 'form-control']) }}
		</div>
	</div>
	@endif

	<div class="form-group">
		{{ Form::label('name', Lang::get('c::user.name-field'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::text('name', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('email', Lang::get('c::user.email-field'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::email('email', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('phone', Lang::get('c::user.phone-field'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::text('phone', null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
			<div class="checkbox">
				<label class="checkbox-inline">
					{{ Form::checkbox('is_active') }}
					@lang('c::user.active-field')
				</label>
				@if (isset($activate) && $activate)
				<label class="checkbox-inline">
					{{ Form::checkbox('send_activation') }}
					@lang('c::user.activation-field')
				</label>
				@endif
			</div>
		</div>
	</div>

	@if ($user->exists)
	<hr>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
			<p>@lang('c::user.updating-password-explanation')</p>
		</div>
	</div>
	@endif

	<div class="form-group">
		{{ Form::label('password', Lang::get('c::user.new-password'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::password('password', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('password_confirmation', Lang::get('c::auth.confirm-password'), ['class' => 'control-label col-sm-3 col-md-2']) }}
		<div class="col-sm-8 col-md-6 col-lg-5">
			{{ Form::password('password_confirmation', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
			<button type="submit" class="btn btn-primary">
				<span class="glyphicon glyphicon-save"></span>
				@lang('c::std.save')
			</button>
			<a href="{{ $backUrl }}" class="btn btn-default">
				<span class="glyphicon glyphicon-backward"></span>
				@lang('c::std.back')
			</a>
			@if (isset($deleteUrl))
			<button type="button" id="delete-user-button" class="btn btn-danger" data-delete-url="{{ $deleteUrl }}" data-confirm-msg="{{ Lang::get('c::user.delete-confirm') }}">
				<span class="glyphicon glyphicon-trash"></span>
				@lang('c::std.delete')
			</button>
			@endif
		</div>
	</div>

{{ Form::close() }}

@stop

@section('scripts')

<script>
$(function() {
	$('#delete-user-button').click(function() {
		var $this = $(this);
		if (!window.confirm($this.data('confirm-msg'))) {
			return;
		}

		var form = $this.closest('form');
		form.attr('action', $this.data('delete-url'));
		form.append('<input type="hidden" name="_method" value="delete">');
		form.submit();
	});
});
</script>

@stop
