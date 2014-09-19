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
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Routing\UrlGenerator;
use Illuminate\View\View;

class MenuViewCreator
{
	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @var Builder
	 */
	protected $menu;

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	/**
	 * @var Repository
	 */
	protected $config;

	public function __construct(
		Application $app,
		Builder $menu,
		UrlGenerator $url,
		Repository $config
	) {
		$this->app = $app;
		$this->menu = $menu;
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
		// wrap in value() to allow closures
		$enableHomeLink = value($this->config->get('c::enable-home-link', false));

		if ($enableHomeLink) {
			$url = $this->config->get('c::redirect-login', '/');
			return $this->url->to($url);
		}

		return null;
	}

	protected function getSiteName()
	{
		$title = $this->config->get('c::site.html-name') ?:
			($this->config->get('c::site.name') ?:
			$this->config->get('app.url'));

		$env = $this->app->environment();

		if ($env !== 'production') {
			$title .= ' <strong>'.strtoupper($env).'</strong>';
		}

		return $title;
	}
}
