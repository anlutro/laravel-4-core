<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Html;

use Illuminate\Pagination\BootstrapPresenter;

class PaginationPresenter extends BootstrapPresenter
{
	public function getActivePageWrapper($text)
	{
		return '<li class="active"><span>'.$text.'</span><span class="sr-only"> ('
			.$this->translate('pagination.current', 'current').')</span></li>';
	}

	protected function translate($key, $default)
	{
		$translator = $this->paginator->getFactory()->getTranslator();

		return $translator->has($key) ? $translator->get($key) : $default;
	}
}
