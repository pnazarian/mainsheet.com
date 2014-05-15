<?php

// Entity class to hold information on the abridged article listings.

class MiniArticleListing
{
	public $id;       			    //Integer
	public $title; 					//String
	public $date;  					//String
	public $authors;				//String[]
	public $section;				//String
	public $isOnlineOnly;			//Boolean
	public $volumeID = 0;			//Integer
	public $issueNumber = 0;		//Integer
	public $promotion = 0;          //Integer
	
	public function __construct($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID = 0, $issueNumber = 0, $promotion = 0)
	{
		$this->id = $articleID;
		$this->title = $title;
		$this->date = $date;
		$this->authors = $authors;
		$this->section = $section;
		$this->isOnlineOnly = $isOnlineOnly;
		$this->volumeID = $volumeID;
		$this->issueNumber = $issueNumber;
		$this->promotion = $promotion;
	}
}

?>