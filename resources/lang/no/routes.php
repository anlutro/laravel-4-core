<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return [
	'login' => '/bruker/logg-inn',
	'login_post' => '/bruker/logg-inn',
	'profile' => '/bruker/profil',
	'profile_post' => '/bruker/profil',
	'logout' => '/bruker/logg-ut',
	'user.show' => '/bruker/{id}/profil',
	'user.index' => '/admin/brukere',
	'user.bulk' => '/admin/brukere',
	'user.create' => '/admin/brukere/ny',
	'user.store' => '/admin/brukere/ny',
	'user.edit' => '/admin/brukere/{id}',
	'user.update' => '/admin/brukere/{id}',
	'user.delete' => '/admin/brukere/{id}',
	'user.restore' => '/admin/brukere/{id}/gjenopprett',
	'user.switch' => '/admin/brukere/{id}/bytt-til',
	'pwreset.request' => '/passord/glemt',
	'pwreset.request_post' => '/passord/glemt',
	'pwreset.reset' => '/passord/tilbakestill',
	'pwreset.reset_post' => '/passord/tilbakestill',
	'activation.register' => '/bruker/registrer',
	'activation.register_post' => '/bruker/registrer',
	'activation.activate' => '/bruker/aktiver',
	'support' => '/support',
	'support_post' => '/support',
];
