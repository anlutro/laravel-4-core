<?php
namespace c\Auth\Activation;

use Illuminate\Support\Facades\Facade;

/**
 * @see  c\Auth\Activation\ActivationBroker
 */
class Activation extends Facade
{
	public static function getFacadeAccessor()
	{
		return 'auth.activation';
	}
}
