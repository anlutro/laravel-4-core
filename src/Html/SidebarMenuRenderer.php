<?php
namespace anlutro\Core\Html;

use anlutro\Menu\Collection;
use anlutro\Menu\Nodes\SubmenuNode;
use anlutro\Menu\Nodes\NodeInterface;
use anlutro\Menu\Renderers\ListRenderer;

class SidebarMenuRenderer extends ListRenderer
{
	public function getMenuAttributes(Collection $menu)
	{
		return $this->mergeAttributes(parent::getMenuAttributes($menu),
			['class' => 'nav navbar-nav']);
	}

	public function getSubmenuAttributes(Collection $menu)
	{
		return $this->mergeAttributes(parent::getSubmenuAttributes($menu),
			['class' => 'slidedown-menu']);
	}

	public function getSubmenuAnchorAttributes(SubmenuNode $item)
	{
		return $this->mergeAttributes(parent::getSubmenuAnchorAttributes($item),
			['class' => 'slidedown-toggle', 'data-toggle' => 'slidedown']);
	}

	public function getDividerAttributes()
	{
		return ['class' => 'divider'];
	}

	public function getSubmenuAffix()
	{
		return ' <b class="caret"></b>';
	}

	public function getSubmenuItemAttributes(SubmenuNode $item)
	{
		return ['class' => 'slidedown'];
	}
}
