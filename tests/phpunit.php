<?php
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

foreach (glob(__DIR__.'/../resources/migrations/*.php') as $file) {
	require_once $file;
}

date_default_timezone_set('UTC');

Carbon\Carbon::setTestNow(Carbon\Carbon::now());
