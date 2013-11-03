<?php

use Illuminate\Database\Migration;
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
			$t->string('user_type', 16);
			$t->boolean('is_active')
				->default(false);
			$t->string('activation_code', 64)
				->nullable();
			$t->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('users');
	}
}
