<?php

spl_autoload_register(function ($class) {
	list($vendor, $library, $classPath) = \explode("\\", $class, 3);
	if (empty($classPath)) return;
	if (file_exists($file = join('/', array(__DIR__, $vendor, $library, 'src', strtr($classPath, '\\', '/'))).'.php')) {
		require_once $file;
	}
});