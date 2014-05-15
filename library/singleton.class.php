<?php

//Singleton class that allows for static calls to protected instance methods, but not variables.

abstract class Singleton
{
	private static $instances;

	public static function __callStatic($methodName, $arguments)
	{
		$className = get_called_class();

		if (!isset(self::$instances[$className]))
		{
			self::$instances[$className] = new $className();
		}
		
		if (method_exists(self::$instances[$className], $methodName))
		{
			return call_user_func_array(array(self::$instances[$className], $methodName), $arguments);
		}
	}	
}

/*

USAGE HELP:

 - declare public methods as protected
 
 - instance variables will be protected, even if declared public
 
 - declare __construct as protected

*/

?>