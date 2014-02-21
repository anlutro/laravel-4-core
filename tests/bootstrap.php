<?php
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/AppTestCase.php';

foreach (glob(__DIR__.'/../resources/migrations/*.php') as $file) {
	require_once $file;
}

date_default_timezone_set('UTC');
