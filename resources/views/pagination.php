<?php if ($paginator->getLastPage() > 1): ?>
	<ul class="pagination">
			<?php echo (new anlutro\Core\Html\PaginationPresenter($paginator))->render(); ?>
	</ul>
<?php endif; ?>
