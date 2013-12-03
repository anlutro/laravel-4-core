<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
	public function up()
	{
		Schema::create('users', function($t) {
			$t->increments('id');
			$t->string('username', 32);
			$t->string('password', 512);
			$t->string('name', 128);
			$t->string('email', 128);
			$t->string('phone', 16);
			$t->tinyInteger('user_level')
				->unsigned();
			$t->boolean('is_active')
				->default(false);
			$t->string('login_token', 32)
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
