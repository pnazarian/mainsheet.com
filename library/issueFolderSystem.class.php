<?php

class IssueFolderSystem
{
	public $articlesFolderID;
	public $pagesFolderID;
	public $imagesFolderID;
	
	public function __construct($articlesFolderID, $pagesFolderID, $imagesFolderID)
	{
		$this->articlesFolderID = $articlesFolderID;
		$this->pagesFolderID = $pagesFolderID;
		$this->imagesFolderID = $imagesFolderID;
	}
}

?>