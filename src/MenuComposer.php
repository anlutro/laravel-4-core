<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c;

use anlutro\Menu\Builder;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\Translator;

/**
 * View composer for resources/views/menu.php
 */
class MenuComposer
{
	protected $menu;
	protected $auth;

	public function __construct(
		Builder $menu,
		AuthManager $auth,
		Config $config,
		UrlGenerator $url,
		Translator $translator
	) {
		$this->menu = $menu;
		$this->auth = $auth;
		$this->config = $config;
		$this->url = $url;
		$this->translator = $translator;
	}

	public function compose($view)
	{
		$user = $this->auth->user();

		$this->menu->createMenu('left')->addItem(
			$this->config->get('app.name'),
			$this->url->to('/')
		);

		$this->menu->createMenu('right', ['class' => 'nav navbar-nav pull-right']);

		if ($user !== null) {
			$subMenu = $this->menu->getMenu('right')->addSubmenu($user->name, ['id' => 'user']);
			$subMenu->addItem(
				$this->translator->get('c::user.profile-title'),
				$this->url->action('c\Controllers\UserController@profile'),
				['id' => 'profile']
			);

			if ($user->hasAccess('admin')) {
				$subMenu->addItem(
					$this->translator->get('c::user.admin-userlist'),
					$this->url->action('c\Controllers\UserController@index'),
					['id' => 'userlist']
				);
				$subMenu->addItem(
					$this->translator->get('c::user.admin-newuser'),
					$this->url->action('c\Controllers\UserController@create'),
					['id' => 'add-user']
				);
			}

			$subMenu->addItem(
				$this->translator->get('c::auth.logout'),
				$this->url->action('c\Controllers\AuthController@logout'),
				['id' => 'log-out']
			);
		}
	}
}
