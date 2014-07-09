<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Composers;

use anlutro\Menu\Builder;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Routing\UrlGenerator;
use Illuminate\View\View;

class MenuViewCreator
{
	/**
	 * @var Builder
	 */
	protected $menu;

	/**
	 * @var AuthManager|\Illuminate\Auth\Guard
	 */
	protected $auth;

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	/**
	 * @var Repository
	 */
	protected $config;

	public function __construct(
		Builder $menu,
		AuthManager $auth,
		UrlGenerator $url,
		Repository $config
	) {
		$this->menu = $menu;
		$this->auth = $auth;
		$this->url = $url;
		$this->config = $config;
	}

	public function create(View $view)
	{
		$view->menus = $this->getMenus();
		$view->homeUrl = $this->getHomeUrl();
		$view->siteName = $this->getSiteName();
	}

	protected function getMenus()
	{
		return [$this->createMenu('left'), $this->createMenu('right')];
	}

	protected function createMenu($location)
	{
		return $this->menu->hasMenu($location)
			? $this->menu->getMenu($location)
			: $this->menu->createMenu($location, ['class' => 'nav navbar-nav navbar-'.$location]);
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
		return $this->config->get('c::site.html-name') ?:
			($this->config->get('c::site.name') ?:
			$this->config->get('app.url'));
	}
}
