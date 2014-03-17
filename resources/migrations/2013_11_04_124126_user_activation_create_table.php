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

class UserActivationCreateTable extends Migration
{
	public function up()
	{
		Schema::create('user_activation', function($t) {
			$t->increments('id');
			$t->string('code', 128);
			$t->string('email', 128);
			$t->timestamp('expires');
		});
	}

	public function down()
	{
		Schema::drop('user_activation');
	}
}
