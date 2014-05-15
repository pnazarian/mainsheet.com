<?php

class ImageListing
{
	public $path;
	
	public function __construct($id)
	{
		$this->path = 'images/userImages/'.$id.'.jpg';
	}
}

?>