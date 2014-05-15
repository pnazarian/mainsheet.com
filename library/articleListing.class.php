<?php

// abstract Entity class to hold a full article

class ArticleListing extends MiniArticleListing
{
	public $blurb; // String
	public $image; // ImageListing;
	public $sectionID; // Integer
	
	public function __construct($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $text, $image, $promotion = 0, $sectionID)
	{
		parent::__construct($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $promotion);
		
		$firstPeriod = 1+strpos($text, '.');
		if ($firstPeriod<=200)
		{
			$this->blurb = substr($text, 0, $firstPeriod);
		} else {
			$this->blurb = substr($text, 0, strrpos($text, ' ', 200-strlen($text))).'...';
		}

		$this->image = $image;
		$this->sectionID = $sectionID;
	}
}

?>