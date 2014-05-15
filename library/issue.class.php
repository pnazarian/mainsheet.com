<?php

class Issue
{
	public $volumeID; // Integer
	public $issueNumber; // Integer
	public $date; // String
	public $isPublished; // Boolean
	
	public $folderSystem; // IssueFolderSystem
	
	public function __construct($volumeID, $issueNumber, $date = null, $articleFolderID = null, $pageFolderID = null, $imageFolderID = null)
	{
		$this->volumeID = $volumeID;
		$this->issueNumber = $issueNumber;
		$this->date = $date;
		$this->isPublished = $date !== null;
		
		if ($articleFolderID !== null && $pageFolderID !== null && $imageFolderID !== null)
			$this->folderSystem = new IssueFolderSystem($articleFolderID, $pageFolderID, $imageFolderID);
	}
}

?>