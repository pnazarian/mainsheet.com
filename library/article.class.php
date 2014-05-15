<?php

class Article extends MiniArticleListing
{
	public $text;			//String
	public $images;			//Image[]
	public $hits;			//Integer
	public $keywords;		//String[]
	public $relatedArticles; //MiniArticleListing[]
	public $pageID;         //Integer
	
	public function __construct($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $text, $images, $hits, $keywords, $relatedArticles, $pageID, $promotion = 0)
	{
		parent::__construct($articleID, $title, $date, $authors, $section, $isOnlineOnly, $volumeID, $issueNumber, $promotion);
		$this->text = $text;
		$this->images = $images;
		$this->hits = $hits;
		$this->keywords = $keywords;
		$this->relatedArticles = $relatedArticles;
		$this->pageID = $pageID;
	}
}

?>