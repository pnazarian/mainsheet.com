<?php

//simply include this file in each root file and it will autoload to the specified path

function __autoload($className)
{
	$paths = array(__DIR__.'/'.$className.'.class.php',
					__DIR__.'/google-api-php-client/src/'.$className.'.php',
					__DIR__.'/google-api-php-client/src/contrib/'.$className.'.php');

	// update this path based on the location of the library relative to this file
	foreach ($paths as $path)
		if (file_exists($path))
		{
			require $path;
			return;
		}
}

?>