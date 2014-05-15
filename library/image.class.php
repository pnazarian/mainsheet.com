<?php

class Image extends ImageListing
{
	public $credit;
	public $caption;
	
	public function __construct($id, $credit, $caption)
	{
		parent::__construct($id);
		$this->credit = $credit;
		$this->caption = $caption;
	}
}

?>