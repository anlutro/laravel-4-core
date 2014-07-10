<?php foreach ($alerts as $alert): ?>

<div class="alert alert-dismissable alert-<?php echo $alert->type ?>">
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $close ?></span></button>
	<?php echo $alert->message ?>
</div>

<?php endforeach; ?>