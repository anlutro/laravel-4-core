<?php
$tryfiles = [
	__DIR__ . '/../../../../../vendor/autoload.php',
	__DIR__ . '/../../../../vendor/autoload.php',
	__DIR__ . '/../../../vendor/autoload.php',
	__DIR__ . '/../vendor/autoload.php',
];

foreach ($tryfiles as $file) {
	if (file_exists($file)) {
		require_once $file;
		// break;
	}
}

require_once __DIR__ . '/SQLiteTestCase.php';
require_once __DIR__ . '/AppTestCase.php';

date_default_timezone_set('UTC');
