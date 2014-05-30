<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core;

use anlutro\Menu\Builder;
use Illuminate\View\View;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Routing\UrlGenerator;

class MenuViewComposer
{
	public function __construct(
		Builder $builder,
		AuthManager $auth,
		UrlGenerator $url,
		Repository $config
	) {
		$this->builder = $builder;
		$this->auth = $auth;
		$this->url = $url;
		$this->config = $config;
	}

	public function compose(View $view)
	{
		$view->menus = $this->getMenus();
		$view->homeUrl = $this->getHomeUrl();
		$view->siteName = $this->getSiteName();
	}

	protected function getMenus()
	{
		$menus = [];

		foreach (['left', 'right'] as $key) {
			$menu = $this->builder->getMenu($key);
			if (!$menu->isEmpty()) {
				$menus[] = $menu;
			}
		}

		return $menus;
	}

	protected function getHomeUrl()
	{
		if ($this->auth->check()) {
			$url = $this->config->get('c::redirect-login', '/');
			return $this->url->to($url);
		}

		return null;
	}

	protected function getSiteName()
	{
		return $this->config->get('site.html-name') ?:
			($this->config->get('site.name') ?:
			$this->config->get('app.url'));
	}
}
