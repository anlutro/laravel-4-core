<div class="sidebar">
@unless(empty($sidebar))

@foreach($sidebar as $item)

<div class="sidebar-item">

	@unless(empty($item->title))
	<div class="sidebar-item-title">
		<h3>{{ $item->title }}</h3>
	</div>
	@endif

	<div class="sidebar-item-content">
		{{ $item->content }}
	</div>

</div>

@endforeach

@endif
</div>