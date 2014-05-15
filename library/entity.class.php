<?php

//declare properties as protected, they will be treated as public
//if a property's default value is an object named 'Test' set the default value as the string: "Test()". It will be initialized at construction.

abstract class Entity
{	
	public function __set($name, $value)
	{		
		if (method_exists($this, 'set'.$name))
		{
			$this->{'set'.$name}($value);
		}
		else if (property_exists($this, $name))
		{
			$this->$name = $value;
		}
	}
	
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}
	}
	
	public function __construct()
	{
		foreach (get_object_vars($this) as $varName => $value)
		{
			if (is_string($value) && substr($value, -2) == "()")
			{
				$className = substr($value, 0, -2);
				$this->$varName = new $className();
			}
		}
	}
}

?>