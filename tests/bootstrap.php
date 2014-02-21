<?php
require_once __DIR__ . '/../vendor/autoload.php';

$tryfiles = [
	__DIR__ . '/../../../vendor/autoload.php',
	__DIR__ . '/../../../../vendor/autoload.php',
	__DIR__ . '/../../../../../vendor/autoload.php',
];

foreach ($tryfiles as $file) {
	if (file_exists($file)) {
		require_once $file; break;
	}
}

require_once __DIR__ . '/SQLiteTestCase.php';
require_once __DIR__ . '/AppTestCase.php';

foreach (glob(__DIR__.'/../resources/migrations/*.php') as $file) {
	require_once $file;
}

date_default_timezone_set('UTC');
