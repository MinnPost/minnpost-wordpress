<?php

if (!function_exists('_bwp_framework_autoloader')) {
	function _bwp_framework_autoloader($class_name)
	{
		$class_maps = include dirname(__FILE__) . '/vendor/composer/autoload_classmap.php';

		// do not load non-BWP classes
		if (stripos($class_name, 'BWP') === false) {
			return;
		}

		if (array_key_exists($class_name, $class_maps)) {
			require $class_maps[$class_name];
		}
	}

	spl_autoload_register('_bwp_framework_autoloader');
}
