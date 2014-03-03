<?php foreach ($alerts as $alert): ?>

<div class="alert alert-dismissable alert-<?= $alert->type ?>">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<?= $alert->message ?>
</div>

<?php endforeach; ?>0