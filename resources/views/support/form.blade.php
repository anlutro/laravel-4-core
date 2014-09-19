@extends('c::layout.main')

@section('title', trans('c::support.title'))

@section('content')

<div class="page-header">
	<h1 class="col-sm-offset-2">@lang('c::support.title')</h1>
</div>

{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

<div class="form-group">
	{{ Form::label('subject', trans('c::support.subject-label'), ['class' => 'control-label col-sm-2']) }}
	<div class="col-sm-10 col-md-8 col-lg-6">
		{{ Form::text('subject', null, ['class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	{{ Form::label('email', trans('c::support.email-label'), ['class' => 'control-label col-sm-2']) }}
	<div class="col-sm-10 col-md-8 col-lg-6">
		{{ Form::text('email', null, ['class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	{{ Form::label('phone', trans('c::support.phone-label'), ['class' => 'control-label col-sm-2']) }}
	<div class="col-sm-10 col-md-8 col-lg-6">
		{{ Form::text('phone', null, ['class' => 'form-control']) }}
		<p class="help-block">@lang('c::support.contact-info')</p>
	</div>
</div>

<div class="form-group">
	{{ Form::label('body', trans('c::support.body-label'), ['class' => 'control-label col-sm-2']) }}
	<div class="col-sm-10 col-md-8 col-lg-6">
		{{ Form::textarea('body', null, ['class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		<button type="submit" class="btn btn-primary">
			<span class="glyphicon glyphicon-save"></span> @lang('c::support.submit')
		</button>
	</div>
</div>

{{ Form::close() }}

@stop
