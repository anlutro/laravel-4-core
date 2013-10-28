@extends('layout.fullwidth')

@section('title', Lang::get('base.reminder-title'))

@section('content')
<div class="page-header">
	<h1>@lang('base.resetpass-title')</h1>
</div>

{{ Form::open(['url' => $formAction, 'class' => 'form-horizontal', 'role' => 'form']) }}

	<p>@lang('base.resetpass-instructions')</p>

	<div class="form-group">
		{{ Form::label('email', Lang::get('base.email-field'), ['class' => 'control-label col-sm-2']) }}
		<div class="col-sm-5">
			{{ Form::email('email', null, ['class' => 'form-control']) }}		
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-2">
			<button type="submit" class="btn btn-primary">@glyph('send') @lang('base.resetpass-send')</button>
		</div>
	</div>

{{ Form::close() }}
@stop