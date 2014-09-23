<div class="navbar-header">
	<?php if (!empty($menus)): ?>
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
		<span class="sr-only"><?= trans('c::std.toggle-menu') ?></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<?php endif; ?>

	<?php if (isset($homeUrl)): ?>
	<a href="<?= $homeUrl ?>" class="navbar-brand"><?= $siteName ?></a>
	<?php elseif (isset($siteName)): ?>
	<span class="navbar-brand"><?= $siteName ?></span>
	<?php endif; ?>
</div>

<div class="collapse navbar-collapse" id="navbar-collapse">
	<?php foreach ($menus as $menu): ?>
		<?= $menu->render() ?>
	<?php endforeach; ?>
</div>
