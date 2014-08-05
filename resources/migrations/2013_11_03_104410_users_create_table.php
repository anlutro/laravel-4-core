<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UsersCreateTable extends Migration
{
	public function up()
	{
		Schema::create('users', function($t) {
			$t->increments('id');
			$t->string('username', 32);
			$t->string('password', 512);
			$t->string('name', 128);
			$t->string('email', 128);
			$t->string('phone', 16)
				->default('');
			$t->tinyInteger('user_level')
				->unsigned();
			$t->boolean('is_active')
				->default(false);
			$t->string('login_token', 32)
				->nullable();
			$t->string('remember_token', 128)
				->nullable();
			$t->timestamp('last_login')
				->nullable();
			$t->softDeletes();
			$t->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('users');
	}
}
