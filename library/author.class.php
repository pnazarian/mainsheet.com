<?php

class Author
{
	public $id;
	public $name;
	public $displayName;
	
	public function __construct($id, $name = "", $displayName = "")
	{
		$this->id = $id;
		$this->name = $name;
		$this->displayName = $displayName;
	}
}

?>