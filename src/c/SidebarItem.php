<?php
namespace c;

class SidebarItem
{
	public $title;
	public $content;

	public function __construct($title = null, $content = null)
	{
		$this->title = $title;
		$this->content = $content;
	}
}
