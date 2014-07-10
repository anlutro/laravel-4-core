<?php foreach ($alerts as $alert): ?>

<div class="alert alert-dismissable alert-<?= $alert->type ?>">
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<?= $alert->message ?>
</div>

<?php endforeach; ?>