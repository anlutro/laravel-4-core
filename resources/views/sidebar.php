<?php if (!empty($sidebar)): ?>

<div class="sidebar">

<?php foreach ($sidebar as $item): ?>

<div class="sidebar-item">

	<?php if (!empty($item->title)): ?>
	<div class="sidebar-item-title">
		<h3><?= $item->title ?></h3>
	</div>
	<?php endif; ?>

	<div class="sidebar-item-content">
		<?= $item->content ?>
	</div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>
